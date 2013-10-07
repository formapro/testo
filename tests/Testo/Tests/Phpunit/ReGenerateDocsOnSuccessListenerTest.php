<?php

namespace Testo\Tests\Phpunit;

use Testo\Phpunit\ReGenerateDocsOnSuccessListener;
use Testo\Testo;

class ReGenerateDocsOnSuccessListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var string
     */
    private static $oldRootDir;

    public static function setUpBeforeClass()
    {
        self::$oldRootDir = self::readAttribute('Testo\Phpunit\ReGenerateDocsOnSuccessListener', 'rootDir');
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
    public function couldBeConstructedWithDocumentsAsFirstArgument()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());

        $this->assertTrue($listener instanceof ReGenerateDocsOnSuccessListener);
    }

    /**
     * @test
     */
    public function shouldCreateTestoInstanceInConstructor()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());

        $this->assertAttributeInstanceOf('Testo\Testo', 'testo', $listener);
    }

    /**
     * @test
     */
    public function shouldAllowSetTesto()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());
        $testoMock = $this->createTestoMock();
        $listener->setTesto($testoMock);

        $this->assertAttributeInstanceOf('Testo\Testo', 'testo', $listener);
        $this->assertAttributeSame($testoMock, 'testo', $listener);
    }

    /**
     * @test
     */
    public function shouldAllowSetRootDir()
    {
        $rootDir = 'path/to/somewhere';
        ReGenerateDocsOnSuccessListener::setRootDir($rootDir);

        $this->assertAttributeEquals($rootDir, 'rootDir', 'Testo\Phpunit\ReGenerateDocsOnSuccessListener');
    }

    /**
     * @test
     */
    public function shouldSetCurrentWorkingDirectoryAsDefaultRootDirUsingInConstructor()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());

        $this->assertAttributeEquals(getcwd(), 'rootDir', $listener);
    }

    /**
     * @test
     */
    public function shouldIsSuccessBeFalseOnAddError()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());


        //guard
        $this->assertAttributeEquals(true, 'isSuccess', $listener);


        $listener->addError($this->createFrameworkTestMock(), new \Exception(), time());

        $this->assertAttributeEquals(false, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldIsSuccessBeFalseOnAddFailure()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());

        //guard
        $this->assertAttributeEquals(true, 'isSuccess', $listener);


        $listener->addFailure($this->createFrameworkTestMock(), new \PHPUnit_Framework_AssertionFailedError(), time());

        $this->assertAttributeEquals(false, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldDoNothingOnAddIncompleteTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());
        $listener->addIncompleteTest($this->createFrameworkTestMock(), new \Exception(), time());

        $this->assertAttributeEquals(true, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldDoNothingOnAddSkippedTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());
        $listener->addSkippedTest($this->createFrameworkTestMock(), new \Exception(), time());

        $this->assertAttributeEquals(true, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldDoNothingOnStartTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());
        $listener->startTest($this->createFrameworkTestMock());

        $this->assertAttributeEquals(true, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldDoNothingOnEndTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());
        $listener->endTest($this->createFrameworkTestMock(), time());

        $this->assertAttributeEquals(true, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldCallGenerateMethodOnceForOneDocumentWithCorrectParams()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array('path1' => 'path2'));
        $listenerRootDir = $this->readAttribute('Testo\Phpunit\ReGenerateDocsOnSuccessListener', 'rootDir');

        $testoMock = $this->createTestoMock();
        $testoMock->expects($this->once())
            ->method('generate')
            ->with($this->equalTo($listenerRootDir . '/path1'), $this->equalTo($listenerRootDir . '/path2'));

        $testSuitMock = $this->createFrameworkTestSuiteMock();

        $listener->setTesto($testoMock);
        $listener->startTestSuite($testSuitMock);
        $listener->endTestSuite($testSuitMock);
    }

    /**
     * @test
     */
    public function shouldNotCallGenerateMethodIfAtLeastOneTestFail()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());

        $testoMock = $this->createTestoMock();
        $testoMock->expects($this->never())
            ->method('generate');
        $testSuitMock = $this->createFrameworkTestSuiteMock();

        //guard
        $this->assertAttributeEquals(true, 'isSuccess', $listener);


        $listener->setTesto($testoMock);
        $listener->startTestSuite($testSuitMock);
        $listener->addFailure($this->createFrameworkTestMock(), new \PHPUnit_Framework_AssertionFailedError(), time());
        $listener->endTestSuite($testSuitMock);
    }

    /**
     * @test
     */
    public function shouldCallGenerateInRootTestSuite()
    {
        $documents = array(
            'doc1' => 'doc1',
            'doc2' => 'doc2',
            'doc3' => 'doc3'
        );
        $listener = new ReGenerateDocsOnSuccessListener($documents);
        $testSuitMock = $this->createFrameworkTestSuiteMock();

        $testoMock = $this->createTestoMock();
        $testoMock->expects($this->exactly(count($documents)))
            ->method('generate');

        $listener->setTesto($testoMock);
        $listener->startTestSuite($testSuitMock);

        //guard
        $this->assertAttributeSame($testSuitMock, 'rootSuite', $listener);
        $this->assertAttributeEquals(true, 'isSuccess', $listener);


        $listener->endTestSuite($testSuitMock);
    }

    /**
     * @test
     */
    public function shouldNotCallGenerateIfInNotRootTestSuite()
    {
        $listener = new ReGenerateDocsOnSuccessListener($documents = array());
        $testoMock = $this->createTestoMock();
        $testoMock->expects($this->never())
            ->method('generate');


        $testSuitMock = $this->createFrameworkTestSuiteMock();
        $testSuitMock2 = $this->createFrameworkTestSuiteMock();

        $listener->setTesto($testoMock);
        $listener->startTestSuite($testSuitMock);

        $this->assertAttributeNotSame($testSuitMock2, 'rootSuite', $listener);
        $this->assertAttributeEquals(true, 'isSuccess', $listener);

        $listener->endTestSuite($testSuitMock2);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Testo
     */
    protected function createTestoMock()
    {
        return $this->getMock('Testo\Testo', array('generate'));
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_Test
     */
    protected function createFrameworkTestMock()
    {
        return $this->getMock('PHPUnit_Framework_Test');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\PHPUnit_Framework_TestSuite
     */
    protected function createFrameworkTestSuiteMock()
    {
        return $this->getMock('PHPUnit_Framework_TestSuite');
    }

}
