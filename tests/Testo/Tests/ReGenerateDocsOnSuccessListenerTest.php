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
        self::$oldRootDir = self::getListenerPropertyValueViaReflection(null, 'rootDir');
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
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);

        $this->assertTrue($listener instanceof ReGenerateDocsOnSuccessListener);
    }

    /**
     * @test
     */
    public function shouldAllowSetTesto()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $testoMock = $this->createTestoMock();
        $listener->setTesto($testoMock);
        $actualTesto = $listener->getTesto();

        $this->assertTrue($listener->getTesto() instanceof Testo);
        $this->assertSame($testoMock, $actualTesto);
    }

    /**
     * @test
     */
    public function shouldAllowSetRootDir()
    {
        $rootDir = 'path/to/somewhere';
        ReGenerateDocsOnSuccessListener::setRootDir($rootDir);
        $actualRootDir = self::getListenerPropertyValueViaReflection(null, 'rootDir');

        $this->assertEquals($rootDir, $actualRootDir);
    }

    /**
     * @test
     */
    public function shouldSetCurrentWorkingDirectoryAsDefaultRootDirUsingInConstructor()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);

        $this->assertAttributeEquals(getcwd(), 'rootDir', $listener);
    }

    /**
     * @test
     */
    public function shouldIsSuccessBeFalseOnAddError()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        self::setListenerPropertyValueViaReflection($listener, 'isSuccess', true);
        $listener->addError($this->createFrameworkTestMock(), new \Exception(), time());

        $this->assertAttributeEquals(false, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldIsSuccessBeFalseOnAddFailure()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        self::setListenerPropertyValueViaReflection($listener, 'isSuccess', true);
        $listener->addFailure($this->createFrameworkTestMock(), new \PHPUnit_Framework_AssertionFailedError(), time());

        $this->assertAttributeEquals(false, 'isSuccess', $listener);
    }

    /**
     * @test
     */
    public function shouldDoNothingOnAddIncompleteTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->addIncompleteTest($this->createFrameworkTestMock(), new \Exception(), time());
    }

    /**
     * @test
     */
    public function shouldDoNothingOnAddSkippedTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->addSkippedTest($this->createFrameworkTestMock(), new \Exception(), time());
    }

    /**
     * @test
     */
    public function shouldDoNothingOnStartTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->startTest($this->createFrameworkTestMock());
    }

    /**
     * @test
     */
    public function shouldDoNothingOnEndTest()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $listener->endTest($this->createFrameworkTestMock(), time());
    }

    /**
     * @test
     */
    public function shouldCallGenerateMethodOnceForOneDocumentWithCorrectParams()
    {
        $listener = new ReGenerateDocsOnSuccessListener(array('path1' => 'path2'));
        $listenerRootDir = self::getListenerPropertyValueViaReflection(null, 'rootDir');
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
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);

        $testoMock = $this->createTestoMock();
        $testoMock->expects($this->never())
            ->method('generate');
        $testSuitMock = $this->createFrameworkTestSuiteMock();

        $listener->setTesto($testoMock);
        $listener->startTestSuite($testSuitMock);
        $listener->addFailure($this->createFrameworkTestMock(), new \PHPUnit_Framework_AssertionFailedError(), time());
        $listener->endTestSuite($testSuitMock);
    }

    /**
     * @test
     */
    public function shouldCallGenerateOnlyInRootTestSuite()
    {
        $listener = new ReGenerateDocsOnSuccessListener($this->documents);
        $testoMock = $this->createTestoMock();
        $testoMock->expects($this->never())
            ->method('generate');

        self::setListenerPropertyValueViaReflection($listener, 'isSuccess', true);

        $testSuitMock = $this->createFrameworkTestSuiteMock();
        $testSuitMock2 = $this->createFrameworkTestSuiteMock();

        $listener->setTesto($testoMock);
        $listener->startTestSuite($testSuitMock);
        $this->assertNotSame($testSuitMock2, self::getListenerPropertyValueViaReflection($listener, 'rootSuite'));
        $listener->endTestSuite($testSuitMock2);

        $testoMock = $this->createTestoMock();
        $testoMock->expects($this->exactly(count($this->documents)))
            ->method('generate');

        $listener->setTesto($testoMock);
        $listener->startTestSuite($testSuitMock);
        $this->assertSame($testSuitMock, self::getListenerPropertyValueViaReflection($listener, 'rootSuite'));
        $listener->endTestSuite($testSuitMock);

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

    /**
     * @param ReGenerateDocsOnSuccessListener $listener
     * @param string $propertyName
     * @param mixed $value
     */
    protected static function setListenerPropertyValueViaReflection(
        ReGenerateDocsOnSuccessListener $listener = null,
        $propertyName,
        $value
    ) {
        $rp = new \ReflectionProperty('Testo\Phpunit\ReGenerateDocsOnSuccessListener', $propertyName);
        $rp->setAccessible(true);
        $rp->setValue($listener, $value);
    }

    /**
     * @param ReGenerateDocsOnSuccessListener $listener
     * @param string $propertyName
     * @return mixed
     */
    protected static function getListenerPropertyValueViaReflection(
        ReGenerateDocsOnSuccessListener $listener = null,
        $propertyName
    ) {
        $rp = new \ReflectionProperty('Testo\Phpunit\ReGenerateDocsOnSuccessListener', $propertyName);
        $rp->setAccessible(true);
        return $rp->getValue($listener);
    }

}
