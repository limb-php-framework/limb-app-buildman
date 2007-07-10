<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordFetchTagTest.class.php 5097 2007-02-16 13:05:25Z serega $
 * @package    web_app
 */
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/web_app/tests/cases/plain/template/lmbWactTestCase.class.php');

class TestObjectTagVersion extends lmbActiveRecord
{
  protected $_db_table_name = 'test_object';

  static function findCustom()
  {
    return new lmbArrayDataset(array(array('method' => 'Custom')));
  }

  static function findByTitle($title)
  {
    return lmbActiveRecord :: find('TestObjectTagVersion', 'title = "' . $title . '"');
  }
}

class lmbActiveRecordFetchTagTest extends lmbWactTestCase
{
  function setUp()
  {
    parent :: setUp();
    $this->_dbCleanUp();
  }

  function tearDown()
  {
    $this->_dbCleanUp();
    parent :: tearDown();
  }

  function _dbCleanUp()
  {
    lmbActiveRecord :: delete('TestObjectTagVersion');
  }

  function testFetchAll()
  {
    $c1 = $this->_createObject();
    $c2 = $this->_createObject();

    $template = '<active_record:fetch class_path="TestObjectTagVersion" target="testTarget" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/ar_fetch_all.html', $template);

    $page = $this->initTemplate('/limb/ar_fetch_all.html');

    $this->assertEqual(trim($page->capture()), $c1->getTitle() . '|' . $c2->getTitle() . '|');
  }

  function testFetchAllAttributeUsing()
  {
    $c1 = $this->_createObject();
    $c2 = $this->_createObject();

    $template = '<active_record:fetch using="TestObjectTagVersion" target="testTarget" />' .
                '<list:LIST id="testTarget"><list:ITEM>{$title}|</list:ITEM></list:LIST>';

    $this->registerTestingTemplate('/limb/ar_fetch_all2.html', $template);

    $page = $this->initTemplate('/limb/ar_fetch_all2.html');

    $this->assertEqual(trim($page->capture()), $c1->getTitle() . '|' . $c2->getTitle() . '|');
  }

  function testFetchNoObject()
  {
    $template = '<active_record:fetch class_path="TestObjectTagVersion" target="testTarget" first="true">' .
                    '<fetch:param record_id="3" />'.
                '</active_record:fetch>'.
                '<core:datasource id="testTarget">'.
                '<core:optional for="id">id={$id}</core:optional>'.
                '<core:default for="id">no object</core:default>'.
                '</core:datasource>';

    $this->registerTestingTemplate('/limb/ar_fetch_noobject.html', $template);

    $page = $this->initTemplate('/limb/ar_fetch_noobject.html');

    $this->assertEqual(trim($page->capture()), 'no object');
  }

  function testFetchWithCustomFindMethod()
  {
    $template = '<active_record:fetch using="TestObjectTagVersion" target="testTarget" find="custom" first="true" />' .
                '<core:datasource id="testTarget">{$method}</core:datasource>';

    $this->registerTestingTemplate('/limb/ar_fetch_with_custom_find.html', $template);

    $page = $this->initTemplate('/limb/ar_fetch_with_custom_find.html');

    $this->assertEqual($page->capture(), 'Custom');
  }


  function _createObject()
  {
    $object = new TestObjectTagVersion();
    $object->setTitle('some title' . mt_rand());
    $object->save();
    return $object;
  }
}
?>
