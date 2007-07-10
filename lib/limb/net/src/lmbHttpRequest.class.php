<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbHttpRequest.class.php 5001 2007-02-08 15:36:45Z pachanga $
 * @package    net
 */
lmb_require('limb/datasource/src/lmbDataspace.class.php');
lmb_require('limb/net/src/lmbUri.class.php');
lmb_require('limb/net/src/lmbUploadedFilesParser.class.php');
lmb_require('limb/util/src/util/lmbComplexArray.class.php');

class lmbHttpRequest extends lmbDataspace
{
  protected $uri;
  protected $request = array();
  protected $get = array();
  protected $post = array();
  protected $cookies = array();
  protected $files = array();
  protected $pretend_post = false;

  function __construct($uri_string = null, $get = null, $post = null, $cookies = null, $files = null)
  {
    parent :: __construct();
    $this->_initRequestProperties($uri_string, $get, $post, $cookies, $files);
  }

  protected function _initRequestProperties($uri_string, $get, $post, $cookies, $files)
  {
    $this->uri = !is_null($uri_string) ? new lmbUri($uri_string) : new lmbUri($this->getRawUriString());

    $this->get = !is_null($get) ? $get : $_GET;
    $items = $this->uri->getQueryItems();
    foreach($items as $k => $v)
      $this->get[$k] = $v;

    $this->post = !is_null($post) ? $post : $_POST;
    $this->cookies = !is_null($cookies) ? $cookies : $_COOKIE;
    $this->files = !is_null($files) ? $this->_parseUploadedFiles($files) : $this->_parseUploadedFiles($_FILES);

    if(ini_get('magic_quotes_gpc'))
    {
      $this->get = $this->_stripHttpSlashes($this->get);
      $this->post = $this->_stripHttpSlashes($this->post);
      $this->cookies = $this->_stripHttpSlashes($this->cookies);
    }

    $this->request = lmbComplexArray :: arrayMerge($this->get, $this->post);

    //ugly hack, uploaded files shouldn't be attributes of request!!!
    $all = $this->request;
    if($this->files)
      $all = lmbComplexArray :: arrayMerge($all, $this->files);

    foreach($all as $k => $v)
      $this->set($k, $v);
  }

  protected function _parseUploadedFiles($files)
  {
    $parser = new lmbUploadedFilesParser();
    return $parser->parse($files);
  }

  protected function _stripHttpSlashes($data, $result=array())
  {
    foreach($data as $k => $v)
    {
      if(is_array($v))
        $result[$k] = $this->_stripHttpSlashes($v);
      else
        $result[$k] = stripslashes($v);
    }
    return $result;
  }

  function hasAttribute($name)//rename later
  {
    return isset($this->properties[$name]);
  }

  function getFiles($key = null)
  {
    return $this->_get('files', $key);
  }

  function getRequest($key = null)
  {
    return $this->_get('request', $key);
  }

  function getGet($key = null)
  {
    return $this->_get('get', $key);
  }

  function getPost($key = null)
  {
    return $this->_get('post', $key);
  }

  function hasPost()
  {
    if($this->pretend_post)
      return true;

    return sizeof($this->post) > 0 ||
      (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST');
  }

  function pretendPost($flag = true)
  {
    $this->pretend_post = $flag;
  }

  function getCookie($key = null)
  {
    return $this->_get('cookies', $key);
  }

  protected function _get($var, $key = null)
  {
    if(is_null($key))
      return $this->$var;

    $arr = $this->$var;
    if(isset($arr[$key]))
      return $arr[$key];
  }

  function getUri()
  {
    return $this->uri;
  }

  function getUriPath()
  {
    return $this->uri->getPath();
  }

  function getRawUriString()
  {
    $host = 'localhost';
    if(!empty($_SERVER['HTTP_HOST']))
      list($host) = explode(':', $_SERVER['HTTP_HOST']);
    elseif(!empty($_SERVER['SERVER_NAME']))
      list($host) = explode(':', $_SERVER['SERVER_NAME']);

    if(isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on'))
      $protocol = 'https';
    else
      $protocol = 'http';

    if(!isset($port) || $port != intval($port))
      $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;

    if($protocol == 'http' && $port == 80)
      $port = null;

    if($protocol == 'https' && $port == 443)
      $port = null;

    $server = $protocol . '://' . $host . (isset($port) ? ':' . $port : '');

    if(isset($_SERVER['REQUEST_URI']))
      $url = $_SERVER['REQUEST_URI'];
    elseif(isset($_SERVER['QUERY_STRING']))
      $url = basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING'];
    else
      $url = $_SERVER['PHP_SELF'];

    return $server . $url;
  }

  function toString()
  {
    //ugly hack: making a copy(uri must be a value object)
    $uri = clone $this->uri;
    $uri->removeQueryItems();

    $result = array();
    $query = '';

    $exported = $this->export();

    //ugly hack again: removing uploaded files data
    if(isset($exported['file']))
      unset($exported['file']);

    lmbComplexArray :: toFlatArray($exported, $result);

    foreach($result as $key => $value)
      $query .= $key . '=' . $value . '&';

    return rtrim($uri->toString() . '?' . rtrim($query, '&'), '?');
  }

  function dump()
  {
    return $this->toString();
  }
}

?>