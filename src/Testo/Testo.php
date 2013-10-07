<?php
namespace Testo;

use Testo\Filter\FilterInterface;
use Testo\Filter\LeaveBlocksFilter;
use Testo\Filter\UncommentFilter;
use Testo\Source\ClassSource;
use Testo\Source\FileSource;
use Testo\Source\MethodSource;
use Testo\Source\RootDirAwareInterface;
use Testo\Source\SourceInterface;

class Testo implements RootDirAwareInterface
{
    /**
     * @var SourceInterface[]
     */
    protected $sources;

    /**
     * @var FilterInterface[]
     */
    protected $filters;

    /**
     * @var string
     */
    protected $rootDir;

    public function __construct()
    {
        $this->filters = array();
        $this->filters[] = new UncommentFilter();
        $this->filters[] = new LeaveBlocksFilter();

        $this->sources = array();
        $this->sources[] = new FileSource($this);
        $this->sources[] = new ClassSource();
        $this->sources[] = new MethodSource();

    }

    /**
     * @param string $templateFile
     * @param string $documentFile
     */
    public function generate($templateFile, $documentFile)
    {
        $this->rootDir = dirname($templateFile);

        $document = array();
        foreach (file($templateFile) as $line) {
            $lineReplaced = false;
            foreach ($this->sources as $source) {
                $content = $source->getContent($line);
                if (is_array($content)) {
                    $lineReplaced = true;
                    foreach ($this->filters as $filter) {
                        $content = $filter->filter($content);
                    }
                    $document[] = $this->unShiftCode(implode($content));
                }
            }
            if (!$lineReplaced) {
                $document[] = $line;
            }
        }

        file_put_contents($documentFile, implode('', $document));
    }

    /**
     * @param string $code
     * @return string
     */
    protected function unShiftCode($code)
    {
        $code = trim($code, "\n") . "\n";
        $placeholders = array();
        if (preg_match('/^(\s*?)[^\s]/', $code, $placeholders)) {
            $code = preg_replace("/^$placeholders[1]/", '', $code);
            $code = str_replace("\n$placeholders[1]", "\n", $code);
        }

        return $code;
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }
}
