<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRoutesDispatchTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRoutes.class.php');
lmb_require('limb/net/src/lmbUri.class.php');

class lmbRoutesDispatchTest extends UnitTestCase
{
  function setUp()
  {
    $toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testControllerAndDefaultAction()
  {
    $config = array(array('path' => '/blog',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')),
                    array('path' => '/news',
                          'defaults' => array('controller' => 'Newsline',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], 'display');

    $result = $routes->dispatch('/news');

    $this->assertEqual($result['controller'], 'Newsline');
    $this->assertEqual($result['action'], 'display');

    $this->assertEqual($routes->dispatch('/no_such_url'), array());
  }

  function testAnyController()
  {
    $config = array(array('path' => '/:controller',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog');

    $this->assertEqual($result['controller'], 'blog');
    $this->assertEqual($result['action'], 'display');

    $result = $routes->dispatch('/news');

    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'display');
  }

  function testAnyControllerAndAction()
  {
    $config = array(array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index');

    $this->assertEqual($result['controller'], 'blog');
    $this->assertEqual($result['action'], 'index');

    $result = $routes->dispatch('/blog');

    $this->assertEqual($result['controller'], 'blog');
    $this->assertEqual($result['action'], 'display');

    $result = $routes->dispatch('/news/last_news');

    $this->assertEqual($result['controller'], 'news');
    $this->assertEqual($result['action'], 'last_news');
  }

  function testConcreteControllerAndAnyAction()
  {
    $config = array(array('path' => '/blog/:action',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')),
                    array('path' => '/news/:action',
                          'defaults' => array('controller' => 'News',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], 'index');

    $result = $routes->dispatch('/blog');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], 'display');

    $result = $routes->dispatch('/news/last_news');

    $this->assertEqual($result['controller'], 'News');
    $this->assertEqual($result['action'], 'last_news');
  }

  function testUrlToMatchAll()
  {
    $config = array(array('path' => '*',
                          'defaults' => array('controller' => '404',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index');

    $this->assertEqual($result['controller'], '404');
    $this->assertEqual($result['action'], 'display');

    $result = $routes->dispatch('/path/to/heaven');

    $this->assertEqual($result['controller'], '404');
    $this->assertEqual($result['action'], 'display');
  }

  function testExtraParamAfterOthers()
  {
    $config = array(array('path' => '/:controller/:action/*additional',
                          'defaults' => array('controller' => '404',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index/and/many/params');

    $this->assertEqual($result['controller'], 'blog');
    $this->assertEqual($result['action'], 'index');
    $this->assertEqual($result['additional'], 'and/many/params');
  }

  function testExtraParamDefaultName()
  {
    $config = array(array('path' => '/:controller/:action/*',
                          'defaults' => array('controller' => '404',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/index/and/many/params');

    $this->assertEqual($result['controller'], 'blog');
    $this->assertEqual($result['action'], 'index');
    $this->assertEqual($result['extra'], 'and/many/params');
  }

  function testWithRequirements()
  {
    $config = array(array('path' => 'blog/',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')),
                    array('path' => 'blog/:year/:month/:day',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'archive',
                                              'year' => date('Y'),
                                              'month' => $default_month = date('m'),
                                              'day' => $default_day = date('d')),
                          'requirements' => array('year' => '/(19|20)\d\d/',
                                                  'month' => '/[01]?\d/',
                                                  'day' => '/[0-3]?\d/')),
                    array('path' => 'blog/:action',
                          'defaults' => array('controller' => 'Blog',
                                              'action' => 'display')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/2004/12');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], 'archive');
    $this->assertEqual($result['year'], '2004');
    $this->assertEqual($result['month'], '12');
    $this->assertEqual($result['day'], $default_day);

    $result = $routes->dispatch('/blog/2004');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], 'archive');
    $this->assertEqual($result['year'], '2004');
    $this->assertEqual($result['month'], $default_month);
    $this->assertEqual($result['day'], $default_day);

    $result = $routes->dispatch('/blog/1865');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], '1865');
    $this->assertFalse(isset($result['year']));

    $result = $routes->dispatch('/blog/last_articles');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], 'last_articles');
    $this->assertFalse(isset($result['year']));
  }

  function testApplyDispatchFilter()
  {
    $config = array(array('path' => '/:controller/:action',
                          'defaults' => array('action' => 'display'),
                          'dispatch_filter' => array($this, '_processDispatchResult')));

    $routes = new lmbRoutes($config);
    $result = $routes->dispatch('/blog/display');

    $this->assertEqual($result['controller'], 'Blog');
    $this->assertEqual($result['action'], 'display');
  }

  function _processDispatchResult(&$dispatched)
  {
    if(isset($dispatched['controller']))
      $dispatched['controller'] = toStudlyCaps($dispatched['controller']);
  }
}
?>
