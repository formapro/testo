<?php
namespace Testo\Phpunit;

use Testo\Testo;

class ReGenerateDocsOnSuccessListener implements \PHPUnit_Framework_TestListener
{
    /**
     * @var string
     */
    protected $rootSuite;

    /**
     * @var bool
     */
    protected $isSuccess = true;

    /**
     * @var array
     */
    protected $documentsFiles;

    /**
     * @var string
     */
    protected static $rootDir;

    /**
     * @var  Testo
     */
    protected $testo;

    /**
     * @param array $documentsFiles
     */
    public function __construct(array $documentsFiles)
    {
        $this->testo = new Testo();
        if (false == self::$rootDir) {
            self::$rootDir = getcwd();
        }

        $this->documentsFiles = $documentsFiles;
    }

    /**
     * @param string $rootDir
     */
    public static function setRootDir($rootDir)
    {
        static::$rootDir = $rootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->isSuccess = false;
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->isSuccess = false;
    }

    /**
     * {@inheritdoc}
     */
    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if (null === $this->rootSuite) {
            $this->rootSuite = $suite;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        if ($this->rootSuite === $suite && $this->isSuccess) {

            foreach ($this->documentsFiles as $documentFile) {
                $this->testo->generate(
                    static::$rootDir . '/' . $documentFile
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(\PHPUnit_Framework_Test $test)
    {
    }

    /**
     * {@inheritdoc}
     */
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
