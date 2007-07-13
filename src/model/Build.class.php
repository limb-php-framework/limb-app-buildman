<?php

lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');

class Build extends lmbObject
{
  const BUILD_NAME_REGEX = '~^r(\d+)-((\d+)_(\d+)_(\d+)-(\d+)_(\d+)_(\d+))-([-\w\._]+)$~';
  const BUILD_TIME_FORMAT = 'Y_m_d-H_i_s';

  const STATE_UNDEF    = 1;
  const STATE_OK       = 2;
  const STATE_ERROR    = 3;
  const STATE_PROGRESS = 4;

  protected $state;
  protected $build_dir;
  protected $build_name;
  protected $project_name;
  protected $build_revision;
  protected $build_date;
  protected $build_stamp;

  function __construct($build_dir)
  {
    $this->build_dir = $build_dir;
    $this->build_name = basename($build_dir);

    //add exception handling later
    preg_match(Build :: BUILD_NAME_REGEX, $this->build_name, $m);
    $this->project_name = $m[9];
    $this->build_revision = $m[1];
    $this->build_date = $m[2];
    $this->build_stamp = mktime($m[6], $m[7], $m[8], $m[4], $m[5], $m[3]);

    if(file_exists($state_file = $this->getStateFile()))
      $this->state = file_get_contents($state_file);
    else
      $this->state = self :: STATE_UNDEF;
  }

  function getBuildWebDir()
  {
    return BUILDMAN_WEB_DIR . 'builds/' . $this->project_name . '/' . $this->build_name;
  }

  function getChangelogWebPath()
  {
    return $this->getBuildWebDir() . '/CHANGE.LOG';
  }

  function getBuildlogWebPath()
  {
    return $this->getBuildWebDir() . '/BUILD.LOG';
  }

  function getBuildLog()
  {
    return file_get_contents($this->build_dir . '/BUILD.LOG');
  }

  function markOk()
  {
    $this->setState(self :: STATE_OK);
  }

  function markError()
  {
    $this->setState(self :: STATE_ERROR);
  }

  function markInProgress()
  {
    $this->setState(self :: STATE_PROGRESS);
  }

  function getIsError()
  {
    return $this->getState() == self :: STATE_ERROR;
  }

  function getIsOk()
  {
    return $this->getState() == self :: STATE_OK;
  }

  function getIsUndefined()
  {
    return $this->getState() == self :: STATE_UNDEF;
  }

  function getIsInProgress()
  {
    return $this->getState() == self :: STATE_PROGRESS;
  }

  function setState($state)
  {
    $this->state = $state;
    file_put_contents($this->getStateFile(), $state);
  }

  function getStateFile()
  {
    return $this->build_dir . '/.state';
  }

  static function createBuild($builds_dir, $project, $rev, $time)
  {
    $name = self :: makeBuildDirName($project, $rev, $time);
    lmbFs :: mkdir($dir = "$builds_dir/$name");
    return new Build($dir);
  }

  static function makeBuildDirName($project, $rev, $time)
  {
    return 'r' . $rev . '-' . date(Build :: BUILD_TIME_FORMAT, $time) .  '-' . $project;
  }

  function copyFile($src, $dst=null)
  {
    if(!$dst)
      $dst = basename($src);
    $cmd = BUILDMAN_CP_BIN . " -rp $src {$this->build_dir}/$dst";
    `$cmd`;
  }

  function createFile($name, $contents)
  {
    file_put_contents($this->build_dir . '/' . $name, $contents);
  }

  function createChangelog($contents)
  {
    $this->createFile('CHANGE.LOG', $contents);
  }

  function createBuildlog($contents)
  {
    $this->createFile('BUILD.LOG', $contents);
  }

  function removeFile($name)
  {
    lmbFs :: rm($this->build_dir . '/' . $name);
  }

  function zipFile($name, $archive_name)
  {
    $file = $this->build_dir . '/' . $name;
    $archive = $this->build_dir . '/' . $archive_name;

    $zip = BUILDMAN_GZIP_BIN;
    $cat = BUILDMAN_CAT_BIN;

    if(is_dir($file))
    {
      $old = getcwd();
      $dir = dirname($file);
      $name = basename($file);
      chdir($dir);
      `$zip -r -9 -q $archive $name`;
      chdir($old);
    }
    else
      `$cat $file | $zip -9 -q > $archive`;
  }

  function gzipFile($name, $archive_name)
  {
    $file = $this->build_dir . '/' . $name;
    $archive = $this->build_dir . '/' . $archive_name;

    $tar = BUILDMAN_TAR_BIN;
    $gzip = BUILDMAN_GZIP_BIN;
    $cat = BUILDMAN_CAT_BIN;

    if(is_dir($file))
    {
      $old = getcwd();
      $dir = dirname($file);
      $name = basename($file);
      chdir($dir);
      `$tar cf - $name | $gzip -9 -c > $archive`;
      chdir($old);
    }
    else
      `$cat $file | $gzip -9 -c > $archive`;
  }

  function listFiles()
  {
    $files = array();
    foreach(scandir($this->build_dir) as $file)
    {
      if($file == '.' || $file == '..')
        continue;
      $files[] = $this->build_dir . '/' . $file;
    }
    return $files;
  }
}

?>
