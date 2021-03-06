<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactPropertyInfoExtractor.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactPropertyInfoExtractor
{
  protected $dictionary;
  protected $file;
  protected $annotations = array();

  function __construct($dictionary, $file)
  {
    $this->dictionary = $dictionary;
    $this->file = $file;
  }

  function setCurrentFile($file)
  {
    $this->file = $file;
  }

  function annotation($name, $value)
  {
    $this->annotations[$name] = $value;
  }

  function beginClass($class, $parent_class)
  {
    $this->_validate();

    $info = new WactPropertyInfo($this->annotations['property'], $this->annotations['tag_class'], $class);
    $this->dictionary->registerPropertyInfo($info, $this->file);
  }

  function endClass()
  {
    $this->annotations = array();
  }

  function _validate()
  {
    if(!file_exists($this->file))
        throw new WactException('File not found', array('file' => $this->file));

    if(!isset($this->annotations['property']))
        throw new WactException('Annotation not found in file',
                                array('annotation' => 'property', 'file' => $this->file));

    if(!isset($this->annotations['tag_class']))
        throw new WactException('Annotation not found in file',
                                array('annotation' => 'tag_class', 'file' => $this->file));

  }
}
