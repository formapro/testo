<?php
namespace Testo\Filter;

class UncommentFilter implements FilterInterface
{
    /**
     * @var string
     */
    protected $uncommentTagRegExp = '|//\s*@testo\s+uncomment\s*|';

    /**
     * @var string
     */
    protected $beginUncommentTagRegExp = '|(//\s*@testo\s+uncomment\s*{\s*)|';

    /**
     * @var string
     */
    protected $endUncommentTagRegExp = '|(//\s*@testo\s+uncomment\s*}\s*)|';

    /**
     * {@inheritDoc}
     */
    public function filter(array $codeLines)
    {
        $commentStarted = false;
        $skipLine = false;

        $codeLinesWithoutMultiLineComments = array();
        $placeholders = array();
        foreach ($codeLines as $codeLine) {

            if (preg_match($this->beginUncommentTagRegExp, $codeLine, $placeholders)) {
                $commentStarted = true;
                $skipLine = true;
            }

            if (preg_match($this->endUncommentTagRegExp, $codeLine, $placeholders)) {
                $commentStarted = false;
                $skipLine = true;
            }

            if ($commentStarted) {
                $codeLine = str_replace('//', '', $codeLine);
            }

            if ($skipLine) {
                $skipLine = false;
            } else {
                $codeLinesWithoutMultiLineComments[] = $codeLine;
            }
        }

        foreach ($codeLinesWithoutMultiLineComments as &$methodCodeLine) {
            if (preg_match($this->uncommentTagRegExp, $methodCodeLine, $placeholders)) {
                $methodCodeLine = str_replace($placeholders[0], '', $methodCodeLine);
            }
        }

        return $codeLinesWithoutMultiLineComments;
    }
}
