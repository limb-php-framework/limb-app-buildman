<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: form.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');
require_once('limb/wact/src/components/form/form.inc.php');

class WactFormTagTestCase extends WactTemplateTestCase
{
  function testHasNoErrors()
  {
    $form = new WactFormComponent('my_id');
    $this->assertFalse($form->hasErrors());
  }

  function testHasErrors()
  {
    $error_list = new WactFormErrorList();
    $errorFields = array(
            'x'=>'Input1',
            'z'=>'Input3'
    );
    $error_list->addError('message', $errorFields);

    $form = new WactFormComponent('my_id');
    $form->setErrors($error_list);
    $this->assertTrue($form->hasErrors());
  }

  function testPrepare()
  {
    $form = new WactFormComponent('my_id');
    $form->prepare();
    $this->assertIsA($form->getDataSource(), 'ArrayObject');
  }

  /**
  * Should test registerFilter, prepare and registerDataSource but lazy right now...
  */

  function testFindLabel()
  {
    $template = '<form id="testForm" runat="server">
                    <label id="testLabel" for="testId" runat="server">A label</label>
                </form>';
    $this->registerTestingTemplate('/components/form/findlabel.html', $template);

    $page = $this->initTemplate('/components/form/findlabel.html');

    $form = $page->getChild('testForm');
    $label = $page->getChild('testLabel');

    $this->assertReference($form->findLabel('testId', $form), $label);
  }

  function testFindLabelNotFound()
  {
    $template = '<form id="testForm" runat="server">
                    <label id="testLabel" for="testId" runat="server">A label</label>
                </form>';
    $this->registerTestingTemplate('/components/form/findlabelnotfound.html', $template);

    $page = $this->initTemplate('/components/form/findlabelnotfound.html');

    $form = $page->getChild('testForm');

    $this->assertFalse($form->findLabel('foo',$form));
  }

  function testSetErrorsReference()
  {
    $error_list = new WactFormErrorList();
    $form = new WactFormComponent('my_id');
    $form->setErrors($error_list);
    $this->assertReference($error_list, $form->getErrorDataSet());
  }

  function testSetErrors()
  {
    $error_list = new WactFormErrorList();
    $errorFields = array(
            'x'=>'Input1',
            'z'=>'Input3'
    );

    $error_list->addError('message', $errorFields);

    $template = '<form id="testForm" runat="server">
                    <label id="Label1" for="Input1" class="Normal" errorclass="Error" runat="server">A label</label>
                    <input id="Input1" type="text" runat="server">
                    <label id="Label2" for="Input2" class="Normal" errorclass="Error" runat="server">A label</label>
                    <input id="Input2" type="text" runat="server">
                    <label id="Label3" for="Input3" class="Normal" errorclass="Error" runat="server">A label</label>
                    <input name="Input3" type="text" runat="server">
                </form>';

    $this->registerTestingTemplate('/components/form/seterrors.html', $template);

    $page = $this->initTemplate('/components/form/seterrors.html');

    $form = $page->getChild('testForm');

    $form->setErrors($error_list);

    $label1 = $page->getChild('Label1');
    $this->assertEqual($label1->getAttribute('class'),'Error');
    $Input1 = $page->getChild('Input1');
    $this->assertTrue($Input1->hasErrors());

    $label2 = $page->getChild('Label2');
    $this->assertEqual($label2->getAttribute('class'),'Normal');
    $Input2 = $page->getChild('Input2');
    $this->assertFalse($Input2->hasErrors());

    $label3 = $page->getChild('Label3');
    $this->assertEqual($label3->getAttribute('class'),'Normal');
    $Input3 = $page->getChild('Input3');
    $this->assertTrue($Input3->hasErrors());
 }

