<?php

declare(strict_types=1);

namespace App\Service\Work\Processor;

use App\Service\Work\Processor\Driver\ProcessorDriver;

class Processor
{
    /**
     * @var ProcessorDriver[]
     */
    private $drivers;

    public function __construct(iterable $drivers)
    {
        $this->drivers = $drivers;
    }

    public function process(?string $text): string
    {
        $result = $text;
        foreach ($this->drivers as $driver) {
            $result = $driver->process($result);
        }
        return $result;
    }
}
