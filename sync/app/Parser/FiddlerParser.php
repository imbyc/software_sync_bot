<?php

namespace App\Parser;

use App\Utils\Util;

// 从 https://telerik-fiddler.s3.amazonaws.com/fiddler/fiddler-version-files/new4.txt 解析,使用正则找出版本,时间,更新日志
//5
//0
//20202
//18177
//5.0.20202.18177 [04/21/2020]
//features:
// "Accept all CONNECTs" option in AutoResponder
//
//fixes:
// Quick filters - removed filter cannot be reapplied
// Various bugfixes and improvements
//
//5.0.20194.41348 [10/03/2019]
//features:
// Ability to preserve filters in Fiddler
// New GetStarted tab
// Ability to group AutoResponder rules
// Fiddler recognizes MSEdge and Brave as web browsers
// New preference fiddler.knownbrowsers - comma-separated list with processes that Fiddler recognizes as browsers
//
//fixes:
// Ctrl+Shift+C and Ctrl+C shortcuts do not copy
// m shortcut changes selection in AutoResponder tab
// Minor bug fixes
//
//5.0.20192.25091 [06/04/2019]
// Security fix CVE-2019-12097
// Update all copyright strings to Progress Software EAD
// Various bugfixes and improvements
//
//5.0.20182.28034 [06/27/2018]
// Publish Fiddler Chocolatey package
// Various bugfixes and improvements
//
//@WELCOME@
//Are you sweating on building JavaScript UI?
//
//    Speed up your development by using Kendo UI library for your next project - http://prgress.co/2swAPLK



class FiddlerParser extends Parser
{
    public function parserVersion($data, $rule)
    {
        preg_match('%(\d+\.\d+\.\d+\.\d+) \[(\d+/\d+/\d+)\].*?\d+\.%sim', $data, $regs);
        return $regs[1];
    }

    public function parserTime($data, $rule): array
    {
        preg_match('%(\d+\.\d+\.\d+\.\d+) \[(\d+/\d+/\d+)\].*?\d+\.%sim', $data, $regs);
        //原时间格式 , 原时间格式转为时间戳
        return [$regs[2], Util::datetime2timestamp($regs[2])];
    }

    public function parserNotes($data, $rule)
    {
        preg_match('%(\d+\.\d+\.\d+\.\d+) \[(\d+/\d+/\d+)\].*?\d+\.%sim', $data, $regs);
        $startPos = strpos($regs[0], "\n");
        $endPos = strripos($regs[0], "\n");
        return substr($regs[0], $startPos, $endPos - $startPos);
    }
}