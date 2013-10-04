<?php
namespace Testo\Tests\Filters;

use Testo\Filters\LeaveBlocksFilter;

class LeaveBlocksFilterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function shouldLeaveBlocksOnly()
    {
        $inputLines = array(
            'some code',
            'some code',
            '//@testo {',
            'some code 1',
            'some code 2',
            'some code 3',
            '//@testo }',
            'some code',
        );
        $expectedFilteredLines = array(
            'some code 1',
            'some code 2',
            'some code 3',
        );

        $filter = new LeaveBlocksFilter();
        $result = $filter->filter($inputLines);
        $this->assertEquals($expectedFilteredLines, $result);
    }

}