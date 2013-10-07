<?php
namespace Testo\Tests\Source;

use Testo\Source\MethodSource;

class MethodSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldReturnArrayOfLinesOfGivenMethodsWithoutLinesWithLeadingAndTrailingBraces()
    {
        $line = '@testo Testo\Tests\files\Example helloWorld';
        $expectedContent = array(
            "        \$helloWorld = new \HelloWorld;\n",
            "\n",
            "        \$helloWorld->say();\n"
        );

        $source = new MethodSource();
        $result = $source->getContent($line);

        $this->assertEquals($expectedContent, $result);
    }

    /**
     * @test
     *
     * @expectedException \Testo\Exception\SourceNotFoundException
     * @expectedExceptionMessage Method not found
     */
    public function shouldThrowExceptionIfMethodNotFound()
    {
        $line = '@testo ClassName methodName';

        $source = new MethodSource();
        $source->getContent($line);

    }
}
