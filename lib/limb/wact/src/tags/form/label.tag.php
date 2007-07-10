<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: label.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * Compile time component for building runtime form labels
 * @tag label
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle
 * @restrict_self_nesting
 * @runat client
 * @parent_tag_class WactFormTag
 */
class WactLabelTag extends WactRuntimeComponentHTMLTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/form.inc.php';

  protected $runtimeComponentName = 'WactLabelComponent';

  /**
   * @param WactCodeWriter
   */
  function generateConstructor($code_writer)
  {
    parent::generateConstructor($code_writer);
    if ($this->hasAttribute('errorclass'))
    {
      $code_writer->writePHP($this->getComponentRefCode() . '->errorclass = ');
      $code_writer->writePHPLiteral($this->getAttribute('errorclass'));
      $code_writer->writePHP(';');
    }

    if ($this->hasAttribute('errorstyle'))
    {
      $code_writer->writePHP($this->getComponentRefCode() . '->errorstyle = ');
      $code_writer->writePHPLiteral($this->getAttribute('errorstyle'));
      $code_writer->writePHP(';');
    }
  }
}
?>