<?php
namespace Testo\Tests\Sources;

use Testo\Sources\MethodSource;

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
}
