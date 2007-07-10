<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NCapitalizeFilterTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
require_once('limb/i18n/utf8.inc.php');

class lmbI18NCapitalizeFilterTest extends lmbWactTestCase
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
    $template = '{$"тест"|i18n_capitalize}';

    $this->registerTestingTemplate('/limb/locale_capitalize_filter.html', $template);

    $page = $this->initTemplate('/limb/locale_capitalize_filter.html');

    $this->assertEqual($page->capture(), 'Тест');
  }

  function testDBE()
  {
    $template = '{$var|i18n_capitalize}';

    $this->registerTestingTemplate('/limb/locale_capitalize_filter_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_capitalize_filter_dbe.html');
    $page->set('var', 'тест');

    $this->assertEqual($page->capture(), 'Тест');
  }

  function testPathBasedDBE()
  {
    $template = '{$my.var|i18n_capitalize}';

    $this->registerTestingTemplate('/limb/locale_capitalize_filter_path_based_dbe.html', $template);

    $page = $this->initTemplate('/limb/locale_capitalize_filter_path_based_dbe.html');
    $my_dataspace = new lmbDataspace(array('var' => 'тест'));
    $page->set('my', $my_dataspace);

    $this->assertEqual($page->capture(), 'Тест');
  }
}
?>
