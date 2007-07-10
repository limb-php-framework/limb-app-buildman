<?php
require_once(dirname(__FILE__) . '/../src/model/Category.class.php');

class CategoryTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: mkdir(TEST_VAR_DIR);
  }

  function tearDown()
  {
    lmbFs :: rm(TEST_VAR_DIR);
  }

  function testFindCategories()
  {
    //cat1
    $this->_createProject(
    'foo',
    "repository=svn://svn.bit/1
    build_cmd=php %project_dir%/cli/build.php
    category=cat1");

    //cat2
    $this->_createProject(
    'zoo',
    "repository=svn://svn.bit/3
    build_cmd=php %project_dir%/cli/build.php
    category=cat2");

    //cat1
    $this->_createProject(
    'bar',
    "repository=svn://svn.bit/2
    build_cmd=php %project_dir%/cli/build.php
    category=cat1");

    //default category
    $this->_createProject(
    'wow',
    "repository=svn://svn.bit/4
    build_cmd=php %project_dir%/cli/build.php");

    //entries are sorted by name
    list($cat1, $cat2, $cat3) = Category :: findAllCategories();

    list($p0) = $cat1->getProjects();
    list($p1, $p2) = $cat2->getProjects();
    list($p3) = $cat3->getProjects();

    $this->assertEqual($p0->getName(), 'wow');//entries are sorted by name

    $this->assertEqual($p1->getName(), 'bar');
    $this->assertEqual($p2->getName(), 'foo');

    $this->assertEqual($p3->getName(), 'zoo');
  }

  function _createProject($name, $content)
  {
    lmbFs :: mkdir(BUILDMAN_PROJECTS_SETTINGS_DIR . '/' . $name);
    $file = BUILDMAN_PROJECTS_SETTINGS_DIR . '/' . $name . '/settings.ini';
    file_put_contents($file, $content);
  }
}

?>