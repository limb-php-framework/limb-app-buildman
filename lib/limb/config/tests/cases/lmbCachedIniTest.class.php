<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCachedIniTest.class.php 4990 2007-02-08 15:35:31Z pachanga $
 * @package    config
 */
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require(dirname(__FILE__) . '/lmbIniTest.class.php');

class lmbCachedIniTest extends lmbIniTest
{
  var $cache_dir;

  function setUp()
  {
    parent :: setUp();

    $this->cache_dir = LIMB_VAR_DIR . '/ini/';
    lmbFs :: rm($this->cache_dir);
  }

  function _createIni($file)
  {
    return new lmbCachedIni($file, $this->cache_dir);
  }
}

?>