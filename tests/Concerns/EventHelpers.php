<?php

namespace Tests\Concerns;

use App\Providers\EventServiceProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;

trait EventHelpers
{
    protected array $dispatchedListeners = [];

    protected array $configuredListeners = [];

    protected $firedEvent = null;

    public function expectsListeners($listeners): self
    {
        $listeners = is_array($listeners) ? $listeners : func_get_args();

        $this->mockListeners($listeners);

        $this->configuredListeners = (new EventServiceProvider($this->app))->listens();

        $this->beforeApplicationDestroyed(function () use ($listeners) {
            $fired = $this->getHandledListeners($listeners);

            $this->assertEmpty(
                $listenersNotHandled = array_diff($listeners, $fired),
                'These expected listeners were not handled: ['.implode(', ', $listenersNotHandled).']'
            );

            if (is_null($this->firedEvent)) {
                $this->assertTrue(false,
                    'No listeners were handled so we did not get the event. Use the event setter "forExpectedEvent" when there are no listeners to handle for this event.');
            } else {
                $event = is_string($this->firedEvent) ? $this->firedEvent : get_class($this->firedEvent);
                $this->assertEquals($listeners, $this->configuredListeners[$event]);
            }
        });

        return $this;
    }

    /**
     * @param  array|string  $listeners
     */
    protected function mockListeners($listeners)
    {
        foreach (Arr::wrap($listeners) as $listener) {
            $this->mock($listener, function ($mock) use ($listener) {
                $mock->shouldIgnoreMissing()
                     ->shouldReceive('handle')
                     ->withAnyArgs()
                     ->andReturnUsing(function ($event) use ($listener) {
                         $this->firedEvent = $event;
                         $this->dispatchedListeners[] = $listener;
                     });
            });
        }
    }

    protected function getHandledListeners(array $listeners): array
    {
        return $this->getHandled($listeners, $this->dispatchedListeners);
    }

    protected function getHandled(array $classes, array $dispatched): array
    {
        return array_filter($classes, function ($class) use ($dispatched) {
            return $this->wasHandled($class, $dispatched);
        });
    }

    protected function wasHandled($needle, array $haystack): bool
    {
        foreach ($haystack as $dispatched) {
            if ((is_string($dispatched) && ($dispatched === $needle || is_subclass_of($dispatched, $needle)))
                || $dispatched instanceof $needle) {
                return true;
            }
        }

        return false;
    }

    protected function forExpectedEvent($event): self
    {
        $this->firedEvent = $event;

        return $this;
    }

    /**
     * @param  string  $listener
     * @param          $event
     * @return mixed
     */
    protected function makeListenerAndCall($listener, $event)
    {
        try {
            return $this->makeListener($listener)->handle($event);
        } catch (BindingResolutionException $e) {
            //
        }
    }

    /**
     * @param  string  $listener
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function makeListener($listener)
    {
        return app()->make($listener);
    }
}
