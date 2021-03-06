<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: optional.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * The opposite of the CoreDefaultTag
 * @tag core:OPTIONAL
 * @req_const_attributes for
 */
class WactCoreOptionalTag extends WactCompilerTag
{
  protected $DBE;

  function prepare()
  {
    $this->DBE = new WactDataBindingExpression($this->getAttribute('for'), $this);
    $this->DBE->prepare();

    parent::prepare();
  }

  function preGenerate($code)
  {
    parent::preGenerate($code);

    $tempvar = $code->getTempVariable();
    $this->DBE->generatePreStatement($code);
    $code->writePHP('$' . $tempvar . ' = ');
    $this->DBE->generateExpression($code);
    $code->writePHP(';');
    $this->DBE->generatePostStatement($code);

    $code->writePHP('if (is_scalar($' . $tempvar .' )) $' . $tempvar . '= trim($' . $tempvar . ');');
    $code->writePHP('if (!empty($' . $tempvar . ')){');
  }

  function postGenerate($code_writer)
  {
    $code_writer->writePHP('}');
    parent::postGenerate($code_writer);
  }
}
?>
