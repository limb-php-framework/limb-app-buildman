<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactFormErrorsTagTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */


class WactFormErrorsTagTest extends WactTemplateTestCase
{
  function testProperNesting()
  {
    $template = '<form:errors target="errors"><list:list id="errors"></list>';

    $this->registerTestingTemplate('/form/form_errors/proper_nesting.html', $template);

    try
    {
      $page = $this->initTemplate('/form/form_errors/proper_nesting.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testPassToAttributeIsRequired()
  {
    $template = '<form runat="server"><form:errors><list:list id="errors"></list:list></form>';

    $this->registerTestingTemplate('/form/form_errors/target_required.html', $template);

    try
    {
      $page = $this->initTemplate('/form/form_errors/target_required.html');
      $this->assertTrue(false);
    }
    catch(WactException $e){}
  }

  function testErrorsPassedToListListTag()
  {
    $template = '<form id="my_form" runat="server"><form:errors target="errors">'.
                '<list:list id="errors"><list:item>{$message}</list:item></list:list>'.
                '</form>';

    $this->registerTestingTemplate('/form/form_errors/passed_to_list.html', $template);

    $page = $this->initTemplate('/form/form_errors/passed_to_list.html');

    $error_list = new WactFormErrorList();
    $error_list->addError('Error1 text');
    $error_list->addError('Error2 text');

    $form = $page->getChild("my_form");
    $form->setErrors($error_list);

    $this->assertEqual($page->capture(), '<form id="my_form">Error1 textError2 text</form>');
  }
}
?>
