<?php
/**
 * @author    jan huang <bboyjanhuang@gmail.com>
 * @copyright 2016
 *
 * @link      https://www.github.com/janhuang
 * @link      http://www.fast-d.cn/
 */

use FastD\Routing\RouteCollection;

class RestRouteTest extends TestCase
{
    /**
     * @var RouteCollection
     */
    protected $collection;

    public function setUp()
    {
        $collection = new RouteCollection();

        $collection->addRoute('GET', '/test', []);
        $collection->addRoute('GET', '/test/{id}', []);
        $collection->addRoute('POST', '/test/{id}', []);
        $collection->addRoute('PATCH', '/test/{id}', []);
        $collection->addRoute('DELETE', '/test/{id}', []);

        $this->collection = $collection;
    }

    public function testLists()
    {
        $route = $this->collection->match($this->createRequest('GET', '/test'));

        $this->assertEquals([], $route->getParameters());
    }

    public function testDetails()
    {
        $route = $this->collection->match($this->createRequest('GET', '/test/123'));

        $this->assertEquals([
            'id' => 123
        ], $route->getParameters());

        $route = $this->collection->match($this->createRequest('GET', '/test/1,2,3'));

        $this->assertEquals([
            'id' => '1,2,3'
        ], $route->getParameters());
    }

    public function testAdd()
    {
        $route = $this->collection->match($this->createRequest('POST', '/test/123'));

        $this->assertEquals([
            'id' => 123
        ], $route->getParameters());
    }

    public function testUpdate()
    {
        $route = $this->collection->match($this->createRequest('PATCH', '/test/123'));

        $this->assertEquals([
            'id' => 123
        ], $route->getParameters());
    }

    public function testDelete()
    {
        $route = $this->collection->match($this->createRequest('DELETE', '/test/123'));

        $this->assertEquals([
            'id' => 123
        ], $route->getParameters());
    }
}
