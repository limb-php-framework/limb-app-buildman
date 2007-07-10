<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbNumericPrecisionRule.class.php 5108 2007-02-19 09:07:53Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
* Checks that field value is a valid numeric value and its precision falls
* within allowable parameters.
* Example of usage:
* <code>
*  lmb_require('limb/validation/src/rule/lmbNumericPrecisionRule.class.php');
*  $validator->addRule(new lmbNumericPrecisionRule('price', 5, 2));
*  // 100.2 with match this rule, 100.300 or 100000 - not.
* </code>
*/
class lmbNumericPrecisionRule extends lmbSingleFieldRule
{
  /**
  * @var int Number of decimal digits allowed
  */
  protected $decimal_digits;
  /**
  * @var int Number of whole digits allowed
  */
  protected $whole_digits;

  /**
  * @param string Field name
  * @param int Number of whole digits allowed
  * @param int Number of decimal digits allowed
  */
  function __construct($field_name, $whole_digits, $decimal_digits = 0)
  {
    parent :: __construct($field_name);

    $this->whole_digits = $whole_digits;
    $this->decimal_digits = $decimal_digits;
  }

  function check($value)
  {
    if (preg_match('/^[+-]?(\d*)\.?(\d*)$/', $value, $match))
    {
      if (strlen($match[1]) > $this->whole_digits) {
          $this->error(tr('/validation', 'You have entered too many whole digits ({digits}) in {Field} (max {maxdigits}).'),
              array('maxdigits' => $this->whole_digits,
                  'digits' => strlen($match[1])));
      }
      if (strlen($match[2]) > $this->decimal_digits) {
          $this->error(tr('/validation', 'You have entered too many decimal digits ({digits}) in {Field} (max {maxdigits}).'),
              array('maxdigits' => $this->decimal_digits,
                  'digits' => strlen($match[2])));
      }
    }
    else
    {
      $this->error(tr('/validation', '{Field} must be a valid number.'));
    }
  }
}
?>