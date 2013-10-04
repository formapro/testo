<?php
namespace Testo\Filters;

class LeaveBlocksFilter implements FilterInterface
{
    protected $beginBlockTagRegExp = '|//\s*@testo\s*{\s*$|m';
    protected $endBlockTagRegExp = '|//\s*@testo\s*}\s*$|m';

    public function filter(array $codeLines)
    {
        $blockStarted = false;
        $blockLines = array();
        foreach ($codeLines as $codeLine) {
            if (preg_match($this->endBlockTagRegExp, $codeLine, $placeholders)) {
                $blockStarted = false;
            }

            if ($blockStarted) {
                $blockLines[] = $codeLine;
            }

            if (preg_match($this->beginBlockTagRegExp, $codeLine, $placeholders)) {
                $blockStarted = true;
            }
        }
        if (!$blockLines) {
            return $codeLines;
        }

        return $blockLines;
    }
}
