<?php

namespace App\Parser;

class FiddlerParser extends Parser {

    public static function parserDownloadData($remoteDownloadData, $downloadParserRule): array
    {

    }

    public static function parserNotes ($data, $rule) {
        $str = <<<EOF
5
0
20194
41348
5.0.20194.41348 [10/03/2019]
features:
 Ability to preserve filters in Fiddler
 New GetStarted tab
 Ability to group AutoResponder rules
 Fiddler recognizes MSEdge and Brave as web browsers
 New preference fiddler.knownbrowsers - comma-separated list with processes that Fiddler recognizes as browsers

fixes:
 Ctrl+Shift+C and Ctrl+C shortcuts do not copy
 m shortcut changes selection in AutoResponder tab
 Minor bug fixes
 
5.0.20192.25091 [06/04/2019]
 Security fix CVE-2019-12097
 Update all copyright strings to Progress Software EAD
 Various bugfixes and improvements

5.0.20182.28034 [06/27/2018]
 Publish Fiddler Chocolatey package
 Various bugfixes and improvements

@WELCOME@
Are you sweating on building JavaScript UI?

Speed up your development by using Kendo UI library for your next project - http://prgress.co/2swAPLK
EOF;


        preg_match('%(\d+\.\d+\.\d+\.\d+) \[(\d+/\d+/\d+)\].*?\d+\.%sim', $str, $regs);
        print_r($regs);
        $version = $regs[1];
        $time = $regs[2];
        echo '版本号:'.$version.PHP_EOL;
        echo '更新时间:'.$time.PHP_EOL;

        $startPos = strpos($regs[0], "\n");
        $endPos = strripos($regs[0], "\n");
        echo substr($regs[0], $startPos, $endPos-$startPos).PHP_EOL;
    }
}