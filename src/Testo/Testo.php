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
     * @var string
     */
    protected $endBlockTagMask = "@testo {hash} }\n";

    /**
     * @var string
     */
    protected $endBlockTagRegExp = '/@testo\s+([^\s]*)\s*}/';

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
     * @param $documentFile
     * @throws \LogicException
     */
    public function generate($documentFile)
    {
        $this->rootDir = dirname($documentFile);

        $document = array();
        $documentLines = file($documentFile);
        for ($i = 0, $n = count($documentLines); $i < $n; $i++) {
            $line = $documentLines[$i];
            $document[] = $line;
            foreach ($this->sources as $source) {
                $content = $source->getContent(rtrim($line, "\n{ "));
                if (is_array($content)) {
                    foreach ($this->filters as $filter) {
                        $content = $filter->filter($content);
                    }
                    $block = '';
                    $i++;
                    while (!$this->isEndBlockTag($documentLines[$i])) {
                        $block .= $documentLines[$i];
                        $i++;
                    }
                    $endBlockLine = $documentLines[$i];
                    $parsedHash = $this->parseHashFromEndBlockLine($endBlockLine);
                    if (!$this->isBlockValid($block, $parsedHash)) {
                        throw new \LogicException(sprintf(
                            "Block changed externally\n\nFile is '%s'\nLine is '%s'\nCode is '%s'",
                            $documentFile,
                            $line,
                            $block
                        ));
                    }
                    $code = $this->unShiftCode(implode($content));
                    $document[] = $code;
                    $document[] = $this->getEndTag($this->hash($code));
                }
            }
        }

        file_put_contents($documentFile, implode('', $document));
    }

    /**
     * @param string $code
     *
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
     *
     * @return bool
     */
    protected function isEndBlockTag($line)
    {
        return preg_match($this->endBlockTagRegExp, $line, $placeholders);
    }

    /**
     * @param string $code
     *
     * @return string
     */
    protected function hash($code)
    {
        return md5($code);
    }

    /**
     * @param string $hash
     *
     * @return string
     */
    protected function getEndTag($hash)
    {
        return str_replace('{hash}', $hash, $this->endBlockTagMask);
    }

    /**
     * @param string $line
     *
     * @return string
     */
    protected function parseHashFromEndBlockLine($line)
    {
        $placeholders = array();
        if (preg_match($this->endBlockTagRegExp, $line, $placeholders)) {

            return $placeholders[1];
        }

        return '';
    }

    /**
     * @param string $block
     * @param string $validHash
     *
     * @return bool
     */
    protected function isBlockValid($block, $validHash)
    {
        if ($block == '' && $validHash == '') {

            return true;
        }

        return $this->hash($block) == $validHash;
    }
}
