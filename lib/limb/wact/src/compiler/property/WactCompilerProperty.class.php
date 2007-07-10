<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactCompilerProperty.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactCompilerProperty
{
  protected $context = null;

  /**
  * Calcluated values are considered active if they have been referenced
  * in the template.
  * @access private
  */
  var $isActive = FALSE;

  function __construct($context = null)
  {
    $this->context = $context;
  }

  /**
  * Does this property refer to a constant value at compile time?
  * @return Boolean
  * @access public
  */
  function isConstant()
  {
    return FALSE;
  }

  /*
  * @return Boolean Activation status of this property
  * @access public
  */
  function isActive()
  {
    return $this->isActive;
  }

  /*
  * Indicated that this property is active
  * @access public
  */
  function activate()
  {
    $this->isActive = TRUE;
  }

  /**
  * Return this value as a PHP value
  * @return String
  * @access public
  */
  function getValue() {
  }

  /**
  * Generate setup code when a property enters a scope in which it is
  * valid.  This is only called if the Property is considered active.
  * @param WactCodeWriter
  * @return void
  * @access protected
  */
  function generateScopeEntry($code_writer) {
  }

  /**
  * Generate setup code for an expression reference
  * @param WactCodeWriter
  * @return void
  * @access protected
  */
  function generatePreStatement($code_writer) {
  }

  /**
  * Generate the code to read the data value at run time
  * Must generate only a valid PHP Expression.
  * @param WactCodeWriter
  * @return void
  * @access protected
  */
  function generateExpression($code_writer) {
  }

  /**
  * Generate tear down code for an expression reference
  * @param WactCodeWriter
  * @return void
  * @access protected
  */
  function generatePostStatement($code_writer) {
  }

  /**
  * Generate tear down code when a property enters a scope in which it is
  * valid.  This is only called if the Property is considered active.
  * @param WactCodeWriter
  * @return void
  * @access protected
  */
  function generateScopeExit($code_writer) {
  }
}

?>