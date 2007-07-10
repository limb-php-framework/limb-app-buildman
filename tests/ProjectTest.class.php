<?php
require_once(dirname(__FILE__) . '/../src/model/Project.class.php');

class ProjectTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: mkdir(TEST_VAR_DIR);
  }

  function tearDown()
  {
    lmbFs :: rm(TEST_VAR_DIR);
  }

  function testCreateFromIni()
  {
    $ini = $this->_createIni("
    repository=svn://svn.bit/
    build_cmd=php %project_dir%/cli/build.php
    ");

    $project = Project :: createFromIni('foo', $ini);

    $this->assertEqual($project->getName(), 'foo');
    $this->assertEqual($project->getProjectDir(), BUILDMAN_PROJECTS_SANDBOX_DIR . 'foo');
    $this->assertEqual($project->getBuildCmd(), 'php ' . $project->getProjectDir() . '/cli/build.php');
  }

  function testCreateFromIniMergeWithSharedIni()
  {
    $main = $this->_createIni("
    repository=svn://svn.bit/
    build_cmd=php %project_dir%/cli/build.php
    ");

    $local = $this->_createIni("
    build_cmd=php %project_dir%/cli/build2.php
    something_else=hey
    ");

    $project = Project :: createFromIni('foo', $local, $main);

    $this->assertEqual($project->getName(), 'foo');
    $this->assertEqual($project->getProjectDir(), BUILDMAN_PROJECTS_SANDBOX_DIR . 'foo');
    $this->assertEqual($project->getRepository(), 'svn://svn.bit/');
    $this->assertEqual($project->getBuildCmd(), 'php ' . $project->getProjectDir() . '/cli/build2.php');
    $this->assertEqual($project->getSomethingElse(), 'hey');
  }

  function testCreateBuild()
  {
    $project1 = new Project('foo');

    $b1 = $project1->createBuild($rev1 = 21, $time1 = time());
    $b2 = $project1->createBuild($rev2 = 34, $time2 = time() + 100);

    $this->assertEqual($b1->getBuildStamp(), $time1);
    $this->assertEqual($b2->getBuildStamp(), $time2);

    $project2 = new Project('foo');
    $this->assertEqual($project2->getBuilds(), array($b2, $b1));//latest come first!
  }

  function testRemoveBuild()
  {
    $project1 = new Project('foo');

    $b1 = $project1->createBuild($rev1 = 21, $time1 = time());
    $b2 = $project1->createBuild($rev2 = 34, $time2 = time() + 100);

    $project1->removeBuild($b1);

    $project2 = new Project('foo');
    $this->assertEqual($project2->getBuilds(), array($b2));
  }

  function _testBuild()
  {
    $project = new Project('foo');
    $project->setRepository();

    $project->build();
  }

  function _createIni($content)
  {
    $file = TEST_VAR_DIR . '/project' . mt_rand() . '.ini';
    file_put_contents($file, $content);
    return $file;
  }
}

?>