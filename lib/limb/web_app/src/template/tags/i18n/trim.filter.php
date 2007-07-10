<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: trim.filter.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
/**
* @filter i18n_trim
* @max_attributes 1
*/
class lmbI18NTrimFilter extends WactCompilerFilter
{
  function getValue()
  {
   if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
      $characters = $this->parameters[0]->getValue();
    else
      $characters = '';

    if (!$this->isConstant())
      $this->raiseUnresolvedBindingError();

    if($characters)
      return _trim($this->base->getValue(), $characters);
    else
      return _trim($this->base->getValue());
  }

  function generateExpression(&$code)
  {
    $code->writePHP('_trim(');
    $this->base->generateExpression($code);

    if(isset($this->parameters[0]) && $this->parameters[0]->getValue())
    {
      $code->writePHP(',');
      $this->parameters[0]->generateExpression($code);
    }

    $code->writePHP(')');
  }
}

?>