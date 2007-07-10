<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbInvalidValueRuleTest.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbInvalidValueRule.class.php');

class lmbInvalidValueRuleTest extends lmbValidationRuleTestCase
{
  function testInvalidValueRuleOkInt()
  {
    $rule = new lmbInvalidValueRule('testfield', 0);

    $data = new lmbDataspace();
    $data->set('testfield', 1);

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testInvalidValueRuleOkInt2()
  {
    $rule = new lmbInvalidValueRule('testfield', 0);

    $data = new lmbDataspace();
    $data->set('testfield', 'whatever');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testInvalidValueRuleOkNull()
  {
    $rule = new lmbInvalidValueRule('testfield', null);

    $data = new lmbDataspace();
    $data->set('testfield', 'null');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);

  }

  function testInvalidValueRuleOkBool()
  {
    $rule = new lmbInvalidValueRule('testfield', false);

    $data = new lmbDataspace();
    $data->set('testfield', 'false');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);

  }

  function testInvalidValueRuleError()
  {
    $rule = new lmbInvalidValueRule('testfield', 1);

    $data = new lmbDataspace();
    $data->set('testfield', 1);

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} value is wrong'),
                                        array('Field' => 'testfield'),
                                        array()));

    $rule->validate($data, $this->error_list);
  }

  function testInvalidValueRuleError2()
  {
    $rule = new lmbInvalidValueRule('testfield', 1);

    $data = new lmbDataspace();
    $data->set('testfield', '1');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} value is wrong'),
                                        array('Field' => 'testfield'),
                                        array()));

    $rule->validate($data, $this->error_list);
  }

}

?>