<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeTerminalNode.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */

abstract class lmbTestTreeTerminalNode extends lmbTestTreeNode
{
  function addChild($node){}
  function findChildByPath($path){}
  function createTestGroupWithoutChildren()
  {
    return $this->createTestGroup();
  }

  function isTerminal()
  {
    return true;
  }
}

?>
