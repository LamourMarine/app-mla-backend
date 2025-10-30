<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class JwtAuthorizationListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->headers->has('Authorization')) {
            if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $request->headers->set('Authorization', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
            }
        }
    }
}
file_put_contents('/tmp/debug_auth.log', print_r($_SERVER, true));

