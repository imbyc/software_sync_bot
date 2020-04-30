<p align="center">
  <img src="sync_128px.png">
</p>

# Software Sync Bot
![Sync Task](https://github.com/buyucoder/software_sync_bot/workflows/Sync%20Task/badge.svg?branch=master&event=schedule)  ![Update Site](https://github.com/buyucoder/software_sync_bot/workflows/Update%20Site/badge.svg?branch=master&event=push)  ![Clean Transit Bucket](https://github.com/buyucoder/software_sync_bot/workflows/Clean%20Transit%20Bucket/badge.svg?branch=master&event=schedule)

常用开发软件自动下载同步到国内,提升下载速度

网站:  http://softsync.gitee.io

网站存放在[码云 Pages](https://gitee.com/softsync/softsync),免费,缺点是不能绑定自定义域名

本项目是在学习 Github Action 时产生的项目，使用 Github Action 定时完成同步任务并生成部署网站页面。

## 基本思路

找到软件的更新日志,通过更新日志获取版本和下载地址,然后将软件下载并上传到国内的云存储,再生成网站需要的数据

## 已支持同步软件列表

[同步软件列表](doc/SYNCLIST.md)

## 同步日志

[同步日志](doc/SYNCLOG.md)

## 特性

**同步特性:**

1. 支持多种远程更新日志格式: json,html,xml,txt
2. 软件多平台单独设置，针对Win,Linux,Mac使用不同的设置
3. 同步多维度信息,包含:版本,版本时间,版本类型(稳定版/不稳定版),更新日志,新特性,文件适用平台架构(x64/x86/arm等),文件大小,文件Hash,文件类型(安装包/源码包),文件适用系统(win/mac/linux)
4. 软件支持同步开启和关闭,可设置提取版本数量，控制丢弃老的版本
5. 允许同步失败，下次任务继续同步，版本自动按新到旧排序，无需担心版本乱掉。
6. 可自定义解析器,遇到通用解析器无法解析的情况,可自定义解析器实现解析
7. 生成数据丰富，最新版本，最后检查时间，版本更新说明Html，同步日志，分类数据，列表数据，

**网站特性:**

1. 易于部署,任意可以存放静态文件的空间都可以,如: [Github Pages][Github Pages],[码云 Pages][码云Pages],[阿里云OSS][阿里云OSS],[腾讯云COS][腾讯云COS],[七牛云][七牛云],[又拍云][又拍云]等等
2. 不使用数据库,数据直接保存为json,前端网站直接ajax加载json数据,前后分离
3. 支持分类，便于查找（暂不支持搜索）
4. 同步日志，按日期显示软件同步日志 （当天没有新版本同步则不记录日志）

**本地开发特性:**

1. 本地调试可以使用代理，解决本地测试因为网络导致更新日志访问不了或者访问慢的问题
2. 调试软件同步时可以不上传文件到云存储，只处理数据，节省流量费用（七牛云跨区域同步按流量收费）

## 项目结构

待补充

## 设计思路

待补充

## 配置说明

待补充

## 开发指南

待补充

## 存储费用

待补充


## 使用到的Github Action
https://github.com/marketplace/actions/github-push

https://github.com/shimataro/ssh-key-action




[Github Pages]: https://help.github.com/cn/github/working-with-github-pages/about-github-pages "Github Pages"
[码云Pages]: https://gitee.com/help/articles/4136 "码云Pages"
[七牛云]: https://www.qiniu.com/products/kodo "七牛云对象存储"
[阿里云OSS]: https://cn.aliyun.com/product/oss "阿里云OSS"
[腾讯云COS]: https://cloud.tencent.com/product/cos "腾讯云COS"
[又拍云]: https://www.upyun.com/products/file-storage "又拍云云存储USS"

