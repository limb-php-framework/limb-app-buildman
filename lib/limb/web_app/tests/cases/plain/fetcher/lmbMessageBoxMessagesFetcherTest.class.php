<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMessageBoxMessagesFetcherTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbMessageBoxMessagesFetcher.class.php');

class lmbMessageBoxMessagesFetcherTest extends UnitTestCase
{
  protected $toolkit;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function testGetDataset()
  {
    $this->toolkit->getMessageBox()->addMessage('Message1');
    $this->toolkit->getMessageBox()->addMessage('Message2');

    $fetcher = new lmbMessageBoxMessagesFetcher();
    $rs = $fetcher->getDataset();

    $rs->rewind();
    $this->assertEqual($rs->current()->get('message'), 'Message1');
    $rs->next();
    $this->assertEqual($rs->current()->get('message'), 'Message2');
  }

}
?>
