/**
 * 从channel和version去判断是否是稳定版
 * 稳定版的channel通常值为 stable, release
 * 如果channel判断不出来尝试从version中去判断,如果version中包含,RC,BETA,ALPHA 的为不稳定版
 * 判断结果不一定准确,如果判断不出来就返回true
 * @param version
 * @param channel
 * @returns {boolean}
 */
function isStableVersion(version, channel) {
    if (!channel) channel = '';
    if (!version) version = '';
    channel = channel.toLowerCase();
    version = version.toLowerCase();
    if (channel.indexOf('stable') !== -1 || channel.indexOf('release') !== -1) {
        return true;
    }
    return !(version.indexOf('rc') !== -1 || version.indexOf('beta') !== -1 || version.indexOf('alpha') !== -1);
}

/**
 * 判断显示平台
 * @param softshowplatform
 * @returns {boolean|*}
 */
function isSoftShowPlatform(softshowplatform) {
    return typeof softshowplatform != "undefined" && softshowplatform;
}

/**
 * 格式化文件大小, 输出成带单位的字符串
 * @param {Number} size 文件大小
 * @param {Number} [pointLength=2] 精确到的小数点数。
 * @param {Array} [units=["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"]] 单位数组。从字节，到千字节，一直往上指定。
 * 如果单位数组里面只指定了到了K(千字节)，同时文件大小大于M, 此方法的输出将还是显示成多少K.
 */
function formatFileSize(size, pointLength, units) {
    var unit;
    units = units || ["Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"];
    while ((unit = units.shift()) && size > 1024) {
        size = size / 1024;
    }
    return (unit === 'B' ? size : size.toFixed(pointLength === undefined ? 2 : pointLength)) + unit;
}

/**
 * 探测文件类型
 * exe/msi windows的安装包
 * pkg/dmg mac安装包
 * rar/zip/7z windows上常用的压缩包
 * tar/gz/tar.gz/tar.xz/bz2/tar.bz2/Z linux常用压缩包格式
 * 带src的通常就是源码包了
 * @param filename
 * @returns {string} Source 源码包 Installer 安装包 Archive 压缩包 识别不出来返回空
 */
function fileKindDetector(filename) {
    var suffix;
    var installerSuffixs = ["exe", "msi", "pkg", "dmg"];
    var archiveSuffixs = ["rar", "zip", "7z", "tar", "gz", "tar.gz", "tar.xz", "bz2", "tar.bz2", "Z"];
    filename = filename.toLowerCase();
    if (filename.indexOf(".src.") !== -1 || filename.indexOf("-src-") !== -1 || filename.indexOf("src") !== -1) {
        return "Source";
    }
    while ((suffix = installerSuffixs.shift())) {
        if (filename.indexOf(suffix) !== -1) {
            return "Installer";
        }
    }
    while ((suffix = archiveSuffixs.shift())) {
        if (filename.indexOf(suffix) !== -1) {
            return "Archive";
        }
    }
    return "";
}

/**
 * 文件是否高亮显示
 * 规则:
 * 是源码包,进行高亮
 * 是win/mac平台,则安装包进行高亮
 * 是linux平台,压缩包进行高亮 (todo 此处不完善,压缩包种类很多,是否只高亮某一种压缩包格式,其他不高亮)
 * @param filename
 * @param platform
 * @returns {boolean}
 */
function isFileHighlight(filename, platform) {
    platform = platform.toLowerCase();
    if (fileKindDetector(filename) === "Source") return true;
    if (platform.indexOf("win") !== -1 && fileKindDetector(filename) === "Installer") return true;
    if (platform.indexOf("mac") !== -1 && fileKindDetector(filename) === "Installer") return true;
    return platform.indexOf("linux") !== -1 && fileKindDetector(filename) === "Archive";
}


function goTucao() {
    let fp = getFP();
    var data = {
        nickname: "SoftSync-" + fp.substr(1, 5),
        avatar: "https://tucao.qq.com/static/desktop/img/products/def-product-logo.png",
        openid: fp,
        customInfo: window.location.href
    };
    Tucao.request(135197, data);
}

function getFP() {
    let excludes = {
        userAgent: true,
        audio: true,
        enumerateDevices: true,
        fonts: true,
        fontsFlash: true,
        webgl: true,
        canvas: true,
    };
    let options = {excludes: excludes};
    let murmur = '';
    Fingerprint2.get(options, function (components) {
        // 参数
        const values = components.map(function (component) {
            return component.value
        });
        // 指纹
        murmur = Fingerprint2.x64hash128(values.join(''), 31);
    });
    return murmur;
}

/////////////////////////////////////
////////////公用事件//////////////////
/////////////////////////////////////
// 分享
$(document).ready(function () {
    $('#share').click(function () {
        if (CopyToClipboard(location.href)) {
            $('.tip').show();
            setTimeout(function () {
                $('.tip').fadeOut()
            }, 3000);
        }
    });
    $('#close').click(function () {
        $('.tip').hide();
    });
});

function CopyToClipboard(txt) {
    if (window.clipboardData) {
        window.clipboardData.clearData();
        window.clipboardData.setData("Text", txt);

    } else if (navigator.userAgent.indexOf("Opera") != -1) {
        //window.location = txt;
    } else if (window.netscape) {
        try {
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        } catch (e) {
            //alert("被浏览器拒绝！\n请在浏览器地址栏输入'about:config'并回车\n然后将'signed.applets.codebase_principal_support'设置为'true'");
            alert("对不起，您的浏览器不支持该复制功能，请手动复制。1");
            return;
        }
        var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
        if (!clip)
            return;
        var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
        if (!trans)
            return;
        trans.addDataFlavor('text/unicode');
        var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
        var copytext = txt;
        str.data = copytext;
        trans.setTransferData("text/unicode", str, copytext.length * 2);
        var clipid = Components.interfaces.nsIClipboard;
        if (!clip)
            return false;
        clip.setData(trans, null, clipid.kGlobalClipboard);
    } else if (document.execCommand) {
        // webkit内核
        try {
            document.getElementById('copy-holder').value = txt;
            document.getElementById('copy-holder').select();
            document.execCommand("copy");
        } catch (e) {
            alert("对不起，您的版本不支持该复制功能，请手动复制。2");
            return;
        }
    } else {
        alert("对不起，您的浏览器不支持该复制功能，请手动复制。3");
        return;
    }
    return true;
}
