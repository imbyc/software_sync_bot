<?php

namespace App\Parser;

use App\Utils\Util;

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