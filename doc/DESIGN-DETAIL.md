# 设计思路

软件同步日志格式

yaml格式:

```yaml
- 日期1:
  - 平台1:
        - 版本1:
            - 文件名
            - 文件名
        - 版本2:
            - 文件名
            - 文件名
  - 平台2:
        - 版本2:
            - 文件名
            - 文件名
  - 平台3:
        - 版本3:
            - 文件名
            - 文件名
- 日期2:
  - 平台1:
        - 版本1:
            - 文件名
            - 文件名
        - 版本2:
            - 文件名
            - 文件名
  - 平台2:
        - 版本2:
            - 文件名
            - 文件名
```

json格式:

```json
[
	{
		"日期1": [
			{
				"平台1": [
					{
						"版本1": [
							"文件名",
							"文件名"
						]
					},
					{
						"版本2": [
							"文件名",
							"文件名"
						]
					}
				]
			},
			{
				"平台2": [
					{
						"版本2": [
							"文件名",
							"文件名"
						]
					}
				]
			},
			{
				"平台3": [
					{
						"版本3": [
							"文件名",
							"文件名"
						]
					}
				]
			}
		]
	},
	{
		"日期2": [
			{
				"平台1": [
					{
						"版本1": [
							"文件名",
							"文件名"
						]
					},
					{
						"版本2": [
							"文件名",
							"文件名"
						]
					}
				]
			},
			{
				"平台2": [
					{
						"版本2": [
							"文件名",
							"文件名"
						]
					}
				]
			}
		]
	}
]
```



全局同步日志格式

yaml格式

```yaml
- 日期1:
    - 软件名称1:
        - 平台1:
            - 版本2:
                - 文件名
                - 文件名
            - 版本3:
                - 文件名
                - 文件名
        - 平台2:
            - 版本2:
                - 文件名
                - 文件名
- 日期2:
    - 软件名称2:
        - 平台2:
            - 版本3:
                - 文件名
                - 文件名
        - 平台1:
            - 版本3:
                - 文件名
                - 文件名
```

json格式

```json
[
	{
		"日期1": [
			{
				"软件名称1": [
					{
						"平台1": [
							{
								"版本2": [
									"文件名",
									"文件名"
								]
							},
							{
								"版本3": [
									"文件名",
									"文件名"
								]
							}
						]
					},
					{
						"平台2": [
							{
								"版本2": [
									"文件名",
									"文件名"
								]
							}
						]
					}
				]
			}
		]
	},
	{
		"日期2": [
			{
				"软件名称2": [
					{
						"平台2": [
							{
								"版本3": [
									"文件名",
									"文件名"
								]
							}
						]
					},
					{
						"平台1": [
							{
								"版本3": [
									"文件名",
									"文件名"
								]
							}
						]
					}
				]
			}
		]
	}
]
```



从远程更新日志提取后得到的数据:

yaml

```yaml
softname: postman
softshowname: Postman
softofficialhomelink: "https://www.getpostman.com"
softofficialdownloadlink: "https://www.postman.com/downloads/"
softlogo: "/statics/soft/postman/logo.png"
softbanner: "http://pc1.gtimg.com/guanjia/images/42/8b/428b48f4f4687434577386d6d7350060.jpg"
softshortcomment: "API调试"
softcomment: "API接口开发调试工具"
softspecialtip: "特别说明"
softcategory: 
 - tool
softicon: 
 - paid
softsync: 1
softshowinlist: 1
softshowplatform: 1
release: 
 win: 
  platform: win
  platformshowname: Windows
  getversionmaxnum: 50
  showversionmaxnum: 5
  showgroupbychannel: 1
  lists: 
   "7.22.1": 
    version: "7.22.1"
    datetime: "2020-04-08T19:29:23.000Z"
    timestamp: 1586374163
    notes: "更新说明"
    features: "新特性"
    gennotespagepath: "app/postman/win/7.22.1/releasenotes.html"
    downloadList: 
     - 
      filename: "Postman-win64-7.22.1-Setup.exe"
      filehash: 5F0D85FC0D7AF5128F9A6B2AD5D598A4962645AC
      filesize: 81256544
      fileurl: "https://dl.pstmn.io/download/version/7.22.1/windows64"
      filekind: ""
      fileos: ""
      filearch: ""
      fileuploadprefix: "app/postman/win/7.22.1/"
   "7.21.2": 
    version: "7.21.2"
    datetime: "2020-04-03T12:55:07.000Z"
    timestamp: 1585918507
    notes: "更新说明"
    features: null
    gennotespagepath: "app/postman/win/7.21.2/releasenotes.html"
    downloadList: 
     - 
      filename: "Postman-win64-7.21.2-Setup.exe"
      filehash: EF57F0765873A31347E5CE16A28B0AB02BC28263
      filesize: 81779808
      fileurl: "https://dl.pstmn.io/download/version/7.21.2/windows64"
      filekind: ""
      fileos: ""
      filearch: ""
      fileuploadprefix: "app/postman/win/7.21.2/"
 mac: 
  platform: mac
  platformshowname: MacOS
  lists: 
   "7.22.1": 
    version: "7.22.1"
    datetime: "2020-04-08T19:29:23.000Z"
    timestamp: 1586374163
    notes: "更新说明"
    features: "新特性"
    gennotespagepath: "app/postman/mac/7.22.1/releasenotes.html"
    downloadList: 
     - 
      filename: "Postman-osx-7.22.1.zip"
      filehash: 4DC496F5453C44078D8C5D603D3931586B800A85
      filesize: 88706115
      fileurl: "https://dl.pstmn.io/download/version/7.22.1/osx64"
      filekind: ""
      fileos: ""
      filearch: ""
      fileuploadprefix: "app/postman/mac/7.22.1/"
```

