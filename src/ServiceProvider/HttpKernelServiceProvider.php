<?php

namespace SlackUnfurl\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\EventListener\RouterListener;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @see https://symfony.com/doc/current/4.4/http_kernel.html#a-full-working-example
 */
class HttpKernelServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app): void
    {
        $app['debug'] = false;

        $app[RouteCollection::class] = new RouteCollection();
        $app[Request::class] = Request::createFromGlobals();

        $matcher = new UrlMatcher($app[RouteCollection::class], new RequestContext());

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new RouterListener($matcher, new RequestStack()));

        $controllerResolver = new ControllerResolver();
        $argumentResolver = new ArgumentResolver();

        $app[HttpKernel::class] = new HttpKernel($dispatcher, $controllerResolver, new RequestStack(), $argumentResolver);

        $app['dispatcher'] = $dispatcher;
    }
}