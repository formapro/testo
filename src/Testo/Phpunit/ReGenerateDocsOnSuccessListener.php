<?php
namespace Testo\Phpunit;

use Testo\Testo;

class ReGenerateDocsOnSuccessListener implements \PHPUnit_Framework_TestListener
{
    protected $rootSuite;

    protected $isSuccess = true;

    protected $documentsFiles;

    protected static $rootDir;

    /**
     * @var  Testo
     */
    protected $testo;

    public function __construct(array $documentsFiles)
    {
        $this->testo=new Testo();
        if (false == self::$rootDir) {
            self::$rootDir = getcwd();
        }

        $this->documentsFiles = $documentsFiles;
    }

    public static function setRootDir($rootDir)
    {
        static::$rootDir = $rootDir;
    }

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->isSuccess = false;
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->isSuccess = false;
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if (null === $this->rootSuite) {
            $this->rootSuite = $suite;
        }
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->rootSuite === $suite && $this->isSuccess) {

            foreach ($this->documentsFiles as $templateFile => $documentFile) {
                $this->testo->generate(
                    static::$rootDir . '/' . $templateFile,
                    static::$rootDir . '/' . $documentFile
                );
            }
        }
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
    }

    /**
     * @param Testo $testo
     */
    public function setTesto(Testo $testo)
    {
        $this->testo = $testo;
    }

}
