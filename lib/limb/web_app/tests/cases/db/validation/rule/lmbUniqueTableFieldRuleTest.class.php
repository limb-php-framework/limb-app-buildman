<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUniqueTableFieldRuleTest.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/validation/tests/cases/rule/lmbValidationRuleTestCase.class.php');
lmb_require('limb/dbal/src/lmbSimpleDb.class.php');
lmb_require('limb/web_app/src/validation/rule/lmbUniqueTableFieldRule.class.php');

class lmbUniqueTableFieldRuleTest extends lmbValidationRuleTestCase
{
  var $db = null;

  function setUp()
  {
    parent :: setUp();

    $toolkit = lmbToolkit :: instance();
    $conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($conn);
    $this->db->insert('test_table', array('field1' => 1, 'field2' => 'wow'), null);
    $this->db->insert('test_table', array('field1' => 2, 'field2' => 'blah'), null);
  }

  function tearDown()
  {
    parent :: tearDown();
  }

  function testFieldValid()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field1');

    $data = new lmbDataspace();
    $data->set('test', -10000);

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testFieldNotValid()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field2');

    $data = new lmbDataspace();
    $data->set('test', 'wow');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must has other value since {Value} is already exists'),
                                        array('Field' => 'test'),
                                        array('Value' => 'wow')));


    $rule->validate($data, $this->error_list);

  }
  function testFieldNotValid2()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field1');

    $data = new lmbDataspace();
    $data->set('test', "001");

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must has other value since {Value} is already exists'),
                                        array('Field' => 'test'),
                                        array('Value' => '001')));


    $rule->validate($data, $this->error_list);
  }

  function testFieldNotValidSelfError()
  {
    $rule = new lmbUniqueTableFieldRule('test', 'test_table', 'field2', $message = "ERROR_DUPLICATE_WOW");

    $data = new lmbDataspace();
    $data->set('test', 'wow');

    $this->error_list->expectOnce('addError',
                                  array($message, array('Field' => 'test'), array('Value' => 'wow')));

    $rule->validate($data, $this->error_list);
  }
}

?>