<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: optionrenderer.test.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/WactTemplate.class.php';
require_once 'limb/wact/src/components/form/form.inc.php';
require_once 'limb/wact/src/components/form/select.inc.php';

class WactOptionRenderTestCase extends UnitTestCase {

  var $OR;

  function setUp() {
    $this->OR =  new WactOptionRenderer();
  }

  function tearDown() {
    unset($this->OR);
  }

  function testRender() {
    ob_start();
    $this->OR->renderOption('foo','bar',FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="foo">bar</option>');
  }

  function testRenderNoContents() {
    ob_start();
    $this->OR->renderOption('foo','',FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="foo">foo</option>');
  }

  function testRenderEntities() {
    ob_start();
    $this->OR->renderOption('x > y','& v < z',FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="x &gt; y">&amp; v &lt; z</option>');
  }

  function testRenderEntitiesNoContents() {
    ob_start();
    $this->OR->renderOption('x > y',FALSE,FALSE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="x &gt; y">x &gt; y</option>');
  }

  function testSelected() {
    ob_start();
    $this->OR->renderOption('foo','bar',TRUE);
    $out = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($out,'<option value="foo" selected>bar</option>');
  }


}
?>
