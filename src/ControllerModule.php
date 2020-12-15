<?php

namespace Chizu\Controller;

use Chizu\Controller\Exception\ContextException;
use Chizu\Event\Event;
use Chizu\Module\Module;
use Ds\Map;
use Exception;

class ControllerModule extends Module
{
    public const BeforeControllerEvent = 'controller.before';
    public const ControllerEvent = 'controller';
    public const AfterControllerEvent = 'controller.after';

    public function __construct()
    {
        parent::__construct();

        $this->events->set(self::InitiationEvent, Event::createByMethod($this, 'onInitiation'));
    }

    protected function onInitiation(): void
    {
        $this->events->set(self::ControllerEvent, Event::createByMethod($this, 'onController'));
    }

    protected function onController(Map $context): void
    {
        if ($this->events->has(self::BeforeControllerEvent))
        {
            $this->events->get(self::BeforeControllerEvent)->execute($context);
        }

        if (!$context->hasKey('controller'))
        {
            throw new ContextException('Context does`nt contain required parameter "controller"');
        }

        if (!$context->hasKey('method'))
        {
            throw new ContextException('Context does`nt contain required parameter "method"');
        }

        $class = $context->get('controller');

        try {
            $controller = new $class($context);

            $context->put('response', $controller->{$context->get('method')}());
        } catch (Exception $exception) {
            $context->put('response', $exception);
        }

        if ($this->events->has(self::AfterControllerEvent))
        {
            $this->events->get(self::AfterControllerEvent)->execute($context);
        }
    }
}