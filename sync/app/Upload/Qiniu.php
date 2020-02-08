<?php

namespace App\Upload;

class Qiniu {

    private $auth;
    private $bucket;

    public function __construct($no=1)
    {
        $accessKey =  getenv('QINIU_ACCESS_KEY_'.$no);
        $secretKey =  getenv('QINIU_SECRET_KEY_'.$no);
        var_dump($accessKey);
        var_dump($secretKey);
        $this->auth = $this->getAuth($accessKey, $secretKey);
    }

    private function getAuth ($accessKey, $secretKey) {
        return new \Qiniu\Auth($accessKey, $secretKey);
    }

    public function setBucket($bucket) {
        $this->bucket = $bucket;
    }

    public function isFileExist ($key) {
        $config = new \Qiniu\Config();
        $bucketManager = new \Qiniu\Storage\BucketManager($this->auth, $config);
        list($fileInfo, $err) = $bucketManager->stat($bucket, $key);
        if ($err) {
            print_r($err);
        } else {
            print_r($fileInfo);
        }
    }

    public function upload ($filePath, $key, $bucket) {
        // 生成上传 Token
        $token = $this->auth->uploadToken($bucket);
        // 要上传文件的本地路径
//        $filePath = $ROOT_PATH.'/config/yaml2json.exe';
        // 上传到七牛后保存的文件名
//        $key = 'test/yaml2json.exe';
        // 初始化 UploadManager 对象并进行文件的上传。
        $uploadMgr = new \Qiniu\Storage\UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
            print_r($err);
        } else {
            print_r($ret);
        }
    }

    public function fetch ($url, $key) {
        $bucketManager = new \Qiniu\Storage\BucketManager($this->auth);
        // 指定抓取的文件保存名称
        list($ret, $err) = $bucketManager->fetch($url, $this->bucket, $key);
        echo "=====> fetch $url to bucket: $this->bucket  key: $key\n";
        if ($err !== null) {
            var_dump($err);
        } else {
            print_r($ret);
        }
    }

}