  function testSetErrorsTricky()
  {
    $error_list = new WactFormErrorList();
    $errorFields = array(
        'a' => 'InputText',
        'b' => 'Select1',
        'c' => 'Select2',
        'd' => 'Select3',
        'e' => 'Select4',
        'f' => 'InputCheckbox2',
    );
    $error_list->addError('message',$errorFields);

    $template = '<form id="testForm" runat="server">

                    <label id="Label1" for="InputText" class="Normal" errorclass="Error" runat="server">A label</label>
                    <input id="InputText" type="text" runat="server">

                    <label id="Label2" for="Select1" class="Normal" errorclass="Error" runat="server">A label</label>
                    <select id="Select1" name="mySelect1" type="text" runat="server"></select>

                    <label id="Label3" for="Select2" class="Normal" errorclass="Error" runat="server">A label</label>
                    <select name="Select2" type="text" runat="server"></select>

                    <label id="Label4" for="Select3" class="Normal" errorclass="Error" runat="server">A label</label>
                    <select id="Select3" name="Test[]" type="text" runat="server" multiple></select>

                    <label id="Label5" for="Select4" class="Normal" errorclass="Error" runat="server">A label</label>
                    <select name="Select4[]" type="text" runat="server" multiple></select>

                    <label id="Label6" for="InputCheckbox1" class="Normal" errorclass="Error" runat="server">A label</label>
                    <label id="Label7" for="InputCheckbox2" class="Normal" errorclass="Error" runat="server">A label</label>
                    <label id="Label8" for="InputCheckbox3" class="Normal" errorclass="Error" runat="server">A label</label>
                    <input id="InputCheckbox1" type="checkbox" name="test" value="x" checked runat="server">
                    <input id="InputCheckbox2" type="checkbox" name="test" value="y" runat="server">
                    <input id="InputCheckbox3" type="checkbox" name="test" value="z" runat="server">

                </form>';

    $this->registerTestingTemplate('/components/form/seterrorstricky.html', $template);

    $page = $this->initTemplate('/components/form/seterrorstricky.html');

    $form = $page->getChild('testForm');

    $form->setErrors($error_list);

    $label1 = $page->getChild('Label1');
    $this->assertEqual($label1->getAttribute('class'),'Error');
    $InputText = $page->getChild('InputText');
    $this->assertTrue($InputText->hasErrors());

    $label2 = $page->getChild('Label2');
    $this->assertEqual($label2->getAttribute('class'),'Error');
    $Select1 = $page->getChild('Select1');
    $this->assertTrue($Select1->hasErrors());

    $label3 = $page->getChild('Label3');
    $this->assertEqual($label3->getAttribute('class'),'Normal');
    $Select2 = $page->getChild('Select2');
    $this->assertTrue($Select2->hasErrors());

    $label4 = $page->getChild('Label4');
    $this->assertEqual($label4->getAttribute('class'),'Error');
    $Select3 = $page->getChild('Select3');
    $this->assertTrue($Select3->hasErrors());

    $label5 = $page->getChild('Label5');
    $this->assertEqual($label5->getAttribute('class'),'Normal');
    $Select4 = $page->getChild('Select4');
    $this->assertTrue($Select4->hasErrors());

    $label6 = $page->getChild('Label6');
    $this->assertEqual($label6->getAttribute('class'),'Normal');
    $InputCheckbox1 = $page->getChild('InputCheckbox1');
    $this->assertFalse($InputCheckbox1->hasErrors());

    $label7 = $page->getChild('Label7');
    $this->assertEqual($label7->getAttribute('class'),'Error');
    $InputCheckbox2 = $page->getChild('InputCheckbox2');
    $this->assertTrue($InputCheckbox2->hasErrors());

    $label8 = $page->getChild('Label8');
    $this->assertEqual($label8->getAttribute('class'),'Normal');
    $InputCheckbox3 = $page->getChild('InputCheckbox3');
    $this->assertFalse($InputCheckbox3->hasErrors());
  }

  function testPreserveState()
  {
    $data = new WactArrayObject();
    $data->set('x','a');
    $data->set('y','b');
    $data->set('z','x < z');

    $form = new WactFormComponent('my_id');
    $form->registerDataSource($data);

    $form->preserveState('x');
    $form->preserveState('z');

    ob_start();
    $form->renderState();
    $result = ob_get_contents();
    ob_end_clean();

    $test = '<input type="hidden" name="x" value="a"/><input type="hidden" name="z" value="x &lt; z"/>';

    $this->assertEqual($result,$test);
  }

  function testKnownChildren()
  {
    $template = '<form id="test" runat="server"><input id="submit" type="submit" name="submit" value="hey"></form>';
    $this->registerTestingTemplate('/tags/form/knownchildren.html', $template);

    $page = $this->initTemplate('/tags/form/knownchildren.html');
    $this->assertIsA($page->findChild('test'),'WactFormComponent');
    $this->assertIsA($page->findChild('submit'),'WactFormElement');
    $output = $page->capture();
    $this->assertEqual($output, '<form id="test"><input id="submit" type="submit" name="submit" value="hey"></form>');
  }

  function testKnownChildrenReuseRunatTrue()
  {
    $template = '<form id="test" runat="server" children_reuse_runat="true"><input id="submit" type="submit" name="submit" value="hey"></form>';
    $this->registerTestingTemplate('/tags/form/knownchildrenchildren_reuse_runattrue.html', $template);

    $page = $this->initTemplate('/tags/form/knownchildrenchildren_reuse_runattrue.html');
    $this->assertIsA($page->findChild('test'),'WactFormComponent');
    $this->assertIsA($page->findChild('submit'),'WactFormElement');
    $output = $page->capture();
    $this->assertEqual($output, '<form id="test"><input id="submit" type="submit" name="submit" value="hey"></form>');
  }

