<?php

namespace App\Utils;

class Qiniu
{

    private $auth;
    private $bucket;

    public function __construct($accessKey = null, $secretKey = null)
    {
        if (!$accessKey) {
            $accessKey = getenv('QINIU_ACCESS_KEY');
        }
        if (!$secretKey) {
            $secretKey = getenv('QINIU_SECRET_KEY');
        }
        $this->auth = $this->getAuth($accessKey, $secretKey);
    }

    private function getAuth($accessKey, $secretKey)
    {
        return new \Qiniu\Auth($accessKey, $secretKey);
    }

    public function setBucket($bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * 判断文件是否在bucket中
     * @param $key
     * @param null $bucket
     * @return bool
     */
    public function isFileExist($key, $bucket = null)
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($this->auth, $config);
        list($fileInfo, $err) = $bucketManager->stat($bucket, $key);
        if ($err) {
            showLog("检查 $key 是否在: $bucket ?", redColorText('NO'), "ErrCode:", $err->code(), "ErrMsg:", $err->message());
            return false;
        }
//        Array
//        (
//            [fsize] => 60164396
//            [hash] => liXKlTuQADeq8HN0MP8j1akFnLu6
//            [md5] => a034f2af7fb0462e28c06ca60d7b85ca
//            [mimeType] => application/x-compressed
//            [putTime] => 15814118086371303
//            [type] => 0
//        )
        showLog("检查 $key 是否在: $bucket ?", greenColorText('YES'));
        return $fileInfo;
    }

    /**
     * 上传文件到bucket
     * @param $filePath
     * @param $key
     * @param null $bucket
     * @return bool
     * @throws \Exception
     */
    public function upload($filePath, $key, $bucket = null)
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        // 生成上传 Token
        $token = $this->auth->uploadToken($bucket);
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            print_r($err);
            return false;
        } else {
            print_r($ret);
            return true;
        }
    }

    /**
     * 上传指定文件夹的文件,可以使用$pattern指定某种格式的文件,默认就是文件夹中的所有文件,包含子目录
     * 只要有一个文件上传失败,就会立即终止,并返回 false
     * @param $folder 文件夹
     * @param string $pattern 文件匹配 如 *.txt 匹配所有txt后缀的文件
     * @param null $keyCallback 上传的key(文件名)回调函数,用于对文件名进行处理,默认就是文件的全路径
     * @param null $bucket 空间名
     * @return bool
     * @throws \Exception
     */
    public function uploadFolder($folder, $pattern = '*', $keyCallback = null, $bucket = null)
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        // 生成上传 Token
        $token = $this->auth->uploadToken($bucket);
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new \Qiniu\Storage\UploadManager();

        $err = false;

        $files = Util::searchDir($folder, $pattern);

        if ($files) {
            foreach ($files as $filePath) {
                $key = is_callable($keyCallback) ? call_user_func($keyCallback, $filePath) : $filePath;
                list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
                if ($err !== null) {
                    showLog("upload $filePath to bucket: $bucket  key: $key", redFail(), "ErrCode:", $err->code(), "ErrMsg:", $err->message());
                    break;
                }
                showLog("upload $filePath to bucket: $bucket  key: $key", greenSucc());
            }
        }

        if ($err) {
            return false;
        }

        return true;

    }

    /**
     * 从远程抓取并上传到bucket
     * @param $url 远程链接
     * @param $key 保存的bucket上的名称
     * @param null $bucket bucket名称
     * @return bool|mixed
     */
    public function fetch($url, $key, $bucket = null)
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        $bucketManager = new \Qiniu\Storage\BucketManager($this->auth);
        // 指定抓取的文件保存名称
        list($ret, $err) = $bucketManager->fetch($url, $bucket, $key);
        if ($err !== null) {
            showLog("fetch $url to bucket: $bucket  key: $key", redFail(), "ErrCode:", $err->code(), "ErrMsg:", $err->message());
            return false;
        }

