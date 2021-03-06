<?php

namespace PennyTest;

use Penny\Config\Loader;
use FastRoute;
use Penny\App;
use Penny\Container;
use PHPUnit_Framework_TestCase;
use Zend\Diactoros\Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Uri;

class DiTest extends PHPUnit_Framework_TestCase
{
    private $container;

    public function setUp()
    {
        $config = Loader::load();
        $config['router'] = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $routeCollector) {
            $routeCollector->addRoute('GET', '/', ['TestApp\Controller\IndexController', 'diTest']);
        });

        $this->container = Container\PHPDiFactory::buildContainer($config);
    }

    public function testInjectionHttpFlow()
    {
        $this->container->set('troyan', 'call me');
        $app = new App($this->container);

        $request = (new Request())
        ->withUri(new Uri('/'))
        ->withMethod('GET');
        $response = new Response();

        $response = $app->run($request, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('call me', $response->getBody()->__toString());
    }
}
