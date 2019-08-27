<?php

declare(strict_types=1);

namespace App\Service\Work\Processor\Driver;

interface ProcessorDriver
{
    public function process(string $text): string;
}
