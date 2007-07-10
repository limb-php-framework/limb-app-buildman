<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/tags/form/control.inc.php';

/**
 * Compile time component for building runtime select components
 * @tag select
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 * @restrict_self_nesting
 * @runat client
 */
class WactSelectTag extends WactControlTag {

  /**
   * File to include at runtime
   * @var string path to runtime component relative to WACT_ROOT
   */
  protected $runtimeIncludeFile;

  /**
   * Name of runtime component class
   * @var string
   */
  protected $runtimeComponentName;

  /**
   * @return void
   */
  function prepare()
  {
    if ($this->getBoolAttribute('multiple'))
    {
      $this->runtimeIncludeFile = 'limb/wact/src/components/form/select.inc.php';
      $this->runtimeComponentName = 'WactSelectMultipleComponent';

      // Repetition of ControlTag::prepare but required for special case
      // of SelectMultiple to provide meaningful error messages
      if (!$this->getBoolAttribute('name'))
      {
        if ( $this->getBoolAttribute('id') )
          $this->setAttribute('name',$this->getAttribute('id').'[]'); // Note - appends [] to id value
        else
          $this->raiseRequiredAttributeError('name');
      }

      if (!is_integer(strpos($this->getAttribute('name'), '[]')))
      {
        $this->raiseCompilerError('Array required in name attribute',
                                  array('name' => $this->getAttribute('name')));
      }
    }
    else
    {
      $this->runtimeIncludeFile = 'limb/wact/src/components/form/select.inc.php';
      $this->runtimeComponentName = 'WactSelectSingleComponent';
    }

    parent::prepare();
  }

  /**
   * Ignore the compiler time contents and generate the contents at run time.
   * @return void
   */
  function generateContents($code_writer)
  {
    $writer = new WactCodeWriter();
    foreach($this->getChildren() as $option_tag)
    {
      if(!is_a($option_tag, 'WactCompilerTag'))
        continue;

      $value = $option_tag->getAttribute('value');
      $option_tag->generateContents($writer);
      $text = addslashes($writer->getCode());
      $writer->reset();
      $code_writer->writePHP($this->getComponentRefCode() . '->addToChoices('. $value .',"'. $text.'");');

      if($option_tag->hasAttribute('selected'))
        $code_writer->writePHP($this->getComponentRefCode() . '->addToDefaultSelection('. $value .');');
    }

    $code_writer->writePHP($this->getComponentRefCode() . '->renderContents();');
  }
}
?>