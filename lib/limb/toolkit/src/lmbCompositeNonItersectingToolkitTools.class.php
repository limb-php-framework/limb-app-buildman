<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCompositeNonItersectingToolkitTools.class.php 5007 2007-02-08 15:37:18Z pachanga $
 * @package    toolkit
 */
lmb_require(dirname(__FILE__) . '/lmbCompositeToolkitTools.class.php');

class lmbCompositeNonItersectingToolkitTools extends lmbCompositeToolkitTools
{
  function getToolsSignatures()
  {
    $result = array();
    foreach($this->tools as $tools)
    {
      $signatures = $tools->getToolsSignatures();

      if($intersect = array_intersect(array_keys($signatures), array_keys($result)))
      {
        throw new lmbException('tools signatures intersection',
                                array('intersection' => $intersect));
      }
      $result = array_merge($result, $signatures);
    }
    return $result;
  }
}
?>
