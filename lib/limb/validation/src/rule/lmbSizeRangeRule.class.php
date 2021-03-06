<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSizeRangeRule.class.php 5108 2007-02-19 09:07:53Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
* For fields have a minimum and maximum length
*/

/**
* Checks that field has minimux and (or) maximum length
* Example of usage:
* <code>
*  lmb_require('limb/validation/src/rule/lmbSizeRangeRule.class.php');
*  // restricts "title" field to be more than 50 characters (minimum length is 0)
*  $validator->addRule(new lmbSizeRangeRule('title', 50));
*  //same as above
*  $validator->addRule(new lmbHandle('limb/validation/src/rule/lmbSizeRangeRule', array('title', 10)));
*  // checks that "title" field have length between 10 and 50 characters
*  $validator->addRule(new lmbSizeRangeRule('title', 10, 50));
* </code>
*/
class lmbSizeRangeRule extends lmbSingleFieldRule
{
  /**
  * @var int Minumum length
  */
  protected $min_length;
  /**
  * @var int Maximum length
  */
  protected $max_length;

  /**
  * Constructor
  * If only two agruments given - use second argument as maximun field length
  * If all three agruments given - use second argument as manimum field length and third - as maximum field length
  * @param string fieldname to validate
  * @param int Minumum or maximum length
  * @param int Maximum length (optional)
  */
  function __construct($field_name, $min_or_max_length, $max_length = NULL)
  {
    parent :: __construct($field_name);

    if (is_null($max_length))
    {
      $this->min_length = NULL;
      $this->max_length = $min_or_max_length;
    }
    else
    {
      $this->min_length = $min_or_max_length;
      $this->max_length = $max_length;
    }
  }

  function check($value)
  {
    if (!is_null($this->min_length) && (strlen($value) < $this->min_length))
    {
      $this->error(tr('/validation', '{Field} must be greater than {min} characters.'),
                   array('min' => $this->min_length));
    }
    elseif (strlen($value) > $this->max_length)
    {
      $this->error(tr('/validation', '{Field} must be less than {max} characters.'),
                   array('max' => $this->max_length));
    }
  }
}
?>