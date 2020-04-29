<?php
/**
 *  清理中转bucket的文件
 *  2020-4-20
 */

require 'vendor/autoload.php';

use App\Utils\Qiniu;

$qiniu = new Qiniu();

// 中转空间
$transitbucket = systemConfig('bucket.transitbucket');

$qiniu->setBucket($transitbucket);

$qiniu->cleanBucket();




