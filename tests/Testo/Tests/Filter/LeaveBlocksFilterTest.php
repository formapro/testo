<?php
namespace Testo\Tests\Filter;

use Testo\Filter\LeaveBlocksFilter;

class LeaveBlocksFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function provideCodeLines()
    {
        return array(
            'regular block' => array(
                array(
                    'some code',
                    'some code',
                    '//@testo {',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    '//@testo }',
                    'some code',
                ),
                array(
                    'some code 1',
                    'some code 2',
                    'some code 3',
                )
            ),
            'code without block' => array(
                array(
                    'some code',
                    'some code',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code',
                ),
                array(
                    'some code',
                    'some code',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code',
                )
            ),
            'only open block tag' => array(
                array(
                    'some code',
                    'some code',
                    '//@testo {',
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code',
                ),
                array(
                    'some code 1',
                    'some code 2',
                    'some code 3',
                    'some code',
                )
            ),

        );
    }

    /**
     * @test
     *
     * @dataProvider provideCodeLines
     */
    public function shouldLeaveBlocksOnly($input, $expected)
    {
        $filter = new LeaveBlocksFilter();
        $result = $filter->filter($input);

        $this->assertEquals($expected, $result);
    }
}
