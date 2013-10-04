<?php
namespace Testo\Sources;

class FileSource extends AbstractSource
{
    protected $fileTagRegExp = '/^\s*@testo\s+([^\s]+)\s*$/m';

    /**
     * @param string $line
     * @return array
     */
    public function getContent($line)
    {
        $placeholders = array();
        if (preg_match($this->fileTagRegExp, $line, $placeholders)) {
            $absolutePathToFile = $this->testo->getRootDir() . '/' . $placeholders[1];
            if (is_file($absolutePathToFile)) {

                $fileLines = file($absolutePathToFile);
                return $fileLines;
            }
        }

        return false;
    }

}
