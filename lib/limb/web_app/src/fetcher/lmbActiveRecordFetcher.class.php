<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbActiveRecordFetcher.class.php 5097 2007-02-16 13:05:25Z serega $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/dbal/src/lmbTableGateway.class.php');
lmb_require('limb/classkit/src/lmbClassPath.class.php');
lmb_require('limb/datasource/src/lmbEmptyIterator.class.php');
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

class lmbActiveRecordFetcher extends lmbFetcher
{
  protected $class_path;
  protected $record_id;
  protected $record_ids;
  protected $find;
  protected $find_params = array();

  function setClassPath($value)
  {
    $this->class_path = $value;
  }

  function setRecordId($value)
  {
    if(!$value)
      $value = '';
    $this->record_id = $value;
  }

  function setFind($find)
  {
    $this->find = $find;
  }

  function addFindParam($value)
  {
    $this->find_params[] = $value;
  }

  function setFindParams($find_params)
  {
    $this->find_params = $find_params;
  }

  function setRecordIds($value)
  {
    if(!is_array($value))
      $this->record_ids = array();
    else
      $this->record_ids = $value;
  }

  function _createDataSet()
  {
    if(!$this->class_path)
      throw new lmbException('Class path is not defined!');

    $class_path = new lmbClassPath($this->class_path);
    $class_path->import();
    $class_name = $class_path->getClassName();

    if(is_null($this->record_id) && is_null($this->record_ids))
    {
      if(!$this->find)
      {
        return lmbActiveRecord :: find($class_name);
      }
      else
      {
        $method = 'find' . toStudlyCaps($this->find);
        if(!method_exists($class_name, $method))
         throw new lmbException('Active record of class "'. $class_name . '" does not support method "'. $method . '"');
        return call_user_func_array(array($class_name, $method), $this->find_params);
      }
    }

    if($this->record_id)
    {
      try
      {
        $record = lmbActiveRecord :: findById($class_name, $this->record_id);
      }
      catch(lmbARNotFoundException $e)
      {
        $record = array();
      }

      return $this->_singleItemCollection($record);
    }
    elseif($this->record_ids)
    {
      return lmbActiveRecord :: findByIds($class_name, $this->record_ids);
    }

    return new lmbEmptyIterator();
  }

  protected function _singleItemCollection($ar)
  {
    return new lmbArrayDataset(array($ar));
  }
}

?>
