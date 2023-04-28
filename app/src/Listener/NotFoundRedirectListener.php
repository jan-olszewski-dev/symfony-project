<?php

namespace App\Listener;

use App\Attribute\NotFoundRedirect;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Routing\RouterInterface;

#[AsEventListener(event: ControllerArgumentsEvent::class)]
class NotFoundRedirectListener
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function __invoke(ControllerArgumentsEvent $event)
    {
        if (\is_array($attributes = $event->getAttributes()[NotFoundRedirect::class] ?? null)) {
            /** @var NotFoundRedirect $attribute */
            foreach ($attributes as $attribute) {
                if (!\array_key_exists($attribute->scope, $event->getNamedArguments())) {
                    $message = sprintf(
                        'Invalid using of %s attribute in %s::%s. There is no any argument with name "%s"',
                        $attribute::class,
                        \get_class($event->getController()[0]),
                        $event->getController()[1],
                        $attribute->scope
                    );

                    throw new \LogicException($message);
                }

                if (\is_null($event->getNamedArguments()[$attribute->scope])) {
                    $redirect = $this->router->generate($attribute->path);

                    return (new RedirectResponse($redirect))->send();
                }
            }
        }
    }
}
