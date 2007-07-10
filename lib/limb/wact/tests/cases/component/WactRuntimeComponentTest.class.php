<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactRuntimeComponentTest.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/tests/cases/WactTemplateTestCase.class.php');

if(!class_exists('WactRuntimeComponentTest'))
{

class WactRuntimeComponentTest extends WactTemplateTestCase
{
  protected $component;

  function setUp()
  {
    parent :: setUp();
    $this->component = new WactRuntimeComponent('TestId');
  }

  function testGetServerID()
  {
    $this->assertEqual($this->component->getId(),'TestId');
  }

  function testFindChild()
  {
    $child = new WactRuntimeComponent('TestChild');
    $this->component->addChild($child);
    $this->assertEqual($this->component->findChild('TestChild'), $child);
  }

  function testFindChildNotFound()
  {
    $this->assertFalse($this->component->findChild('TestChild'));
  }

  function testFindChildByClass()
  {
    $child = new WactRuntimeComponent('TestChild');
    $this->component->addChild($child);
    $this->assertEqual($this->component->findChildByClass('WactRuntimeComponent'), $child);
  }

  function testFindChildByClassNotFound()
  {
    $this->assertFalse($this->component->findChildByClass('TestComponent'));
  }

  function testFindParentByChilld()
  {
    $component = new WactRuntimeComponent('TestParent');
    $component->addChild($this->component);
    $this->assertIsA($this->component->findParentByClass('WactRuntimeComponent'),'WactRuntimeComponent');
  }

  function testFindParentByClassNotFound()
  {
    $this->assertFalse($this->component->findParentByClass('TestComponent'));
  }
}

}
?>