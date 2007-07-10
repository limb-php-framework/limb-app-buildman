<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactSelectTagTestCase extends WactTemplateTestCase
{
  function testSelectSingle()
  {
    $template = '<form runat="server">'.
                  '<select id="test" name="mySelect" runat="server"></select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/select/selectsingle.html', $template);

    $page = $this->initTemplate('/tags/form/controls/select/selectsingle.html');
    $this->assertIsA($page->findChild('test'),'WactSelectSingleComponent');
  }

  function testSelectMultiple()
  {
    $template = '<form runat="server">'.
                  '<select id="test" name="foo[]" runat="server" multiple></select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/select/selectmultiple.html', $template);

    $page = $this->initTemplate('/tags/form/controls/select/selectmultiple.html');
    $this->assertIsA($page->findChild('test'),'WactSelectMultipleComponent');
  }

  function testSelectMultipleByName()
  {
    $template = '<form runat="server">'.
                  '<select name="test[]" runat="server" multiple></select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/select/selectmultiplebyname.html', $template);

    $page = $this->initTemplate('/tags/form/controls/select/selectmultiplebyname.html');
    $this->assertIsA($page->findChild('test'),'WactSelectMultipleComponent');

  }

  function testSelectMultipleControlArrayRequired()
  {
    $template = '<form runat="server">'.
                  '<select id="test" name="mySelect" runat="server" multiple></select>'.
                '</form>';
    $this->registerTestingTemplate('/tags/form/controls/select/selectmultiplecontrolarray.html', $template);

    try
    {
      $page = $this->initTemplate('/tags/form/controls/select/selectmultiplecontrolarray.html');
      $this->assertTrue(false);
    }
    catch(WactException $e)
    {
      $this->assertWantedPattern('/Array required in name attribute/', $e->getMessage());
    }
  }
}
?>
