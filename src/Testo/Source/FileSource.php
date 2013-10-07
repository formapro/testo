<?php
namespace Testo\Source;

use Testo\Exception\FileNotFoundException;

class FileSource implements SourceInterface
{
    /**
     * @var string
     */
    protected $fileTagRegExp = '/^\s*@testo\s+([^\.]+\.[^\s]+)\s*$/m';

    /**
     * @var RootDirAwareInterface
     */
    protected $rootDirAware;

    /**
     * @param RootDirAwareInterface $rootDirAware
     */
    public function __construct(RootDirAwareInterface $rootDirAware)
    {
        $this->rootDirAware = $rootDirAware;
    }

    /**
     * {@inheritDoc}
     *
     * @throws FileNotFoundException
     */
    public function getContent($line)
    {
        $placeholders = array();
        if (preg_match($this->fileTagRegExp, $line, $placeholders)) {
            $absolutePathToFile = $this->rootDirAware->getRootDir() . '/' . $placeholders[1];
            if (is_file($absolutePathToFile)) {
                $fileLines = file($absolutePathToFile);

                return $fileLines;
            } else {
                throw new FileNotFoundException($line);
            }
        }

        return false;
    }

}
