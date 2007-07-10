<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: label.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

class WactLabelTagTestCase extends WactTemplateTestCase {

  var $Control;

  function testErrorClass() {

    $template = '<form runat="server">
                        <label id="test" errorclass="warning" runat="server">A label</label>
                     </form>';
    $this->registerTestingTemplate('/tags/form/label/errorclass.html', $template);

    $page = $this->initTemplate('/tags/form/label/errorclass.html');
    $Label = $page->findChild('test');
    $Label->setError();
    $this->assertEqual('warning',$Label->getAttribute('class'));
  }

  function testErrorStyle() {

    $template = '<form runat="server">
                        <label id="test" errorstyle="warning" runat="server">A label</label>
                     </form>';
    $this->registerTestingTemplate('/tags/form/label/errorstyle.html', $template);

    $page = $this->initTemplate('/tags/form/label/errorstyle.html');
    $Label = $page->findChild('test');
    $Label->setError();
    $this->assertEqual('warning',$Label->getAttribute('style'));

  }

  function testSetError() {

    $template = '<form id="testForm" runat="server">
                        <label id="test" class="Normal" style="Normal"
                            errorclass="ErrorClass" errorstyle="ErrorStyle" runat="server">
                        </label>
                    </form>';
    $this->registerTestingTemplate('/components/form/label/seterror.html', $template);

    $page = $this->initTemplate('/components/form/label/seterror.html');

    $Label = $page->getChild('test');
    $this->assertEqual('Normal',$Label->getAttribute('class'));
    $this->assertEqual('Normal',$Label->getAttribute('style'));
    $Label->setError();
    $this->assertEqual('ErrorClass',$Label->getAttribute('class'));
    $this->assertEqual('ErrorStyle',$Label->getAttribute('style'));

  }


}
?>
