<?php

/*
 * This file is part of the PHPBench package
 *
 * (c) Daniel Leech <daniel@dantleech.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace PhpBench\Progress\Logger;

use PhpBench\Model\Benchmark;
use PhpBench\Model\Iteration;
use PhpBench\Model\Variant;

class VerboseLogger extends PhpBenchLogger
{
    /**
     * @var int
     */
    private $rejectionCount = 0;

    /**
     * {@inheritdoc}
     */
    public function benchmarkStart(Benchmark $benchmark)
    {
        $this->output->writeln(sprintf('<comment>%s</comment>', $benchmark->getClass()));
        $this->output->write(PHP_EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function benchmarkEnd(Benchmark $benchmark)
    {
        $this->output->write(PHP_EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function iterationStart(Iteration $iteration)
    {
        $this->output->write(sprintf(
            "\x1B[0G    %-30s%sI%s P%s ",
            $iteration->getVariant()->getSubject()->getName(),
            $this->rejectionCount ? 'R' . $this->rejectionCount . ' ' : '',
            $iteration->getIndex(),
            $iteration->getVariant()->getParameterSet()->getIndex()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function variantStart(Variant $variant)
    {
        $this->paramSetIndex = $variant->getParameterSet()->getIndex();
    }

    /**
     * {@inheritdoc}
     */
    public function variantEnd(Variant $variant)
    {
        if ($variant->hasErrorStack()) {
            $this->output->write(sprintf(
                "\x1B[0G    %-30s<error>ERROR</error>",
                $variant->getSubject()->getName()
            ));
            $this->output->write(PHP_EOL);

            return;
        }

        $this->output->write(sprintf("\t%s", $this->formatIterationsFullSummary($variant)));
        $this->output->write(PHP_EOL);
    }

    /**
     * {@inheritdoc}
     */
    public function retryStart($rejectionCount)
    {
        $this->rejectionCount = $rejectionCount;
        $this->output->write("\x1B[1F\x1B[0K");
    }
}
