<?php
namespace Testo\Tests\Filter;

use Testo\Filter\UncommentFilter;

class UncommentFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return array
     */
    public function provideCodeLines()
    {
        return array(
            'uncomment inline comment' => array(
                array(
                    'some code',
                    '//@testo uncomment use Something;',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code 4',
                ),
                array(
                    'some code',
                    'use Something;',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code 4',
                )
            ),
            'uncomment multiline comment' => array(
                array(
                    'some code',
                    '//@testo uncomment {',
                    '//use Something1;',
                    '//use Something2;',
                    '//use Something3;',
                    '//@testo uncomment }',
                    'some code 3',
                    'some code 4',
                ),
                array(
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
     * @dataProvider provideCodeLines
     *
     * @test
     */
    public function shouldUncommentLines($input, $expected)
    {
        $filter = new UncommentFilter();
        $result = $filter->filter($input);

        $this->assertEquals($expected, $result);
    }
}