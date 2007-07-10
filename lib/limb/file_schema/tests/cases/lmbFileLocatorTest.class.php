<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFileLocatorTest.class.php 4996 2007-02-08 15:36:18Z pachanga $
 * @package    file_schema
 */
lmb_require('limb/file_schema/src/lmbFileLocations.interface.php');
lmb_require('limb/file_schema/src/lmbFileLocator.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

Mock :: generate('lmbFileLocations', 'MockFileLocaions');

class lmbFileLocatorTest extends UnitTestCase
{
  function testLocateException()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations());

    $params = array('whatever');
    $mock->expectOnce('getLocations', array($params));
    $mock->setReturnValue('getLocations', array());

    try
    {
      $locator->locate('whatever', $params);
      $this->assertTrue(false);
    }
    catch(lmbFileNotFoundException $e){}
  }

  function testLocateUsingLocations()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations());

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/_en/',
                                     dirname(__FILE__) . '/design/'));

    $this->assertEqual(lmbFs :: normalizePath($locator->locate('test1.html')),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testLocateUsingAliasFormatting()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations(), '/_en/*');

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/'));

    $this->assertEqual(lmbFs :: normalizePath($locator->locate('test1.html')),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testSkipAbsoluteAliases()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations());

    $mock->expectNever('getLocations');

    $this->assertEqual(lmbFs :: normalizePath($locator->locate(dirname(__FILE__) . '/design/_en/test1.html')),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testLocateAll()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations(), '*.html');

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/',
                                dirname(__FILE__) . '/design/_en/'));


    $all_files = $locator->locateAll();
    $this->assertEqual(lmbFs :: normalizePath($all_files[0]),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/test1.html'));

    $this->assertEqual(lmbFs :: normalizePath($all_files[1]),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }

  function testLocateAllUsingPrefix()
  {
    $locator = new lmbFileLocator($mock = new MockFileLocations(), '*.html');

    $mock->expectOnce('getLocations');
    $mock->setReturnValue('getLocations',
                          array(dirname(__FILE__) . '/design/',
                                dirname(__FILE__) . '/design/_en/'));


    $all_files = $locator->locateAll('/_en/');

    $this->assertEqual(sizeof($all_files), 1);

    $this->assertEqual(lmbFs :: normalizePath($all_files[0]),
                       lmbFs :: normalizePath(dirname(__FILE__) . '/design/_en/test1.html'));
  }
}

?>