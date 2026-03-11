<?php

use Psr\Clock\ClockInterface;

class PSR20 implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}