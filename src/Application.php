<?php

namespace SlackUnfurl;

use Pimple\Container;
use Psr\Log\LoggerInterface;
use Silex\Provider\MonologServiceProvider;
use SlackUnfurl\Controller\InfoController;
use SlackUnfurl\Controller\UnfurlController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Throwable;

class Application extends Container
{
    private const NAME = 'unfurl';

    /**
     * Instantiate a new Application.
     *
     * Objects and parameters can be passed as argument to the constructor.
     *
     * @param array $values the parameters or objects
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->setupKernel();
        /*
        $this->setupErrorHandler();
         */
        $this->registerProviders();
        $this->configureRoutes();
    }

    private function setupKernel(): void
    {
        $this->register(new ServiceProvider\HttpKernelServiceProvider());
    }

    private function registerProviders(): void
    {
        $this->register(new ServiceProvider\ServiceProvider());
        $this->register(new ServiceProvider\CommandProvider());
        $this->register(new MonologServiceProvider(), [
            'monolog.name' => self::NAME,
            'monolog.logfile' => "{$this['appDir']}/var/log/{$this['env']}.log",
            'monolog.use_error_handler' => true,
        ]);
    }

    private function configureRoutes(): void
    {
        $this->post($this['unfurl.app_prefix'], function (Request $request) {
            return $this[UnfurlController::class]($request);
        });
        $this->get($this['unfurl.app_prefix'], function (Request $request) {
            return $this[InfoController::class]($request);
        });
        $this->get('/favicon.ico', static function () {
            return new Response('', 404);
        });
    }

    /**
     * Maps a GET request to a callable.
     *
     * @param string $path The path pattern to match
     * @param callable $callable Callback that returns the response when matched
     */
    public function get($path, $callable): void
    {
        $this->addRoute('get', $path, $callable);
    }

    /**
     * Maps a POST request to a callable.
     *
     * @param string $path The path pattern to match
     * @param callable $callable Callback that returns the response when matched
     **/
    public function post($path, $callable): void
    {
        $this->addRoute('post', $path, $callable);
    }

    private function addRoute($method, $path, $callable): void
    {
        /** @var RouteCollection $routes */
        $routes = $this[RouteCollection::class];

        $defaults = [
            '_controller' => $callable,
        ];
        $route = new Route($path, $defaults);
        $route->setMethods($method);
        $name = $method . $path;
        $routes->add($name, $route);
    }

    private function setupErrorHandler(): void
    {
        $this->error(function (Throwable $e, Request $request, $code) {
            /** @var LoggerInterface $logger */
            $logger = $this['logger'];
            $logger->error($e->getMessage(), ['exception' => $e, 'trace' => $e->getTraceAsString()]);

            return new JsonResponse('Internal Error', $code);
        });
    }

    public function run(): void
    {
        /** @var HttpKernel $kernel */
        $kernel = $this[HttpKernel::class];
        /** @var Request $request */
        $request = $this[Request::class];

        $response = $kernel->handle($request);
        $response->send();

        $kernel->terminate($request, $response);
    }
}