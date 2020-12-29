<?php

namespace Tests;

use Chizu\Controller\ControllerModule;
use Chizu\DI\Container;
use Chizu\DI\Dependency;
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

        $container = $this->module->getContainer();

        $container->add(Container::class, new Dependency($container));

        $this->module->getEvents()->get(ControllerModule::InitiationEvent)->execute();
    }

    public function getTests(): array
    {
        return [
            ['test', false, 'test'],
            ['testException', true, '']
        ];
    }


    /**
     * @dataProvider getTests
     *
     * @param string $method
     * @param bool $exception
     * @param string $expected
     */
    public function testController(string $method, bool $exception, string $expected): void
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
            $response = $context->get('response');

            self::assertInstanceOf(Response::class, $response);
            self::assertEquals($expected, $response->getBody());
        }
    }
}