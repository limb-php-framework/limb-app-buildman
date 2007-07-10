<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmailRuleTest.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbEmailRule.class.php');

class lmbEmailRuleTest extends lmbValidationRuleTestCase
{
  function testEmailRule()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'billgates@microsoft.com');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleNoAt()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'billgatesmicrosoft.com');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must contain a @ character.'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleInvalidUser()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'bill(y!)gates@microsoft.com');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', 'Invalid user in {Field}.'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleInvalidDomain()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'billgates@micro$oft.com');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} must contain only letters, numbers, hyphens, and periods.'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleMixedCase()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'BillGates@Microsoft.com');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }

  function testEmailRuleSpecialChars()
  {
    $rule = new lmbEmailRule('testfield');

    $dataspace = new lmbDataspace();
    $dataspace->set('testfield', 'bill_gates.the-boss@microsoft.com');

    $this->error_list->expectNever('addError');

    $rule->validate($dataspace, $this->error_list);
  }
}

?>