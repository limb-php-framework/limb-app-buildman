<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSimpleUrlRule.class.php 5108 2007-02-19 09:07:53Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
* Checks that field value is a valid Url using single regexp
* @see lmbUrlRule
*/
class lmbSimpleUrlRule extends lmbSingleFieldRule
{
  function check($value)
  {
    $regex = "
      \b
      (
        (ftp|https?)://[-\w]+(\.\w[-\w]*)+
        |
        (?i: [a-z0-9] (?:[-a-z0-9]*[-a-z0-9])? \. )+
        (?-i: com\b
            |	edu\b
            |	biz\b
            |	gov\b
            |	in(?:t|fo)\b
            | mil\b
            |	net\b
            |	org\b
            |	[a-z][a-z]\b
        )
      )
      ( : \d+ )?
      (
        /
        [^;\"'<>()\[\]{}\s\x7F-\xFF]*
        (?:
          [..?]+ [^;\"'<>()\[\]{}\s\x7F-\xFF]
        )*
      )?
    ";

    if (!preg_match("~{$regex}~x", $value))
    {
      $this->error(tr('/validation', '{Field} must be valid URL'), array());
    }
  }
}
?>