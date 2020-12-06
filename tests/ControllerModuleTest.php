<?php

namespace Tests;

use Chizu\Controller\ControllerModule;
use Ds\Map;
use PHPUnit\Framework\TestCase;

class ControllerModuleTest extends TestCase
{
    public function testControllerEvent(): void
    {
        $module = new ControllerModule();

        $dispatcher = $module->getDispatcher();

        $dispatcher->dispatch(ControllerModule::InitiationEvent);

        $map = new Map();

        $map->put('controller', TestController::class);
        $map->put('action', 'handle');

        $result = $dispatcher->dispatch(ControllerModule::ControllerEvent, $map);

        self::assertTrue($result->hasKey('response'));
    }
}