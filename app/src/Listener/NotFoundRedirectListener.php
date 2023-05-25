<?php

namespace App\Listener;

use App\Attribute\NotFoundRedirect;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\Routing\RouterInterface;

/** @SuppressWarnings(PHPMD.MissingImport) */
#[AsEventListener(event: ControllerArgumentsEvent::class)]
class NotFoundRedirectListener
{
    public function __construct(private RouterInterface $router)
    {
    }

    public function __invoke(ControllerArgumentsEvent $event): ?Response
    {
        $attributes = $event->getAttributes()[NotFoundRedirect::class] ?? null;

        if (\is_array($attributes)) {
            /** @var NotFoundRedirect $attribute */
            foreach ($attributes as $attribute) {
                $this->validAttributeUsage($attribute, $event);

                if (\is_null($event->getNamedArguments()[$attribute->scope])) {
                    $redirect = $this->router->generate($attribute->path);

                    $event->setController(function() use ($redirect) {
                        return new RedirectResponse($redirect);
                    });
                }
            }
        }

        return null;
    }

    private function validAttributeUsage(NotFoundRedirect $attribute, ControllerArgumentsEvent $event): void
    {
        if (!\array_key_exists($attribute->scope, $event->getNamedArguments())) {
            $controller = $event->getController();
            $representative = \is_array($controller) ?
                sprintf('%s::%s', \get_class($controller[0]), $controller[1]) :
                get_debug_type($controller);

            $message = sprintf(
                'Invalid using of %s attribute in %s. There is no any argument with name "%s"',
                $attribute::class,
                $representative,
                $attribute->scope
            );

            throw new \LogicException($message);
        }
    }
}
