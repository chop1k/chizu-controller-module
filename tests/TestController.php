<?php

namespace Tests;

use Ds\Map;

class TestController
{
    protected Map $context;

    public function __construct(Map $context)
    {
        $this->context = $context;
    }

    public function handle(): bool
    {
        return true;
    }
}