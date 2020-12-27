<?php

namespace Tests;

use Chizu\Controller\ControllerModule;
use Chizu\DI\Container;
use Chizu\Event\Events;
use Chizu\Http\Response\Response;
use Ds\Map;
use Exception;
use PHPUnit\Framework\TestCase;

class ControllerModuleTest extends TestCase
{
    protected ControllerModule $module;

    protected function setUp(): void
    {
        $this->module = new ControllerModule(new Events(), new Container(), new Map());

        $this->module->getEvents()->get(ControllerModule::InitiationEvent)->execute();
    }

    public function getTests(): array
    {
        return [
            ['test', false],
            ['testException', true]
        ];
    }


    /**
     * @dataProvider getTests
     *
     * @param string $method
     * @param bool $exception
     */
    public function testController(string $method, bool $exception): void
    {
        $context = new Map();

        $context->put('controller', TestHttpController::class);
        $context->put('method', $method);

        $this->module->getEvents()->get(ControllerModule::ControllerEvent)->execute($context);

        if (!$context->hasKey('response'))
        {
            self::fail('Context does`nt contain response');
        }

        if ($exception)
        {
            self::assertInstanceOf(Exception::class, $context->get('response'));
        }
        else
        {
            self::assertInstanceOf(Response::class, $context->get('response'));
        }
    }
}