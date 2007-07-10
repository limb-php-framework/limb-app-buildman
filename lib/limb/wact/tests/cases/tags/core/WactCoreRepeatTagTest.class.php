<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCoreRepeatTagTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactCoreRepeatTagTest extends WactTemplateTestCase
{
  function testRepeat()
  {
    $template = '<core:REPEAT value="3">hello!</core:REPEAT>';

    $this->registerTestingTemplate('/core/repeat.html', $template);

    $page = $this->initTemplate('/core/repeat.html');

    $this->assertEqual($page->capture(), 'hello!hello!hello!');
  }

  function testRepeatByVariable()
  {
    $template = '<core:SET count="4"><core:REPEAT value="{$count}">hello!</core:REPEAT>';

    $this->registerTestingTemplate('/core/repeat2.html', $template);

    $page = $this->initTemplate('/core/repeat2.html');

    $this->assertEqual($page->capture(), 'hello!hello!hello!hello!');
  }

  function testRepeatByDBEVariable()
  {
    $template = '<core:REPEAT value="{$count}">hello!</core:REPEAT>';

    $this->registerTestingTemplate('/core/repeat_with_dbe_value.html', $template);

    $page = $this->initTemplate('/core/repeat_with_dbe_value.html');
    $page->set('count', 4);

    $this->assertEqual($page->capture(), 'hello!hello!hello!hello!');
  }
}
?>
