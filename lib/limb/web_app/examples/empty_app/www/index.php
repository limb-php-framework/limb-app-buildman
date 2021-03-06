<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: index.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

require_once(dirname(__FILE__) . '/../setup.php');
require_once('limb/web_app/src/lmbWebApplication.class.php');

$application = new lmbWebApplication();
$application->process();

?>
