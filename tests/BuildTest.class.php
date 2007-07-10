<?php
require_once(dirname(__FILE__) . '/../src/model/Build.class.php');

class BuildTest extends UnitTestCase
{
  function setUp()
  {
    lmbFs :: mkdir(TEST_VAR_DIR);
  }

  function tearDown()
  {
    lmbFs :: rm(TEST_VAR_DIR);
  }

  function testCreateBuild()
  {
    $build = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev = 31, $time = time());

    $this->assertEqual($build->getProjectName(), 'foo');
    $this->assertEqual($build->getBuildRevision(), $rev);
    $this->assertEqual($build->getBuildStamp(), $time);
  }

  function testCopyFile()
  {
    $build = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev = 32, $time = time());
    file_put_contents(TEST_VAR_DIR . '/foo', 'blah');
    $build->copyFile(TEST_VAR_DIR . '/foo');

    $files = $build->listFiles();

    $this->assertEqual(sizeof($files), 1);
    $this->assertEqual($files[0], $build->getBuildDir() . '/foo');
    $this->assertEqual(file_get_contents($files[0]), 'blah');
  }

  function testCreateFile()
  {
    $build = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev = 72, $time = time());
    $build->createFile('foo.test', 'yahoo');

    $files = $build->listFiles();

    $this->assertEqual(sizeof($files), 1);
    $this->assertEqual($files[0], $build->getBuildDir() . '/foo.test');
    $this->assertEqual(file_get_contents($files[0]), 'yahoo');
  }

  function testDefaultIsUndefState()
  {
    $build = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev = 21, $time = time());
    $this->assertEqual(Build :: STATE_UNDEF, $build->getState());
    $this->assertFalse($build->isError());
  }

  function testMarkOk()
  {
    $build1 = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev = 378, $time = time());
    $build1->markOk();
    $this->assertFalse($build1->isError());
    $this->assertEqual(Build :: STATE_OK, $build1->getState());

    $build2 = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev, $time);
    $this->assertFalse($build2->isError());
    $this->assertEqual(Build :: STATE_OK, $build2->getState());
  }

  function testMarkError()
  {
    $build1 = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev = 48, $time = time());
    $this->assertFalse($build1->isError());
    $build1->markError();
    $this->assertTrue($build1->isError());
    $this->assertEqual(Build :: STATE_ERROR, $build1->getState());

    $build2 = Build :: createBuild(TEST_VAR_DIR, 'foo', $rev, $time);
    $this->assertEqual(Build :: STATE_ERROR, $build2->getState());
    $this->assertTrue($build2->isError());
  }
}

?>