<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFilterChain.class.php 5068 2007-02-15 08:40:23Z pachanga $
 * @package    filter_chain
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

/**
 *  lmbFilterChain is an implementation of InterceptinfFilter design pattern.
 *
 *  lmbFilterChain contains registered filters and controls execution of the chain.
 *  Usually used as a FrontController in Limb based web applications (see web_app package)
 *
 *  The best way to think about filters is as of a "russian nested doll", e.g:
 *  <code>
 *  // +-Filter A
 *  // | +-Filter B
 *  // | | +-Filter C
 *  // | | |_
 *  // | |_
 *  // |_
 *  </code>
 *  To achieve this sample structure you should write the following code:
 *  <code>
 *  $chain = new lmbFilterChain();
 *  $chain->registerFilter(new A());
 *  $chain->registerFilter(new B());
 *  $chain->registerFilter(new C());
 *  </code>
 *
 *  Remember, it's the filter that decides whether to pass control to the
 *  underlying filter, this is done by calling filter chain instance next()
 *  method.
 *
 *  Usage example:
 *  <code>
 *  lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
 *  //create new chain
 *  $chain = new lmbFilterChain();
 *  //register filter object in the chain
 *  $chain->registerFilter(new MyFilter());
 *  //register a handle for a filter in the chain
 *  //in this case we can avoid PHP code parsing if
 *  //this filter won't be processed
 *  $chain->registerFilter(new lmbHandle('/path/to/MyFilter'));
 *  //executes the chain
 *  $chain->process();
 *  </code>
 *
 * @version $Id: lmbFilterChain.class.php 5068 2007-02-15 08:40:23Z pachanga $
 */
class lmbFilterChain
{
  /**
   * @var array registered filters (or filter handles (see {@link lmbHandle}))
   */
  protected $filters = array();
  /**
   * @var integer Index of the current active filter while running the chain
   */
  protected $counter = -1;

  function __construct(){}

  /**
   * Registers filter (or handle on a filter) in the chain.
   *
   * @return void
   */
  function registerFilter($filter)
  {
    $this->filters[] = $filter;
  }
  /**
   * Returns registered filters
   *
   * @return array
   */
  function getFilters()
  {
    return $this->filters;
  }
  /**
   * Runs next filter in the chain.
   *
   * @return void
   */
  function next()
  {
    $this->counter++;

    if(isset($this->filters[$this->counter]))
    {
      $this->filters[$this->counter]->run($this);
    }
  }
  /**
   * Executes the chain
   *
   * @return void
   */
  function process()
  {
    $this->counter = -1;
    $this->next();
  }
}

?>