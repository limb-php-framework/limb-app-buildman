<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSysParamComponent.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('SysParam.class.php');

class lmbSysParamComponent extends WactRuntimeComponent
{
  function getParam($name, $type)
  {
    $inst = SysParam :: instance();
    echo $inst->getParam($name, $type);
  }
}

?>