json

```json
{
	"softname": "postman",
	"softshowname": "Postman",
	"softofficialhomelink": "https://www.getpostman.com",
	"softofficialdownloadlink": "https://www.postman.com/downloads/",
	"softlogo": "/statics/soft/postman/logo.png",
	"softbanner": "http://pc1.gtimg.com/guanjia/images/42/8b/428b48f4f4687434577386d6d7350060.jpg",
	"softshortcomment": "API调试",
	"softcomment": "API接口开发调试工具",
	"softspecialtip": "特别说明",
	"softcategory": [
		"tool"
	],
	"softicon": [
		"paid"
	],
	"softsync": 1,
	"softshowinlist": 1,
	"softshowplatform": 1,
	"release": {
		"win": {
			"platform": "win",
			"platformshowname": "Windows",
			"getversionmaxnum": 50,
			"showversionmaxnum": 5,
			"showgroupbychannel": 1,
			"lists": {
				"7.22.1": {
					"version": "7.22.1",
					"datetime": "2020-04-08T19:29:23.000Z",
					"timestamp": 1586374163,
					"notes": "更新说明",
					"features": "新特性",
					"gennotespagepath": "app/postman/win/7.22.1/releasenotes.html",
					"downloadList": [
						{
							"filename": "Postman-win64-7.22.1-Setup.exe",
							"filehash": "5F0D85FC0D7AF5128F9A6B2AD5D598A4962645AC",
							"filesize": 81256544,
							"fileurl": "https://dl.pstmn.io/download/version/7.22.1/windows64",
							"filekind": "",
							"fileos": "",
							"filearch": "",
							"fileuploadprefix": "app/postman/win/7.22.1/"
						}
					]
				},
				"7.21.2": {
					"version": "7.21.2",
					"datetime": "2020-04-03T12:55:07.000Z",
					"timestamp": 1585918507,
					"notes": "更新说明",
					"features": null,
					"gennotespagepath": "app/postman/win/7.21.2/releasenotes.html",
					"downloadList": [
						{
							"filename": "Postman-win64-7.21.2-Setup.exe",
							"filehash": "EF57F0765873A31347E5CE16A28B0AB02BC28263",
							"filesize": 81779808,
							"fileurl": "https://dl.pstmn.io/download/version/7.21.2/windows64",
							"filekind": "",
							"fileos": "",
							"filearch": "",
							"fileuploadprefix": "app/postman/win/7.21.2/"
						}
					]
				}
			}
		},
		"mac": {
			"platform": "mac",
			"platformshowname": "MacOS",
			"lists": {
				"7.22.1": {
					"version": "7.22.1",
					"datetime": "2020-04-08T19:29:23.000Z",
					"timestamp": 1586374163,
					"notes": "更新说明",
					"features": "新特性",
					"gennotespagepath": "app/postman/mac/7.22.1/releasenotes.html",
					"downloadList": [
						{
							"filename": "Postman-osx-7.22.1.zip",
							"filehash": "4DC496F5453C44078D8C5D603D3931586B800A85",
							"filesize": 88706115,
							"fileurl": "https://dl.pstmn.io/download/version/7.22.1/osx64",
							"filekind": "",
							"fileos": "",
							"filearch": "",
							"fileuploadprefix": "app/postman/mac/7.22.1/"
						}
					]
				}
			}
		}
	}
}
```

经过上传同步后得到的数据(上传失败的会剔除)

yaml

