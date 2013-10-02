<?php

namespace Testo\Tests;

use Testo\Phpunit\ReGenerateDocsOnSuccessListener;
use Testo\Testo;

class ReGenerateDocsOnSuccessListenerTest extends \PHPUnit_Framework_TestCase
{
    private $documents = array(
        'doc1' => 'doc1',
        'doc2' => 'doc2',
        'doc3' => 'doc3'
    );

    private static $oldRootDir;


    public static function setUpBeforeClass()
    {
        self::$oldRootDir = ReGenerateDocsOnSuccessListener::getRootDir();
    }

    public static function tearDownAfterClass()
    {
        ReGenerateDocsOnSuccessListener::setRootDir(self::$oldRootDir);
    }

    protected function setUp()
    {
        ReGenerateDocsOnSuccessListener::setRootDir(null);
    }

    /**
     * @test
     */
    public function shouldCreateInstanceOfTestedClass()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $this->assertTrue($listener instanceof ReGenerateDocsOnSuccessListener);
    }

    /**
     * @test
     */
    public function shouldCheckSetTestoInstanceMethod()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->setTestoInstance($this->getTestoMock());
        $this->assertTrue($listener->getTestoInstance() instanceof Testo);
    }

    /**
     * @test
     */
    public function shouldCheckSetRootDirMethod()
    {
        $rootDir = 'path/to/somewhere';
        ReGenerateDocsOnSuccessListener::setRootDir($rootDir);
        $actualRootDir = ReGenerateDocsOnSuccessListener::getRootDir();
        $this->assertEquals($rootDir, $actualRootDir);
    }

    /**
     * @test
     */
    public function shouldIsSuccessBeFalseOnAddError()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->addError($this->getFrameworkTestMock(), new \Exception(), time());
        $this->assertFalse($listener->getIsSuccess());
    }

    /**
     * @test
     */
    public function shouldIsSuccessBeFalseOnAddFailure()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->addFailure($this->getFrameworkTestMock(),new \PHPUnit_Framework_AssertionFailedError(), time());
        $this->assertFalse($listener->getIsSuccess());
    }

    /**
     * @test
     */
    public function shouldDoNothingOnAddIncompleteTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->addIncompleteTest($this->getFrameworkTestMock(), new \Exception(), time());
    }

    /**
     * @test
     */
    public function shouldDoNothingOnAddSkippedTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->addSkippedTest($this->getFrameworkTestMock(), new \Exception(), time());
    }

    /**
     * @test
     */
    public function shouldDoNothingOnStartTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->startTest($this->getFrameworkTestMock());
    }

    /**
     * @test
     */
    public function shouldDoNothingOnEndTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->endTest($this->getFrameworkTestMock(), time());
    }

    /**
     * @test
     */
    public function shouldCallGenerateMethodInvocation()
    {
        $testoMock = $this->getTestoMock();
        $testoMock->expects($this->exactly(count($this->documents)))
            ->method('generate');

        $testSuitMock = $this->getFrameworkTestSuiteMock();

        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->setTestoInstance($testoMock);
        $listener->startTestSuite($testSuitMock);
        $listener->endTestSuite($testSuitMock);
    }

    /**
     * @test
     */
    public function shouldNotCallGenerateMethodBecauseIsSuccessIsFalse()
    {
        $testoMock = $this->getTestoMock();
        $testoMock->expects($this->never())
            ->method('generate');

        $testSuitMock = $this->getFrameworkTestSuiteMock();

        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->setIsSuccess(false);
        $listener->setTestoInstance($testoMock);
        $listener->startTestSuite($testSuitMock);
        $listener->endTestSuite($testSuitMock);
    }

    /**
     * @test
     */
    public function shouldNotCallGenerateMethodBecauseRootTestSuiteAndPassedTestSuiteAreNotTheSame()
    {
        $testoMock = $this->getTestoMock();
        $testoMock->expects($this->never())
            ->method('generate');

        $testSuitMock = $this->getFrameworkTestSuiteMock();
        $testSuitMock2 = $this->getFrameworkTestSuiteMock();

        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->setTestoInstance($testoMock);
        $listener->startTestSuite($testSuitMock);
        $listener->endTestSuite($testSuitMock2);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Testo
     */
    protected function getTestoMock()
    {
        return $this->getMock('\Testo\Testo', array('generate'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_Test
     */
    protected function getFrameworkTestMock()
    {
        return $this->getMock('\PHPUnit_Framework_Test');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_TestSuite
     */
    protected function getFrameworkTestSuiteMock()
    {
        return $this->getMock('\PHPUnit_Framework_TestSuite');
    }
}
