<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbPatternRuleTest.class.php 5010 2007-02-08 15:37:40Z pachanga $
 * @package    validation
 */
require_once(dirname(__FILE__) . '/lmbValidationRuleTestCase.class.php');
lmb_require('limb/validation/src/rule/lmbPatternRule.class.php');

class lmbPatternRuleTest extends lmbValidationRuleTestCase
{
  function testPatternRule()
  {
    $rule = new lmbPatternRule('testfield', '/^\w+$/');

    $data = new lmbDataspace();
    $data->set('testfield', 'SimpletestisCool');

    $this->error_list->expectNever('addError');

    $rule->validate($data, $this->error_list);
  }

  function testPatternRuleFailed()
  {
    $rule = new lmbPatternRule('testfield', '/^\w+$/');

    $data = new lmbDataspace();
    $data->set('testfield', 'Simpletest is Cool!');

    $this->error_list->expectOnce('addError',
                                  array(tr('/validation', '{Field} value is wrong'),
                                        array('Field'=>'testfield'),
                                        array()));

    $rule->validate($data, $this->error_list);
  }
}
?>