```yaml
softname: postman
softshowname: Postman
softofficialhomelink: "https://www.getpostman.com"
softofficialdownloadlink: "https://www.postman.com/downloads/"
softlogo: "/statics/soft/postman/logo.png"
softbanner: "http://pc1.gtimg.com/guanjia/images/42/8b/428b48f4f4687434577386d6d7350060.jpg"
softshortcomment: "API调试"
softcomment: "API接口开发调试工具"
softspecialtip: "特别说明"
softcategory: 
 - tool
softicon: 
 - paid
softsync: 1
softshowinlist: 1
softshowplatform: 1
release: 
 win: 
  platform: win
  platformshowname: Windows
  getversionmaxnum: 50
  showversionmaxnum: 5
  showgroupbychannel: 1
  lists: 
   "7.22.1": 
        version: "7.22.1"
        datetime: "2020-04-08T19:29:23.000Z"
        timestamp: 1586374163
        notes: "更新说明"
        features: "新特性"
        gennotespagepath: "app/postman/win/7.22.1/releasenotes.html"
        downloadList: 
         - 
          filename: "Postman-win64-7.22.1-Setup.exe"
          filehash: 5F0D85FC0D7AF5128F9A6B2AD5D598A4962645AC
          filesize: 81256544
          fileurl: "https://dl.pstmn.io/download/version/7.22.1/windows64"
          filekind: ""
          fileos: ""
          filearch: ""
          fileuploadprefix: "app/postman/win/7.22.1/"
          filekey: "app/postman/win/7.22.1/Postman-win64-7.22.1-Setup.exe"
   "7.21.2": 
        version: "7.21.2"
        datetime: "2020-04-03T12:55:07.000Z"
        timestamp: 1585918507
        notes: "更新说明"
        features: null
        gennotespagepath: "app/postman/win/7.21.2/releasenotes.html"
        downloadList: 
         - 
          filename: "Postman-win64-7.21.2-Setup.exe"
          filehash: EF57F0765873A31347E5CE16A28B0AB02BC28263
          filesize: 81779808
          fileurl: "https://dl.pstmn.io/download/version/7.21.2/windows64"
          filekind: ""
          fileos: ""
          filearch: ""
          fileuploadprefix: "app/postman/win/7.21.2/"
          filekey: "app/postman/win/7.21.2/Postman-win64-7.21.2-Setup.exe"
```

json

```json
{
	"softname": "postman",
	"softshowname": "Postman",
	"softofficialhomelink": "https://www.getpostman.com",
	"softofficialdownloadlink": "https://www.postman.com/downloads/",
	"softlogo": "/statics/soft/postman/logo.png",
	"softbanner": "http://pc1.gtimg.com/guanjia/images/42/8b/428b48f4f4687434577386d6d7350060.jpg",
	"softshortcomment": "API调试",
	"softcomment": "API接口开发调试工具",
	"softspecialtip": "特别说明",
	"softcategory": [
		"tool"
	],
	"softicon": [
		"paid"
	],
	"softsync": 1,
	"softshowinlist": 1,
	"softshowplatform": 1,
	"release": {
		"win": {
			"platform": "win",
			"platformshowname": "Windows",
			"getversionmaxnum": 50,
			"showversionmaxnum": 5,
			"showgroupbychannel": 1,
			"lists": {
				"7.22.1": {
					"version": "7.22.1",
					"datetime": "2020-04-08T19:29:23.000Z",
					"timestamp": 1586374163,
					"notes": "更新说明",
					"features": "新特性",
					"gennotespagepath": "app/postman/win/7.22.1/releasenotes.html",
					"downloadList": [
						{
							"filename": "Postman-win64-7.22.1-Setup.exe",
							"filehash": "5F0D85FC0D7AF5128F9A6B2AD5D598A4962645AC",
							"filesize": 81256544,
							"fileurl": "https://dl.pstmn.io/download/version/7.22.1/windows64",
							"filekind": "",
							"fileos": "",
							"filearch": "",
							"fileuploadprefix": "app/postman/win/7.22.1/",
							"filekey": "app/postman/win/7.22.1/Postman-win64-7.22.1-Setup.exe"
						}
					]
				},
				"7.21.2": {
					"version": "7.21.2",
					"datetime": "2020-04-03T12:55:07.000Z",
					"timestamp": 1585918507,
					"notes": "更新说明",
					"features": null,
					"gennotespagepath": "app/postman/win/7.21.2/releasenotes.html",
					"downloadList": [
						{
							"filename": "Postman-win64-7.21.2-Setup.exe",
							"filehash": "EF57F0765873A31347E5CE16A28B0AB02BC28263",
							"filesize": 81779808,
							"fileurl": "https://dl.pstmn.io/download/version/7.21.2/windows64",
							"filekind": "",
							"fileos": "",
							"filearch": "",
							"fileuploadprefix": "app/postman/win/7.21.2/",
							"filekey": "app/postman/win/7.21.2/Postman-win64-7.21.2-Setup.exe"
						}
					]
				}
			}
		}
	}
}
```

Action 触发机制

同步任务: 使用定时任务触发,每天触发2次,早上8点和晚上6点

Pull触发: 当代码提交时,可能会有这些内容修改,修改了静态页面,同步配置项发生修改,如软件的基础信息发生修改,这些需要即时同步到网站上去.

要判断修改了哪些文件.

1. 提交了文件有涉及到site目录的表示静态页发生修改,将新静态页提交到空间
2. deploy发生修改
3. 

新增加了软件,要判断提交的文件中是否有 config/soft 目录下的yml配置文件（无论是新增还是修改）,如果有表示有新增软件,要立即触发同步,而不是等待定时任务去触发。

修改的也要触发,比如之前先添加了一个软件的同步配置,但 softsync 设置为了false,不同步,现在又改为了true,肯定是希望立即去同步的.