//            Array
//            (
//                [fsize] => 81213024
//                [hash] => lqQerqf5fPcV-O-AT2EewC7QvnuC
//                [key] => app/postman/7.16.1/Postman-win64-7.16.1-Setup.exe
//                [mimeType] => application/octet-stream
//            )
        showLog("fetch $url to bucket: $bucket  key: $key", greenSucc());
        return $ret;
    }

    /**
     * 获取标准存储的当前存储量。监控统计可能会延迟 1 天。
     * https://developer.qiniu.com/kodo/api/3908/statistic-space
     * @param null $bucket
     * @return int 单位: 字节
     */
    public function getBucketSpace($bucket = null): int
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        $params = [
            'bucket' => $bucket,
            'begin' => strtotime("-1 day"),
            'end' => date('YmdHis'),
            'g' => 'day',
        ];
        $url = '/v6/space?' . http_build_query($params);

        $Authorization = $this->auth->authorization($url);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "api.qiniu.com" . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: " . $Authorization['Authorization']
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $arr = json_decode($response, true);

        return array_pop($arr['datas']);
    }

    /**
     * 获取指定空间的文件列表
     * @param $prefix 要列取文件的公共前缀 前缀 /statics/js/ 和 statics/js/ 不一样,应当使用 statics/js/ 或者 statics/js
     * @param int $limit 本次列举的条目数
     * @param string $marker 上次列举返回的位置标记，作为本次列举的起点信息。
     * @param null $bucket 空间名
     * @param string $delimiter 指定目录分隔符
     * @return array|null 数组格式为: [marker=> 位置标记,不一定有这个键, items=> 文件列表]
     */
    public function getFileList($prefix, $limit = 1000, $marker = '', $bucket = null, $delimiter = '/'): ?array
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        $bucketManager = new \Qiniu\Storage\BucketManager($this->auth);
        // 列举文件
        list($ret, $err) = $bucketManager->listFiles($bucket, $prefix, $marker, $limit, $delimiter);
        if ($err !== null) {
            return null;
        } else {
            return $ret;
        }
    }

    /**
     * 清理bucket空间,递归删除
     * 单次获取文件1000个,因为接口限制每次最多1000个,如果文件前缀下文件大于1000个,会出现不能全部删除的情况.
     * 问题不大,此函数主要作用是 文件先会被上传到北美空间(上传快,因为github在外国),然后跨区域同步到华东,所以北美空间的是冗余
     * 免费存储空间只有10G,如果保留北美空间的文件,则会造成浪费,所以每次任务结束后,尽量将北美空间文件全部删除.
     * 函数没有对删除成功或不成功做处理,因为不重要,这次删除不成功,下次任务时还是会删除.
     * @important 此函数不能再同步任务结束后调用,因为跨区域同步需要时间,如果同步任务结束后立即删除,可能还没有完成同步,所以应当单独调用.
     * @doc https://developer.qiniu.com/kodo/sdk/1241/php#rs-batch-delete
     * @param string $prefix 需要清理的文件前缀, 默认为空,表示根目录
     * @param null $bucket 空间名
     */
    public function cleanBucket($prefix = '', $bucket = null)
    {
        if (!$bucket) {
            $bucket = $this->bucket;
        }
        // 获取文件列表,1000个
        $lists = $this->getFileList($prefix, 1000, '', $bucket);

        if ((!isset($lists['items']) || empty($lists['items'])) && (!isset($lists['commonPrefixes']) || empty($lists['commonPrefixes']))) {
            showSuccLog("无需清理bucket: $bucket");
            return true;
        }

        if ($lists['items']) {
            $keys = array_column($lists['items'], 'key');

            showLog("清理bucket: $bucket , 批量删除", $keys);

            $config = new \Qiniu\Config();
            $bucketManager = new \Qiniu\Storage\BucketManager($this->auth, $config);
            //每次最多不能超过1000个
            $ops = $bucketManager->buildBatchDelete($bucket, $keys);
            list($ret, $err) = $bucketManager->batch($ops);
            if ($err) {
                showFailLog("ErrCode:", $err->code());
                showFailLog("ErrMsg:", $err->message());
            }

            unset($config, $bucketManager);

        }

        // 递归
        if (isset($lists['commonPrefixes'])) {
            foreach ($lists['commonPrefixes'] as $cPrefix) {
                $this->cleanBucket($cPrefix);
            }
        }

        showSuccLog("清理bucket: $bucket 结束");
        return true;
    }

}