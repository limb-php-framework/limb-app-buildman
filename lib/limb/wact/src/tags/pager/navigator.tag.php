<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: navigator.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag pager:NAVIGATOR
*/
class WactPagerNavigatorTag extends WactRuntimeComponentTag
{
  protected $runtimeComponentName = 'WactPagerComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/pager/WactPagerComponent.class.php';
  protected $mirror;

  function preGenerate($code)
  {
    parent :: preGenerate($code);
    $code->writePhp($this->getComponentRefCode() . '->resetPagesIterator();' . "\n");
  }

  function generateConstructor($code)
  {
    if ($this->mirror && ($mirrored_pager = $this->parent->findChild($this->mirror)))
    {
      return $mirrored_pager->generateConstructor($code);
    }
    else
      parent :: generateConstructor($code);

    $items = $this->getAttribute('items');
    if (!empty($items))
      $code->writePhp($this->getComponentRefCode() . '->setItemsPerPage(' . $items . ');' . "\n");

    $pager_prefix = $this->getAttribute('pager_prefix');
    if (!empty($pager_prefix))
      $code->writePhp($this->getComponentRefCode() . '->setPagerPrefix("' . $pager_prefix . '");' . "\n");

    if($this->findChildByClass('WactPagerElipsesTag'))
    {
      $code->writePhp($this->getComponentRefCode() . '->useElipses();' . "\n");
      $pages_in_middle = $this->getAttribute('pages_in_middle');

      if (!empty($pages_in_middle))
        $code->writePhp($this->getComponentRefCode() . '->setPagesInMiddle(' . $pages_in_middle . ');' . "\n");

      if ($this->hasAttribute('pages_in_sides'))
        $code->writePhp($this->getComponentRefCode() . '->setPagesInSides(' . (int)$this->getAttribute('pages_in_sides') . ');' . "\n");
    }
    else
    {
      $code->writePhp($this->getComponentRefCode() . '->useSections();' . "\n");
      $pages_per_section = $this->getAttribute('pages_per_section');
      if (!empty($pages_per_section))
        $code->writePhp($this->getComponentRefCode() . '->setPagesPerSection(' . $pages_per_section . ');' . "\n");
    }
  }

  function prepare()
  {
    parent :: prepare();
    $this->mirror = $this->getAttribute('mirror');
    if (empty($this->mirror))
      return;

    if(!$mirrored_pager = $this->parent->findChild($this->mirror))
      $this->raiseCompilerError('Could not find component',
                                array('attribute' => $this->mirror));
  }

  function getComponentRefCode()
  {
    if ($this->mirror && ($mirrored_pager = $this->parent->findChild($this->mirror)))
    {
      return $mirrored_pager->getComponentRefCode();
    }
    else
      return parent :: getComponentRefCode();
  }
}

?>