<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbViewRenderingFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
lmb_require('limb/net/src/lmbHttpResponse.class.php');
lmb_require('limb/web_app/src/filter/lmbViewRenderingFilter.class.php');
lmb_require('limb/web_app/src/view/lmbView.class.php');

Mock :: generate('lmbHttpResponse', 'MockHttpResponse');
Mock :: generate('lmbFilterChain', 'MockFilterChain');
Mock :: generate('lmbView', 'MockView');

class lmbViewRenderingFilterTest extends UnitTestCase
{
  var $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testRenderViewIfResponseEmpty()
  {
    $response = new MockHttpResponse();
    $response->expectOnce('isEmpty');
    $response->setReturnValue('isEmpty', true);
    $this->toolkit->setResponse($response);

    $view = new MockView();
    $this->toolkit->setView($view);

    $filter = new lmbViewRenderingFilter();

    $view->expectOnce('render');
    $view->setReturnValue('render', 'bar');
    $response->expectOnce('write', array('bar'));

    $chain = new MockFilterChain();
    $chain->expectOnce('next');

    $filter->run($chain);
  }

  function testDoNotRenderViewIfResponseNotEmpty()
  {
    $response = new MockHttpResponse();
    $response->expectOnce('isEmpty');
    $response->setReturnValue('isEmpty', false);
    $this->toolkit->setResponse($response);

    $view = new MockView();
    $this->toolkit->setView($view);

    $filter = new lmbViewRenderingFilter();

    $view->expectNever('render');
    $response->expectNever('write');

    $chain = new MockFilterChain();
    $chain->expectOnce('next');

    $filter->run($chain);
  }
}

?>