<?php
namespace Testo\Tests\Sources;

use Testo\Sources\MethodSource;
use Testo\Testo;

class MethodSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnArrayOfLinesOfGivenMethodsWithoutLinesWithLeadingAndTrailingBraces()
    {
        $testoMock = $this->createTestoMock();
        $line = '@testo Testo\Tests\files\Example helloWorld';
        $expectedContent = array(
            "        \$helloWorld = new \HelloWorld;\n",
            "\n",
            "        \$helloWorld->say();\n"
        );

        $source = new MethodSource($testoMock);
        $result = $source->getContent($line);
        $this->assertEquals($expectedContent, $result);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Testo
     */
    protected function createTestoMock()
    {
        return $this->getMock('Testo\Testo');
    }

}