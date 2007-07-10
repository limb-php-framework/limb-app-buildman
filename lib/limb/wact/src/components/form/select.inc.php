<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select.inc.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/components/form/form.inc.php';

/**
* Represents an HTML select multiple tag where multiple options
* can be selected
*/
class WactSelectMultipleComponent extends WactFormElement
{
  /**
  * A associative array of choices to build the option list with
  * @var array
  * @access private
  */
  var $choice_list = array();

  protected $default_selection = array();
  /**
  * The object responsible for rendering the option tags
  * @var object
  * @access private
  */
  var $option_handler;

  /**
  * Override WactFormElement method to deal with name attributes containing
  * PHP array syntax.
  * @return array the contents of the value
  * @access private
  */
  function getValue()
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    $name = str_replace('[]', '', $this->getAttribute('name'));
    return $form_component->getValue($name);
  }

  /**
  * Sets the choice list. Passed an associative array, the keys become the
  * contents of the option value attributes and the values in the array
  * become the text contents of the option tag e.g.
  * <code>
  * $choices = array ( 4 => 'red', 5=>'blue', 6=>'green' );
  * </code>
  * ...becomes...
  * <pre>
  * <select multiple>
  *   <option value="4">red</option>
  *   <option value="5">blue</option>
  *   <option value="6">green</option>
  * </select>
  * </pre>
  * @see setSelection()
  * @param array
  * @return void
  * @access public
  */
  function setChoices($choice_list)
  {
    $this->choice_list = $choice_list;
  }

  function addToChoices($key, $value)
  {
    $this->choice_list[$key] = $value;
  }

  function addToDefaultSelection($selection)
  {
    $this->default_selection[] = $selection;
  }

  function setSelection($selection)
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    $name = str_replace('[]', '', $this->getAttribute('name'));
    $form_component->setValue($name, $selection);
  }

  /**
  * Sets object responsible for rendering the options
  * Supply your own WactOptionRenderer if the default
  * is too simple
  * @see WactOptionRenderer
  * @param object
  * @return void
  * @access public
  */
  function setOptionRenderer($option_handler) {
      $this->option_handler = $option_handler;
  }

  /**
  * Renders the contents of the the select tag, option tags being built by
  * the option handler. Called from with a compiled template render function.
  * @return void
  * @access public
  */
  function renderContents()
  {
    $values = $this->getValue();
    if (!is_object($values) && !is_array($values))
      $values = $this->default_selection;

    if (empty($this->option_handler))
      $this->option_handler = new WactOptionRenderer();

    if(!$select_field = $this->getAttribute('select_field'))
      $select_field = 'id';

    foreach($this->choice_list as $key => $choice)
    {
      $selected = false;
      foreach($values as $value)
      {
        if(is_scalar($value) && $key == $value)
        {
          $selected = true;
          break;
        }
        elseif(!is_scalar($value) && $value[$select_field] == $key)
        {
          $selected = true;
          break;
        }
      }

      $this->option_handler->renderOption($key, $choice, $selected);
    }
  }
}

//--------------------------------------------------------------------------------
/**
* Deals with rendering option elements for HTML select tags
* Simple renderer for OPTIONs.  Does not support disabled
* and label attributes. Does not support OPTGROUP tags.
* @package wact
*/
class WactOptionRenderer {

  /**
  * Renders an option, sending directly to display.
  * Called from WactSelectSingleComponent or WactSelectMultipleComponent
  * in their renderContents() method
  * @todo XTHML: selected="selected"
  * @param string value to place within the option value attribute
  * @param string contents of the option tag
  * @param boolean whether the option is selected or not
  * @return void
  * @access private
  */
  function renderOption($key, $contents, $selected) {
      echo '<option value="';
      echo htmlspecialchars($key, ENT_QUOTES);
      echo '"';
      if ($selected) {
          echo " selected";
      }
      echo '>';
      if (empty($contents)) {
          echo htmlspecialchars($key, ENT_QUOTES);
      } else {
          echo htmlspecialchars($contents, ENT_QUOTES);
      }
      echo '</option>';
  }
}

//--------------------------------------------------------------------------------
/**
* Represents an HTML select tag where only a single option can
* be selected
* @package wact
*/
class WactSelectSingleComponent extends WactFormElement
{
  /**
  * A associative array of choices to build the option list with
  * @var array
  * @access private
  */
  var $choice_list = array();

  protected $default_selection = null;
  /**
  * The object responsible for rendering the option tags
  * @var object
  * @access private
  */
  var $option_handler;

  /**
  * Sets the choice list. Passed an associative array, the keys become the
  * contents of the option value attributes and the values in the array
  * become the text contents of the option tag e.g.
  * <code>
  * $choices = array ( 4 => 'red', 5=>'blue', 6=>'green' );
  * </code>
  * ...becomes...
  * <pre>
  * <select>
  *   <option value="4">red</option>
  *   <option value="5">blue</option>
  *   <option value="6">green</option>
  * </select>
  * </pre>
  * @see setSelection()
  * @param array
  * @return void
  * @access public
  */
  function setChoices($choice_list) {
      $this->choice_list = $choice_list;
  }

  function addToChoices($key, $choice)
  {
    $this->choice_list[$key] = $choice;
  }

  function addToDefaultSelection($selection)
  {
    $this->default_selection = $selection;
  }

  function setSelection($selection)
  {
    $form_component = $this->findParentByClass('WactFormComponent');
    $form_component->setValue($this->getAttribute('name'), $selection);
  }

  /**
  * Sets object responsible for rendering the options
  * Supply your own WactOptionRenderer if the default
  * is too simple
  * @see WactOptionRenderer
  */
  function setOptionRenderer($option_handler)
  {
    $this->option_handler = $option_handler;
  }

  /**
  * Renders the contents of the the select tag, option tags being built by
  * the option handler. Called from with a compiled template render function.
  */
  function renderContents()
  {
    $value = $this->getValue();
    if(is_null($value))
      $value = $this->default_selection;

    if (!is_object($this->option_handler))
      $this->option_handler = new WactOptionRenderer();

    if(!$select_field = $this->getAttribute('select_field'))
      $select_field = 'id';

    foreach($this->choice_list as $key => $choice)
    {
      if(!is_scalar($value))
        $selected = $value[$select_field];
      else
        $selected = $value;

      $this->option_handler->renderOption($key, $choice, $key == $selected);
    }
  }
}

?>