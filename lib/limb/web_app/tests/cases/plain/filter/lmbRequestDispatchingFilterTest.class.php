<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequestDispatchingFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/web_app/src/filter/lmbRequestDispatchingFilter.class.php');
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');
lmb_require('limb/toolkit/src/lmbMockToolsWrapper.class.php');
lmb_require('limb/web_app/src/toolkit/lmbWebAppTools.class.php');
lmb_require('limb/web_app/src/controller/lmbStaticCommandController.class.php');

Mock :: generate('lmbFilterChain', 'MockFilterChain');
Mock :: generate('lmbRequestDispatcher', 'MockRequestDispatcher');
Mock :: generate('lmbWebAppTools', 'MockWebAppTools');

class lmbRequestDispatchingTestingController extends lmbStaticCommandController
{
  function __construct($name)
  {
    $this->name = $name;
    parent :: __construct();
  }

  protected function _defineActions()
  {
    return array('display' => array());
  }
}

//this class used to test exceptions since SimpleTest does not support exception generation by mocks yet.
class lmbRequestDispatchingFilterTestTools extends lmbAbstractTools
{
  protected $exception_controller_name;
  protected $controller;

  function __construct($exception_controller_name)
  {
    $this->exception_controller_name = $exception_controller_name;
  }

  function setController($controller)
  {
    $this->controller = $controller;
  }

  function createController($controller_name)
  {
    if($controller_name == $this->exception_controller_name)
      throw new lmbException('Controller not created!');
    else
      return $this->controller;
  }
}

class lmbRequestDispatchingFilterTest extends UnitTestCase
{
  protected $toolkit;
  protected $request;
  protected $mock_tools;
  protected $dispatcher;
  protected $filter;
  protected $chain;

  function setUp()
  {
    $this->mock_tools = new MockWebAppTools();
    $tools = new lmbMockToolsWrapper($this->mock_tools, array('createController'));

    lmbToolkit :: save();
    $this->toolkit = lmbToolkit :: merge($tools);
    $this->request = $this->toolkit->getRequest();

    $this->dispatcher = new MockRequestDispatcher();
    $this->filter = new lmbRequestDispatchingFilter($this->dispatcher);
    $this->chain = new MockFilterChain();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  protected function _setUpMocks($dispatched_params, $controller = null, $default_controller_name = '')
  {
    $this->chain->expectOnce('next');

    $this->dispatcher->expectOnce('dispatch', array($this->request));
    $this->dispatcher->setReturnValue('dispatch', $dispatched_params);

    if($controller)
    {
      $this->mock_tools->expectArgumentsAt(0, 'createController', array($controller->getName()));
      $this->mock_tools->setReturnValueAt(0, 'createController', $controller, array($controller->getName()));
    }
  }

  function testSetDispatchedRequestInToolkit()
  {
    $controller = new lmbRequestDispatchingTestingController($controller_name = 'SomeController');

    $dispatched_params = array('controller' => $controller_name,
                               'action' => 'display');

    $this->_setUpMocks($dispatched_params, $controller);

    $this->filter->run($this->chain);

    $this->assertDispatchedOk($controller, 'display', __LINE__);
  }

  function testUseDefaultActionFromControllerIsActionWasNotDispatchedFromRequest()
  {
    $dispatched_params = array('controller' => $controller_name = 'SomeController');

    $controller = new lmbRequestDispatchingTestingController($controller_name);

    $this->_setUpMocks($dispatched_params, $controller);

    $this->filter->run($this->chain);

    $this->assertDispatchedOk($controller, $controller->getDefaultAction(), __LINE__);
  }

  function testUse404ControllerIsNoSuchActionInDispatchedController()
  {
    $dispatched_params = array('controller' => $controller_name = 'SomeController',
                               'action' => 'no_such_action');

    $controller = new lmbRequestDispatchingTestingController($controller_name);

    $this->_setUpMocks($dispatched_params, $controller);

    $not_found_controller = new lmbRequestDispatchingTestingController('404');

    $this->mock_tools->expectArgumentsAt(1, 'createController', array('404'));
    $this->mock_tools->setReturnValueAt(1, 'createController', $not_found_controller, array('404'));

    $this->filter->setDefaultControllerName('404');
    $this->filter->run($this->chain);

    $this->assertDispatchedOk($not_found_controller, $not_found_controller->getDefaultAction(), __LINE__);
  }

  function testControllerParamIsEmpty()
  {
    $this->filter->setDefaultControllerName('404');

    $dispatched_params = array('id' => 150);

    $controller = new lmbRequestDispatchingTestingController('404');

    $this->_setUpMocks($dispatched_params, $controller);

    $this->filter->run($this->chain);

    $this->assertDispatchedOk($controller, 'display', __LINE__);
  }

  function testNoSuchController()
  {
    $this->filter->setDefaultControllerName($default_controller_name = '404');

    $dispatched_params = array('controller' => $exception_controller_name = 'no_such_controller'. time());

    $this->_setUpMocks($dispatched_params);

    $tools = new lmbRequestDispatchingFilterTestTools($exception_controller_name);
    $tools->setController($controller = new lmbRequestDispatchingTestingController($default_controller_name));

    $this->toolkit = lmbToolkit :: merge($tools);

    $this->filter->run($this->chain);

    $this->assertDispatchedOk($controller, 'display', __LINE__);
  }

  function testPutOtherParamsToRequest()
  {
    $dispatched_params = array('controller' => 'SomeController',
                               'id' => 150,
                               'extra' => 'bla-bla');

    $controller = new lmbRequestDispatchingTestingController('SomeController');
    $this->_setUpMocks($dispatched_params, $controller);

    $this->filter->run($this->chain);

    $this->assertDispatchedOk($controller, $controller->getDefaultAction(), __LINE__);

    $this->assertEqual($this->request->get('id'), 150);
    $this->assertEqual($this->request->get('extra'), 'bla-bla');
  }

  function assertDispatchedOk($controller, $action, $line)
  {
    $dispatched_request = $this->toolkit->getDispatchedController();
    $this->assertEqual($dispatched_request->getName(), $controller->getName(), '%s ' . $line);
    $this->assertEqual($dispatched_request->getCurrentAction(), $action, '%s ' . $line);
  }
}

?>