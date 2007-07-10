<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactFormComponent.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactFormComponent extends WactRuntimeTagComponent
{
  protected $error_list;

  protected $is_valid = TRUE;

  protected $state_vars = array();

  protected $_datasource;

  protected function _ensureDataSourceAvailable()
  {
    if (!isset($this->_datasource))
      $this->registerDataSource(new ArrayObject());
  }

  function get($name)
  {
    return $this->_datasource->get($name);
  }

  function set($name, $value)
  {
    $this->_datasource->set($name, $value);
  }

  /**
  * Get the named property from the form DataSource
  * @param string variable name
  * @return mixed value or void if not found
  * @access public
  * @deprecated will probablybe removed in a future reorganization of
  *   how form elements become associated with their values
  */
  function getValue($name)
  {
    $this->_ensureDataSourceAvailable();
    return $this->_datasource->get($name);
  }

  /**
  * Set a named property in the form DataSource
  */
  function setValue($name, $value)
  {
    $this->_ensureDataSourceAvailable();
    $this->_datasource->set($name, $value);
  }

  function prepare()
  {
    $this->_ensureDataSourceAvailable();
  }

  function registerDataSource($datasource)
  {
    $this->_datasource = new WactArrayObject($datasource);
  }

  function getDataSource()
  {
    return $this->_datasource->getInnerObject();
  }

  /**
  * Finds the WactLabelComponent associated with a form field, allowing
  * an error message to be displayed next to the field. Called by this
  * setErrors.
  */
  function findLabel($field_id, $component)
  {
    foreach( array_keys($component->children) as $key)
    {
      $child = $component->children[$key];
      if (is_a($child, 'WactLabelComponent') && $child->getAttribute('for') == $field_id)
        return $child;
      elseif ($result = $this->findLabel($field_id, $child))
       return $result;
    }
  }

  /**
  * If errors occur, use this method to identify them to the FormComponent.
  * (typically this is called for you by controllers)
  * @param ErrorList
  */
  function setErrors($ErrorList)
  {
    // Sets the human readable dictionary corresponding to form fields.
    // Entries in the dictionary defined by displayname attribute of tag
    $ErrorList->setFieldNameDictionary(new WactFormFieldNameDictionary($this));

    for ($ErrorList->rewind(); $ErrorList->valid(); $ErrorList->next()) {
        $this->is_valid = FALSE;
        $Error = $ErrorList->current();

        // Find the component(s) that the error applies to and tell
        // them there was an error (using their setError() method)
        // as well as notifying related label components if found
        foreach ($Error->getFieldsList() as $fieldName)
        {
          $Field = $this->findChild($fieldName);
          if (is_object($Field))
          {
            $Field->setError();
            if ($Field->hasAttribute('id'))
            {
              $Label = $this->findLabel($Field->getAttribute('id'), $this);
              if ($Label)
                $Label->setError();
            }
          }
        }
    }

    $this->error_list = $ErrorList;
  }

  function hasErrors()
  {
    return !$this->is_valid;
  }

  /**
  * Returns the ErrorList if it exists or an EmptyErrorList if not
  * (typically this is called for you by controllers)
  * @return object WactErrorList or EmptyArrayIterator
  */
  function getErrorDataSet()
  {
    if (!isset($this->error_list))
      $this->error_list = new ArrayIterator(array());

    return $this->error_list;
  }

  /**
  * Identify a property stored in the DataSource of the component, which
  * should be passed as a hidden input field in the form post. The name
  * attribute of the hidden input field will be the name of the property.
  * Use this to have properties persist between form submits
  * @see renderState()
  */
  function preserveState($variable)
  {
    $this->state_vars[] = $variable;
  }

  /**
  * Renders the hidden fields for variables which should be preserved.
  * Called from within a compiled template render function.
  */
  function renderState()
  {
    foreach ($this->state_vars as $var)
    {
      echo '<input type="hidden" name="';
      echo $var;
      echo '" value="';
      echo htmlspecialchars($this->getValue($var), ENT_QUOTES);
      echo '"/>';
    }
  }
}
?>