<?php
namespace Testo;

use Testo\Filters\FilterInterface;
use Testo\Filters\LeaveBlocksFilter;
use Testo\Filters\UncommentFilter;
use Testo\Sources\ClassSource;
use Testo\Sources\FileSource;
use Testo\Sources\MethodSource;
use Testo\Sources\AbstractSource;

class Testo
{
    /**
     * @var AbstractSource[]
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
        $this->filters[] = new UncommentFilter();
        $this->filters[] = new LeaveBlocksFilter();

        $this->sources[] = new FileSource($this);
        $this->sources[] = new ClassSource($this);
        $this->sources[] = new MethodSource($this);

    }

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
