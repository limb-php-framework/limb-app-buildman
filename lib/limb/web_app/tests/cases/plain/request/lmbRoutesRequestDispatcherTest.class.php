<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRoutesRequestDispatcherTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRoutesRequestDispatcher.class.php');
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');

class lmbRoutesRequestDispatcherTest extends UnitTestCase
{
  protected $request;
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testDispatch()
  {
    $config_array = array(array('path' => '/:controller/:action',
                                'defaults' => array('action' => 'display')));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->parse('/news');

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'display');
  }

  function testUseActionFromRequestEvenIfMatchedByRoutes()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $this->request->getUri()->parse('/news/display');
    $this->request->set('action', 'admin_display'); // !!!

    $dispatcher = new lmbRoutesRequestDispatcher();
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'admin_display');
  }


  function testNormalizeUrl()
  {
    $config_array = array(array('path' => '/:controller/:action'));
    $routes = new lmbRoutes($config_array);
    $this->toolkit->setRoutes($routes);

    $dispatcher = new lmbRoutesRequestDispatcher();

    $this->request->getUri()->parse('/news/admin_display');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'admin_display');

    $this->request->getUri()->parse('/blog////index');
    $result = $dispatcher->dispatch($this->request);

    $this->assertEqual($result['controller'], 'blog');
    $this->assertEqual($result['action'], 'index');

    $this->request->getUri()->parse('/blog/../bar/index/');
    $result = $dispatcher->dispatch($this->request);
    $this->assertEqual($result['controller'], 'bar');
    $this->assertEqual($result['action'], 'index');
  }

}

?>
