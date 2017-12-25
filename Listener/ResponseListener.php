<?php
/**
 * Created by PhpStorm.
 * User: maxim
 * Date: 25.10.17 1:15
 */


namespace Makm\GuestBundle\Listener;

use Makm\GuestBundle\Security\Listener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseListener implements EventSubscriberInterface
{
    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {

        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        if ($request->attributes->has(Listener::REMEMBER_GUEST_PARAM_KEY_COOKIE)) {
            $response->headers->setCookie($request->attributes->get(Listener::REMEMBER_GUEST_PARAM_KEY_COOKIE));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => 'onKernelResponse'];
    }
}
