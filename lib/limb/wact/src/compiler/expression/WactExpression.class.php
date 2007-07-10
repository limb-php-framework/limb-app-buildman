<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpression.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/compiler/expression/WactExpressionLexer.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionLexerParallelRegex.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionLexerStateStack.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionValueParser.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionFilterFindingParser.class.php';
require_once 'limb/wact/src/compiler/expression/WactExpressionFilterParser.class.php';

/**
* Represents a single Expression found in the template like "var|uppercase|trim:0,10"
* Responsibly for parsing the expression and building a filter chain
* for the expression (if expression contained filter syntax)
*/
class WactExpression
{
  protected $parsed;

  protected $context;

  protected $original_expression;

  protected $expression;

  protected $filter_dictionary;

  function __construct($expression, $context_node, $filter_dictionary, $default_filter = 'raw')
  {
    $this->original_expression = $expression;

    $this->expression = $expression;

    $this->context = $context_node;

    $this->filter_dictionary = $filter_dictionary;

    $this->default_filter = $default_filter;

    $this->_parseExpression();
  }

  protected function _parseExpression()
  {
    $apply_default_filter = $this->_shouldApplyDefaultFilter();

    $this->_createParsedExpression();

    if ($apply_default_filter)
      $this->_applyDefaultFilter();
  }

  protected function _createParsedExpression()
  {
    $pos = strpos($this->expression, "|");

    if ($pos === FALSE)
      $this->parsed = $this->createValue($this->expression);
    else
    {
      $base_expression = trim(substr($this->expression, 0, $pos));
      $filters_expression = trim(substr($this->expression, $pos + 1));
      $this->parsed = $this->createFilterChain($filters_expression, $this->createValue($base_expression));
    }
  }

  protected function _shouldApplyDefaultFilter()
  {
    if ($this->default_filter == 'raw')
      return false;

    if (preg_match('/^(.*)\s*\|\s*raw$/is', $this->expression, $match))
    {
      $this->expression = $match[1];
      return false;
    }
    return true;
  }

  protected function _applyDefaultFilter()
  {
    $default_filter = $this->_createFilter($this->default_filter, $this->parsed);

    // Don't apply the default filter if the last filter in the
    // chain is already that filter.
    if (strcasecmp(get_class($this->parsed), get_class($default_filter)) == 0)
      return;

    $this->parsed = $default_filter;
  }

  /**
  * Parses an expression and returns an object representing the expression
  */
  function createValue($expression)
  {
    $Parser = new WactExpressionValueParser($expression);
    if($Parser->isConstantValue())
      return new WactConstantProperty($Parser->getValue());
    else
      return new WactDataBindingExpression($expression, $this->context);
  }

  /**
  * Parses an expression, building a chain of filters for it
  */
  function createFilterChain($expression, $base)
  {
    $filter_finding_parser = new WactExpressionFilterFindingParser($expression);

    foreach($filter_finding_parser->getFilterExpressions() as $filter_expression)
    {
      $filter_parser = new WactExpressionFilterParser($filter_expression);

      $filter_name = $filter_parser->getFilterName();

      if (is_null($filter_name))
        $this->context->raiseCompilerError('Invalid filter specification');

      $filter_info = $this->filter_dictionary->getFilterInfo($filter_name);

      if (!is_object($filter_info))
        $this->context->raiseCompilerError('Unknown filter', array('filter' => $filter_name));

      $base = $this->_createFilter($filter_name, $base, $filter_parser->getFilterArguments());
    }

    return $base;
  }

  protected function _createFilter($name, $base, $args = array())
  {
    $filter_info = $this->filter_dictionary->getFilterInfo($name);

    if (!is_object($filter_info))
      $this->context->raiseCompilerError('Unknown filter', array('filter' => $name));

    $filter_info->load();

    $filter_class = $filter_info->FilterClass;
    $filter = new $filter_class($this->context->getLocationInTemplate());

    if (is_array($args) && count($args))
    {
      $numArgs = count($args);

      if ($numArgs < $filter_info->MinParameterCount)
        $this->context->raiseCompilerError('Invalid or missing filter parameter', array('filter' => $name));

      if ($numArgs > $filter_info->MaxParameterCount)
        $this->context->raiseCompilerError('Too many parameters for filter', array('filter' => $name));

      foreach ($args as $value_expr)
        $filter->registerParameter($this->createValue($value_expr, $this->context));
    }

    $filter->registerBase($base);

    return $filter;
  }

  function isConstant()
  {
    return $this->parsed->isConstant();
  }

  function getValue()
  {
    return $this->parsed->getValue();
  }

  function generatePreStatement($code_writer)
  {
    $this->parsed->generatePreStatement($code_writer);
  }

  function generateExpression($code_writer)
  {
    $this->parsed->generateExpression($code_writer);
  }

  function generatePostStatement($code_writer)
  {
    $this->parsed->generatePostStatement($code_writer);
  }

  function prepare()
  {
    return $this->parsed->prepare();
  }

  function getFilterDictionary()
  {
    return $this->filter_dictionary;
  }
}
?>