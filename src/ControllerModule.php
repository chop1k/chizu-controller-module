<?php

namespace Chizu\Controller;

use Chizu\Controller\Exception\ContextException;
use Chizu\DI\Container;
use Chizu\DI\Dependency;
use Chizu\Event\Event;
use Chizu\Event\Events;
use Chizu\Module\Module;
use Ds\Map;
use Exception;

/**
 * Class ControllerModule provides controllers functionality.
 *
 * @package Chizu\Controller
 */
class ControllerModule extends Module
{
    public const InitiationEvent = 'controller.initiation';

    /**
     * Executes before controller event.
     */
    public const BeforeControllerEvent = 'controller.before';

    /**
     * Main event, which takes map with 'controller' and 'method' keys.
     */
    public const ControllerEvent = 'controller';

    /**
     * Executes after controller event.
     */
    public const AfterControllerEvent = 'controller.after';

    /**
     * ControllerModule constructor.
     *
     * @param Events $events
     * @param Container $container
     * @param Map $modules
     */
    public function __construct(Events $events, Container $container, Map $modules)
    {
        parent::__construct($events, $container, $modules);

        $this->events->set(self::InitiationEvent, Event::createByMethod($this, 'onInitiation'));
    }

    /**
     * Executes when initiation event dispatched.
     */
    protected function onInitiation(): void
    {
        $this->events->set(self::ControllerEvent, Event::createByMethod($this, 'onController'));
    }

    /**
     * Executes when controller event dispatched.
     *
     * @param Map $context
     * Map with any data which will be passed to controller.
     *
     * @throws ContextException
     * Throws if context doesn't contain required parameter.
     */
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

        try {
            $controller = $this->container->create(new Dependency($context->get('controller'), [
                'context' => $context
            ]));

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