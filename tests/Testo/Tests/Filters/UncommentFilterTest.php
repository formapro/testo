<?php
namespace Testo\Tests\Filters;

use Testo\Filters\UncommentFilter;

class UncommentFilterTest extends \PHPUnit_Framework_TestCase
{

    public function dataProvider()
    {
        return array(
            array(
                $inputLines = array(
                    'some code',
                    '//@testo uncomment use Something;',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code 4',
                ),
                $expectedFilteredLines = array(
                    'some code',
                    'use Something;',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code 4',
                )
            ),
            array(
                $inputLines = array(
                    'some code',
                    '//@testo uncomment {',
                    '//use Something1;',
                    '//use Something2;',
                    '//use Something3;',
                    '//@testo uncomment }',
                    'some code 3',
                    'some code 4',
                ),
                $expectedFilteredLines = array(
                    'some code',
                    'use Something1;',
                    'use Something2;',
                    'use Something3;',
                    'some code 3',
                    'some code 4',
                )
            ),


        );
    }

    /**
     * @dataProvider dataProvider
     *
     * @test
     */
    public function shouldUncommentLines($input, $expected)
    {
        $inputLines = array(
            'some code',
            '//@testo uncomment use Something;',
            'some code 1',
            'some code 2',
            'some code 3',
            'some code 4',
        );
        $expectedFilteredLines = array(
            'some code',
            'use Something;',
            'some code 1',
            'some code 2',
            'some code 3',
            'some code 4',
        );

        $filter = new UncommentFilter();
        $result = $filter->filter($input);
        $this->assertEquals($expected, $result);
    }
}