  function testKnownChildrenReuseRunatFalse()
  {
    $template = '<form id="test" runat="server" children_reuse_runat="fAlSe"><input id="submit" type="submit" name="submit" value="hey" /></form>';
    $this->registerTestingTemplate('/tags/form/knownchildrenchildren_reuse_runatfalse.html', $template);

    $page = $this->initTemplate('/tags/form/knownchildrenchildren_reuse_runatfalse.html');
    $this->assertIsA($page->findChild('test'),'WactFormComponent');
    $this->assertFalse($page->findChild('submit'));
    $output = $page->capture();
    $this->assertEqual($output, '<form id="test"><input id="submit" type="submit" name="submit" value="hey" /></form>');
  }

  function testKnownChildrenRunatClient()
  {
    $template = '<form id="test" runat="server"><input id="submit" type="submit" name="submit" value="hey" runat="client" /></form>';
    $this->registerTestingTemplate('/tags/form/knownchildrenuserunatclient.html', $template);

    $page = $this->initTemplate('/tags/form/knownchildrenuserunatclient.html');
    $this->assertIsA($page->findChild('test'),'WactFormComponent');
    $this->assertFalse($page->findChild('submit'));
    $output = $page->capture();
    $this->assertEqual($output, '<form id="test"><input id="submit" type="submit" name="submit" value="hey" /></form>');
  }

  function testNestedKnownChildren()
  {
    $template = '<form id="test" runat="server"><core:block name="block"><input id="submit" type="submit" name="submit" value="hey"></core:block></form>';
    $this->registerTestingTemplate('/tags/form/nestedknownchildren.html', $template);

    $page = $this->initTemplate('/tags/form/nestedknownchildren.html');
    $this->assertIsA($page->findChild('test'),'WactFormComponent');
    $this->assertIsA($page->findChild('submit'),'WactFormElement');
    $output = $page->capture();
    $this->assertEqual($output, '<form id="test"><input id="submit" type="submit" name="submit" value="hey"></form>');
  }

  function testIncludedKnownChildren()
  {
    $template = '<input id="submit" type="submit" name="submit" value="hey">';
    $this->registerTestingTemplate('/tags/form/knowninclude.html', $template);

    $template = '<form id="test" runat="server"><core:include file="/tags/form/knowninclude.html"></form>';
    $this->registerTestingTemplate('/tags/form/includedknownchildren.html', $template);

    $page = $this->initTemplate('/tags/form/includedknownchildren.html');
    $this->assertIsA($page->findChild('test'),'WactFormComponent');
    $this->assertIsA($page->findChild('submit'),'WactFormElement');
    $output = $page->capture();
    $this->assertEqual($output, '<form id="test"><input id="submit" type="submit" name="submit" value="hey"></form>');
  }

  function testGetServerIdWithID()
  {
    $template = '<form id="test" name="foo" runat="server"></form>';
    $this->registerTestingTemplate('/tags/form/getserveridwithid.html', $template);

    $page = $this->initTemplate('/tags/form/getserveridwithid.html');
    $page->getChild('test');
    $this->assertNoErrors();
  }

  function testGetServerIdWithName()
  {
    $template = '<form name="foo" runat="server"></form>';
    $this->registerTestingTemplate('/tags/form/getserveridwithname.html', $template);

    $page = $this->initTemplate('/tags/form/getserveridwithname.html');
    $page->getChild('foo');
    $this->assertNoErrors();
  }

  function testIsDataSource()
  {
    $FormTag = new WactFormTag(null, null, null);
    $this->assertTrue($FormTag->isDataSource());
  }

  function testFromAttributeDataTakenFromParent()
  {
    $template =
        '<form name="my_form" from="middle" runat="server">' .
        '{$Var}:{$^Var}:{$#Var}' .
        '</form>';

    $this->registerTestingTemplate('/tags/core/form/from_attribute.html', $template);
    $page = $this->initTemplate('/tags/core/form/from_attribute.html');
    $page->set('Var', 'outer');
    $page->set('middle', array('Var' => 'middle'));

    $output = $page->capture();
    $this->assertEqual($output, '<form name="my_form">middle:outer:outer</form>');
  }

  function testDBEFromAttribute()
  {
    $template =
        '<core:datasource>' .
        '<form  name="my_form" from="#middle" runat="server">' .
        '{$Var}' .
        '</form>'.
        '</core:datasource>';

    $this->registerTestingTemplate('/tags/core/form/dbe_from_attribute.html', $template);
    $page = $this->initTemplate('/tags/core/form/dbe_from_attribute.html');
    $page->set('middle', array('Var' => 'middle'));

    $output = $page->capture();
    $this->assertEqual($output, '<form name="my_form">middle</form>');
  }
}
?>