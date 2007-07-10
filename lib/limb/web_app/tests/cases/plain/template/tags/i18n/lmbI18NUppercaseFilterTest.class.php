<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NUppercaseFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
require_once('limb/i18n/utf8.inc.php');

class lmbI18NUppercaseFilterTest extends lmbWactTestCase
{
  var $prev_driver;

  function setUp()
  {
    parent :: setUp();
    $this->prev_driver = installStringsDriver(new lmbUTF8BaseDriver());
  }

  function tearDown()
  {
    installStringsDriver($this->prev_driver);
    parent :: tearDown();
  }

  function testSimple()
  {
    $template = '{$"тест"|i18n_uppercase}';

    $this->registerTestingTemplate('/limb/locale_uppercase_filter.html', $template);

    $page = $this->initTemplate('/limb/locale_uppercase_filter.html');

    $this->assertEqual($page->capture(), 'ТЕСТ');
  }

  function testDBE()
  {
    $template = '{$var|i18n_uppercase}';

    $this->registerTestingTemplate('/limb/locale_uppercase_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_uppercase_filter_dbe.html');
    $page->set('var', 'ТесТ');

    $this->assertEqual($page->capture(), 'ТЕСТ');
  }

}
?>
