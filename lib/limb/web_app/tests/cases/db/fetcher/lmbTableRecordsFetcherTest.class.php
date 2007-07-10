<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTableRecordsFetcherTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbTableRecordsFetcher.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');

//do we really need lmbTableRecordsFetcher ?
class lmbTestDbTable extends lmbTableGateway
{
  protected $_db_table_name = 'test_db_table';
}

class lmbTableRecordsFetcherTest extends UnitTestCase
{
  var $db;

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->db = new lmbSimpleDb($toolkit->getDefaultDbConnection());

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    lmbToolkit :: restore();
  }

  function _cleanUp()
  {
    $this->db->delete('test_db_table');
  }

  function testSetTableClass()
  {
    $this->_createTwoRecordsInTestDbTable();

    $helper = new lmbTableRecordsFetcher();
    $helper->setTableClass('lmbTestDbTable');

    $this->_verifyRecordsFetched($helper->getDataSet(), __LINE__);
  }

  function testSetTableName()
  {
    $this->_createTwoRecordsInTestDbTable();

    $helper = new lmbTableRecordsFetcher();
    $helper->setTableName('test_db_table');

    $this->_verifyRecordsFetched($helper->getDataSet(), __LINE__);
  }

  protected function _createTwoRecordsInTestDbTable()
  {
    $this->db->insert('test_db_table', array('title' => 'Some title',
                                             'description' => 'Some description'));

    $this->db->insert('test_db_table', array('title' => 'Some other title',
                                             'description' => 'Some other description'));
  }

  protected function _verifyRecordsFetched($record_set)
  {
    $this->assertEqual($record_set->count(), 2);
    $this->assertEqual($record_set->countPaginated(), 2);

    $record_set->rewind();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'Some title');
    $this->assertEqual($record->get('description'), 'Some description');

    $record_set->next();
    $record = $record_set->current();
    $this->assertEqual($record->get('title'), 'Some other title');
    $this->assertEqual($record->get('description'), 'Some other description');

    $record_set->next();
    $this->assertFalse($record_set->valid());
  }
}
?>
