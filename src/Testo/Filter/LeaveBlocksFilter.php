<?php
namespace Testo\Filter;

class LeaveBlocksFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $beginBlockTagRegExp = '|//\s*@testo\s*{\s*$|m';

    /**
     * @var string
     */
    protected $endBlockTagRegExp = '|//\s*@testo\s*}\s*$|m';

    /**
     * {@inheritDoc}
     */
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
