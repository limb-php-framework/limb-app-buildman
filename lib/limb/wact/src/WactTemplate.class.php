<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTemplate.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once('limb/wact/src/components/components.inc.php');

class WactTemplate extends WactDatasourceRuntimeComponent
{
  protected $template_path;

  protected $render_function;

  protected $config;

  protected $locator;

  protected $components;

  function __construct($template_path, $config = null, $locator = null)
  {
    parent :: __construct('root');

    if(!is_object($config))
    {
      require_once('limb/wact/src/WactDefaultTemplateConfig.class.php');
      $config = new WactDefaultTemplateConfig();
    }

    if(!is_object($locator))
    {
      require_once('limb/wact/src/locator/WactDefaultTemplateLocator.class.php');
      $locator = new WactDefaultTemplateLocator($config);
    }

    $this->config = $config;
    $this->locator = $locator;

    $this->template_path = $template_path;

    $compiled_template_path = $this->locator->locateCompiledTemplate($this->template_path);

    if (!isset($GLOBALS['TemplateRender'][$compiled_template_path]))
    {
      if (($this->config->isForceCompile()) || !file_exists($compiled_template_path))
      {
        include_once 'limb/wact/src/compiler/templatecompiler.inc.php';
        $compiler = new WactCompiler($config, $locator);
        $compiler->compile($this->template_path);
      }

      include_once($compiled_template_path);
    }

    $this->render_function = $GLOBALS['TemplateRender'][$compiled_template_path];
    $func = $GLOBALS['TemplateConstruct'][$compiled_template_path];
    $this->components = array();
    $func($this, $this->components);
  }


  /**
  * @return WactArrayObject
  **/
  static function makeObject($DataSource, $name)
  {
    $value = $DataSource->get($name);
    if (is_object($value))
      return new WactArrayObject($value);
    elseif(is_array($value))
      return new WactArrayObject($value);

    return new WactArrayObject(array());
  }

  /**
  * @return WactArrayIterator/Iterator
  **/
  static function castToIterator($value)
  {
    if(!$value || is_scalar($value))
      return new WactArrayIterator(array());

    if(is_array($value))
      return new WactArrayIterator($value);

    if($value instanceof IteratorAggregate)
      return $value->getIterator();

    return $value;
  }

  function display()
  {
    $func = $this->render_function;
    $func($this, $this->components);
  }

  function capture()
  {
    ob_start();
    $this->display();
    return ob_get_clean();
  }

  function getTemplatePath()
  {
    return $this->locator->locateSourceTemplate($this->template_path);
  }
}


?>