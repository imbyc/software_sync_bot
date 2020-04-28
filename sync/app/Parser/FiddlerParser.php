<?php

namespace App\Parser;

class FiddlerParser extends Parser {

    public function parserVersion($data, $rule)
    {
        preg_match('%(\d+\.\d+\.\d+\.\d+) \[(\d+/\d+/\d+)\].*?\d+\.%sim', $data, $regs);
        return $regs[1];
    }

    public function parserTime($data, $rule)
    {
        preg_match('%(\d+\.\d+\.\d+\.\d+) \[(\d+/\d+/\d+)\].*?\d+\.%sim', $data, $regs);
        return $regs[2];
    }

    public function parserNotes ($data, $rule) {
        preg_match('%(\d+\.\d+\.\d+\.\d+) \[(\d+/\d+/\d+)\].*?\d+\.%sim', $data, $regs);
        $startPos = strpos($regs[0], "\n");
        $endPos = strripos($regs[0], "\n");
        return substr($regs[0], $startPos, $endPos-$startPos);
    }
}