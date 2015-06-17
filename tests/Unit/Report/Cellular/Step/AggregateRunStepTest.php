<?php

/*
 * This file is part of the PHP Bench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpBench\Tests\Unit\Report\Cellular\Step;

use DTL\Cellular\Workspace;
use PhpBench\Report\Cellular\Step\AggregateRunStep;

class AggregateRunStepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * It should aggregate all rows in the group "aggregate"
     * It should add a column for each function for each aggregated field
     * It should retain the title and description of the original tables.
     */
    public function testAggregate()
    {
        $workspace = Workspace::create();
        $table = $workspace->createAndAddTable();
        $table->createAndAddRow()
            ->set('run', 0)
            ->set('revs', 100)
            ->set('params', '[]')
            ->set('a', 10, array('aggregate'))
            ->set('b', 10, array('aggregate'));

        $table->createAndAddRow()
            ->set('run', 0)
            ->set('revs', 100)
            ->set('params', '[]')
            ->set('a', 90, array('aggregate'))
            ->set('b', 4, array('aggregate'));

        $step = new AggregateRunStep(array('min', 'max'));
        $step->step($workspace);

        $this->assertCount(1, $workspace->getTables());
        $table = $workspace[0];
        $this->assertCount(1, $table->getRows());
        $row = $table->getRow(0);
        $this->assertCount(10, $row->getCells());
        $this->assertCount(4, $row->getCells(array('aggregate')));
        $this->assertEquals(10, $row->getCell('min_a')->getValue());
        $this->assertEquals(90, $row->getCell('max_a')->getValue());
        $this->assertEquals(800, $row->getCell('variance_a')->getValue());
    }
}