<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbValidatorTest.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/lmbValidator.class.php');
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');
lmb_require('limb/validation/src/lmbErrorList.class.php');

Mock::generate('lmbValidationRule', 'MockValidationRule');
Mock::generate('lmbErrorList', 'MockFieldsErrorList');

class lmbValidatorTest extends UnitTestCase
{
  var $error_list;
  var $validator;

  function setUp()
  {
    $this->error_list = new MockFieldsErrorList();
    $this->validator = new lmbValidator();
    $this->validator->setErrorList($this->error_list);
  }

  function testValidateEmpty()
  {
    $validator = new lmbValidator();
    $this->assertTrue($validator->validate(new lmbDataspace()));
  }

  function testIsValid()
  {
    $this->error_list->expectCallCount('isValid', 2);
    $this->error_list->setReturnValueAt(0, 'isValid', false);
    $this->error_list->setReturnValueAt(1, 'isValid', true);

    $this->assertFalse($this->validator->isValid());
    $this->assertTrue($this->validator->isValid());
  }

  function testAddRulesAndValidate()
  {
    $ds = new lmbDataspace(array('foo'));

    $r1 = new MockValidationRule();
    $r2 = new MockValidationRule();

    $this->validator->addRule($r1);
    $this->validator->addRule($r2);

    $r1->expectOnce('validate', array($ds, $this->error_list));
    $r2->expectOnce('validate', array($ds, $this->error_list));

    $this->error_list->expectOnce('isValid');
    $this->error_list->setReturnValue('isValid', true);

    $this->assertTrue($this->validator->validate($ds));
  }
}

?>