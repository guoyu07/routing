<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

class RouteCollectionTest extends TestCase
{
    public function testAddRouteToCollection()
    {
        $this->assertEquals(count($this->collection->staticRoutes), 1);
        $this->assertEquals(count($this->collection->dynamicRoutes), 2);
        $this->assertEquals(count($this->collection->dynamicRoutes['GET'][0]['routes']), 3);
        $this->assertEquals(count($this->collection->dynamicRoutes['POST'][0]['routes']), 2);
    }

    public function testRouteGetMethodMatch()
    {
        $route = $this->collection->match($this->createRequest('GET', '/'));

        $this->assertEquals('GET', $route->getMethod());
        $this->assertNull($route->getName());
        $this->assertTrue($route->isStaticRoute());
    }

    public function testRoutePostMethodMatch()
    {
        $serverRequest = $this->createRequest('POST', '/foo/bar');
        $route = $this->collection->match($serverRequest);

        $this->assertEquals('POST', $route->getMethod());
        $this->assertNull($route->getName());
        $this->assertFalse($route->isStaticRoute());
        $this->assertEquals(['name' => 'bar'], $route->getParameters());
        $this->assertEquals($serverRequest->getAttributes(), $route->getParameters());
        $this->assertEquals(['name'], $route->getVariables());
    }

    public function testRouteAnyMethodMatch()
    {
        $route = $this->collection->match($this->createRequest('POST', '/foo/bar'));
        $this->assertEquals('POST', $route->getMethod());
    }

    public function testRouteGenerator()
    {
        $url = $this->collection->generateUrl('/foo/{name}', ['name' => 'bar']);
        $this->assertEquals('/foo/bar', $url);
    }

    public function testRouteGeneratorHasSuffix()
    {
        $url = $this->collection->generateUrl('/foo/{name}', ['name' => 'bar'], 'html');
        $this->assertEquals('/foo/bar.html', $url);
    }

    public function testAddRouteName()
    {
        $route = $this->collection->get(['/default', 'name' => 'default'], []);
        $defaultRoute = $this->collection->getRoute('default');
        $this->assertEquals($route, $defaultRoute);
    }

    public function testMatchFuzzyRoute()
    {
        $request1 = $this->createRequest('GET', '/fuzzy/bar');
        $request2 = $this->createRequest('GET', '/fuzzy/foo/bar');
        $route1 = $this->collection->match($request1);
        $route2 = $this->collection->match($request2);
        $this->assertEquals($route1, $route2);

        $response = call_user_func_array($route1->getCallback(), [$request1]);
        $this->assertEquals('/bar', (string) $response->getBody());

        $response = call_user_func_array($route2->getCallback(), [$request2]);
        $this->assertEquals('/foo/bar', (string) $response->getBody());
    }
}
