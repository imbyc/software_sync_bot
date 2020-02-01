# software_sync_bot
常用开发软件自动下载同步到国内,方便快速下载

## 基本思路

找到软件的更新日志,通过更新日志获取版本和下载地址,然后将软件下载,在通过接口将文件上传到[蓝奏云](http://pan.lanzou.com)

蓝奏云免费版有单文件100M的限制,对于大于100M的文件使用 RAR 分卷压缩的方式将大文件拆分成 100MB 的小块。

蓝奏云上传使用[LanZouCloud-API](https://github.com/zaxtyson/LanZouCloud-API)项目


## 流程

使用 github Actions 定时运行脚本


## 软件同步地址

https://www.lanzous.com/b00z79vkd
