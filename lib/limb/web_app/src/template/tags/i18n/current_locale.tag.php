<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: current_locale.tag.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @tag limb:CURRENT_LOCALE
* @req_const_attributes name
*/
class lmbCurrentLocaleTag extends WactCompilerTag
{
  function preGenerate($code)
  {
    parent::preGenerate($code);

    $name = $this->getAttribute('name');
    $code->writePhp('if ("' . $name. '" == lmbToolkit :: instance()->getLocale()->getLocaleSpec()->getLocaleString()) {');
  }

  function postGenerate($code)
  {
    $code->writePhp('}');
    parent::postGenerate($code);
  }
}

?>