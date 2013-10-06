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
    protected $beginBlockTagMask = "<!-- begin {hash} -->\n";
    protected $beginBlockTagRegExp = '/<!-- begin ([^\s]+) -->/';
    protected $endBlockTag = "<!-- end -->\n";
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

    public function generate($documentFile)
    {
        $this->rootDir = dirname($documentFile);

        $document = array();
        $documentLines = file($documentFile);
        for ($i = 0, $n = count($documentLines); $i < $n; $i++) {
            $line = $documentLines[$i];
            $document[] = $line;
            foreach ($this->sources as $source) {
                $content = $source->getContent($line);
                if (is_array($content)) {
                    foreach ($this->filters as $filter) {
                        $content = $filter->filter($content);
                    }
                    if ($this->isBeginBlockTag($documentLines[$i + 1])) {
                        $beginBlockLine = $documentLines[++$i];
                        $parsedHash = $this->parseHashFromBeginBlockLine($beginBlockLine);
                        $i += 1; // line next to block beginning
                        $block = '';
                        while (!$this->isEndBlockTag($documentLines[$i])) {
                            $block .= $documentLines[$i++];
                        }
                        $endBlockLine = $documentLines[$i];
                        if (!$this->isBlockValid($block, $parsedHash)) {
                            $document[] = $beginBlockLine;
                            $document[] = $block;
                            $document[] = $endBlockLine;
                            break;
                        }
                    }
                    $code = $this->unShiftCode(implode($content));
                    $document[] = $this->getBeginBlockTagMask($this->hash($code));
                    $document[] = $code;
                    $document[] = $this->endBlockTag;
                }
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

    /**
     * @param string $line
     * @return bool
     */
    protected function isBeginBlockTag($line)
    {
        return preg_match($this->beginBlockTagRegExp, $line, $placeholders);
    }

    /**
     * @param string $line
     * @return bool
     */
    protected function isEndBlockTag($line)
    {
        return $this->endBlockTag == $line;
    }

    /**
     * @param string $code
     * @return string
     */
    protected function hash($code)
    {
        return md5($code);
    }

    protected function getBeginBlockTagMask($hash)
    {
        return str_replace('{hash}', $hash, $this->beginBlockTagMask);
    }

    protected function parseHashFromBeginBlockLine($line)
    {
        $placeholders = array();
        if (preg_match($this->beginBlockTagRegExp, $line, $placeholders)) {
            return $placeholders[1];
        }
        return '';
    }

    protected function isBlockValid($block, $validHash)
    {
        return $this->hash($block) == $validHash;
    }
}
