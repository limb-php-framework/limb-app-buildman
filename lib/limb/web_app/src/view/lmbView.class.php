<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbView.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

abstract class lmbView
{
  protected $template_name;
  protected $variables = array();

  function __construct($template_name = '')
  {
    $this->template_name = $template_name;
  }

  function setTemplate($template_name)
  {
    $this->template_name = $template_name;
  }

  function hasTemplate()
  {
    return $this->template_name != '';
  }

  abstract function render();

  function reset()
  {
    $this->variables = array();
  }

  function getTemplate()
  {
    return $this->template_name;
  }

  function set($variable_name, $value)
  {
    $this->variables[$variable_name] = $value;
  }

  function setVariables($vars)
  {
    $this->variables = $vars;
  }

  function get($variable_name)
  {
    if(isset($this->variables[$variable_name]))
      return $this->variables[$variable_name];
    else
      return null;
  }

  function getVariables()
  {
    return $this->variables;
  }
}
?>
