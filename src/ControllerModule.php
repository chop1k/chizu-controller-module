<?php

namespace Chizu\Controller;

use Chizu\Controller\Exception\ContextException;
use Chizu\Event\Event;
use Chizu\Module\Module;
use Ds\Map;

class ControllerModule extends Module
{
    public const BeforeControllerEvent = 'controller.before';
    public const ControllerEvent = 'controller';
    public const AfterControllerEvent = 'controller.after';

    public function __construct()
    {
        parent::__construct();

        $this->dispatcher->set(self::InitiationEvent, new Event([function () {
            $this->onInitiation();
        }]));
    }

    protected function onInitiation(): void
    {
        $this->dispatcher->set(self::ControllerEvent, new Event([function (Map $context) {
            return $this->onController($context);
        }], true));
    }

    protected function onController(Map $context): Map
    {
        if ($this->dispatcher->has(self::BeforeControllerEvent))
        {
            $this->dispatcher->dispatch(self::BeforeControllerEvent, $context);
        }

        if (!$context->hasKey('controller'))
        {
            throw new ContextException('Context does`nt contain required parameter "controller"');
        }

        $class = $context->get('controller');

        $controller = new $class($context);

        if (!$context->hasKey('action'))
        {
            throw new ContextException('Context does`nt contain required parameter "action"');
        }

        $context->put('response', $controller->{$context->get('action')}());

        if ($this->dispatcher->has(self::AfterControllerEvent))
        {
            $this->dispatcher->dispatch(self::AfterControllerEvent, $context);
        }

        return $context;
    }
}