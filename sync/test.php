<?php
require 'vendor/autoload.php';


use Symfony\Component\Yaml\Yaml;

$ROOT_PATH = dirname(dirname(__FILE__));

////$url = 'https://telerik-fiddler.s3.amazonaws.com/fiddler/FiddlerSetup.exe';
////$url = 'http://pc1.gtimg.com/guanjia/images/7d/5b/7d5b3c4911f5157e2e2e778d8840d3cd.jpg';
////$url = 'https://dl.pstmn.io/download/version/7.17.0/windows64';
////$url = 'https://dl.pstmn.io/download/version/7.17.0/windows64';
//
////print_r(\App\Utils\Util::getRemoteFileSize($url));
////
////die;
//
//define('SOFTNAME', 'phpstorm');
//
//// 遍历config目录
//$syncConfig = [];
//foreach (glob($ROOT_PATH.'/config/soft/'.SOFTNAME.'.yml') as $filename) {
//    try {
//        array_push($syncConfig, Yaml::parseFile($filename));
//    } catch (\Symfony\Component\Yaml\Exception\ParseException $exception) {
//        printf('Unable to parse the YAML string: %s', $exception->getMessage());
//        exit;
//    }
//}
//
//if (empty($syncConfig)) exit;
//
//foreach ($syncConfig as $c) {
//
//    //软件名称
//    $softname = $c['softname'];
//    $softhomelink = $c['softhomelink'];
//    $softdownloadlink = $c['softdownloadlink'];
//    $softlogo = $c['softlogo'];
//    $softbanner = $c['softbanner'];
//    $softtip = $c['softtip'];
//    $softcomment = $c['softcomment'];
//    $softcategory = $c['softcategory'];
//    $release = $c['release'];
//
//    foreach ($release as $platform => $item) {
//
//        // 远程链接
//        $link = $item['link'];
//        // 远程链接返回内容类型 json, html
//        $linktype = $item['linktype'];
//
//        $parserRule = $item['rule'];
//
//        // 获取远程数据
//        $remoteData = \App\Parser\Parser::getRemoteData($link, $linktype);
//
//        $releaseRootKey = $item['rule']['root'];
//
//        $remoteReleaseData = \App\Utils\Util::arrayDataGet($remoteData, $releaseRootKey);
//
//        // 远程数据解析
//        $remoteLists = \App\Parser\Parser::process($remoteReleaseData, $parserRule);
//
//        file_put_contents($ROOT_PATH.'/'.SOFTNAME.'.txt', print_r($remoteLists, true));
//
//        system('cat '.$ROOT_PATH.'/'.SOFTNAME.'.txt');
//
//
//        // 远程数据和本地数据比较
//
//        break;
//    }
//}
//
//die;

$accessKey = 'EeQXJckbbue4otga94iNWpHervr9q5QaQvY8kBHy';
$secretKey = 'wRonx-upkO2XZxM2YLh0fsoX76MX0kx0sodfLxdm';
$bucket = 'software-sync-na';
$key = "index.html";
$auth = new \Qiniu\Auth($accessKey, $secretKey);
$config = new \Qiniu\Config();
$bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);
list($fileInfo, $err) = $bucketManager->stat($bucket, $key);
if ($err) {
    print_r($err);
} else {
    print_r($fileInfo);
}

// 抓取网络资源到空间
$qiniu = new \App\Upload\Qiniu();
$url = 'https://telerik-fiddler.s3.amazonaws.com/fiddler/FiddlerSetup.exe';
$key = 'soft/fiddler/5.0.20194.41348/FiddlerSetup-5.0.20194.41348.exe';
$qiniu->fetch($url, $key);


// 获取标准存储的存储量统计
$params = [
    'bucket' => $bucket,
    'region' => 'z0',
    'begin' => strtotime("-1 day"),
    'end' => date('YmdHis'),
    'g' => 'day',
];
$url = '/v6/space?'. http_build_query($params);

$Authorization = $auth->authorization($url);

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "api.qiniu.com". $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Authorization: ".$Authorization['Authorization']
    ),
));

$response = curl_exec($curl);

curl_close($curl);
$arr = json_decode($response, true);


$lastSpace = array_pop($arr['datas']);

echo \App\Utils\Util::formatBytes($lastSpace);

die;


// 获取指定前缀文件列表
$bucketManager = new \Qiniu\Storage\BucketManager($auth);
// 要列取文件的公共前缀
$prefix = 'statics/js/';
// 上次列举返回的位置标记，作为本次列举的起点信息。
$marker = 'eyJjIjowLCJrIjoic3RhdGljcy9qcy9qcXVlcnkuanMifQ==';
$marker = '';
// 本次列举的条目数
$limit = 1000;
$delimiter = '/';
// 列举文件
list($ret, $err) = $bucketManager->listFiles($bucket, $prefix, $marker, $limit, $delimiter);
if ($err !== null) {
    echo "\n====> list file err: \n";
    print_r($err);
} else {
    if (array_key_exists('marker', $ret)) {
        echo "Marker:" . $ret["marker"] . "\n";
    }
    echo "\nList Iterms====>\n";
    print_r($ret['items']);
}


// 文件上传

// 生成上传 Token
$token = $auth->uploadToken($bucket);
// 要上传文件的本地路径
$filePath = $ROOT_PATH.'/config/yaml2json.exe';
// 上传到七牛后保存的文件名
$key = 'test/yaml2json.exe';
// 初始化 UploadManager 对象并进行文件的上传。
$uploadMgr = new \Qiniu\Storage\UploadManager();
// 调用 UploadManager 的 putFile 方法进行文件的上传。
list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
echo "\n====> putFile result: \n";
if ($err !== null) {
    print_r($err);
} else {
    print_r($ret);
}