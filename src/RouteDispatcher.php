<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

namespace FastD\Routing;


use FastD\Middleware\Dispatcher;
use FastD\Middleware\MiddlewareInterface;
use FastD\Routing\Exceptions\RouteException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RouteDispatcher
 * @package FastD\Routing
 */
class RouteDispatcher extends Dispatcher
{
    public $route;

    /**
     * @var RouteCollection
     */
    protected $routeCollection;

    /**
     * @var array
     */
    protected $definition = [];

    /**
     * RouteDispatcher constructor.
     *
     * @param RouteCollection $routeCollection
     * @param $definition
     */
    public function __construct(RouteCollection $routeCollection, $definition = [])
    {
        $this->routeCollection = $routeCollection;

        $this->definition = $definition;

        parent::__construct([]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function dispatch(ServerRequestInterface $request)
    {
        $route = $this->routeCollection->match($request);

        // set middleware list
        foreach ($route->getMiddleware() as $middleware) {
            if ($middleware instanceof MiddlewareInterface) {
                $this->withAddMiddleware($middleware);
            } else if (is_string($middleware)) {
                if (!isset($this->definition[$middleware])) {
                    throw new \RuntimeException(sprintf('Middleware %s is not defined.'));
                }
                $definition = $this->definition[$middleware];
                if (is_array($definition)) {
                    foreach ($definition as $value) {
                        $this->withAddMiddleware(is_string($value) ? new $value : $value);
                    }
                } else {
                    $this->withAddMiddleware(is_string($definition) ? new $definition : $definition);
                }
            } else {
                throw new RouteException(sprintf('Don\'t support %s middleware', gettype($middleware)));
            }
        }

        // wrapper route middleware
        $this->withAddMiddleware(new RouteMiddleware($route));

        return parent::dispatch($request);
    }
}