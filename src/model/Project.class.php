<?php
lmb_require('limb/classkit/src/lmbObject.class.php');
lmb_require('limb/util/src/system/lmbFs.class.php');
lmb_require('limb/util/src/system/lmbSys.class.php');
lmb_require('limb/mail/src/lmbMailer.class.php');
lmb_require('limb/config/src/lmbCachedIni.class.php');
lmb_require('src/model/Build.class.php');

@define('BUILDMAN_HOST', 'buildman');
@define('BUILDMAN_MAIL_ADDR', 'buildman@localhost');
@define('BUILDMAN_WEB_SERVER', 'http://' . BUILDMAN_HOST);
@define('BUILDMAN_SVN_BIN', 'svn');
@define('BUILDMAN_RSYNC_BIN', 'rsync');

class Project extends lmbObject
{
  protected $listener;
  protected $buildlog_processor;
  protected $build_rev;
  protected $build_date;
  protected $log;
  protected $build;

  function __construct($name)
  {
    $this->setName($name);
  }

  static function createFromIni($name, $file, $shared_file=null)
  {
    $project = new Project($name);

    $ini = new lmbCachedIni($file);

    if($shared_file)
    {
      $shared_ini = new lmbCachedIni($shared_file);
      $project->import($shared_ini->getGroup('default'));
    }

    $project->import($ini->getGroup('default'));
    return $project;
  }

  static function findAllProjects()
  {
    $common_ini = null;
    if(file_exists(BUILDMAN_PROJECTS_SETTINGS_DIR . '/common.ini'))
      $common_ini = BUILDMAN_PROJECTS_SETTINGS_DIR . '/common.ini';

    $projects = array();
    foreach(scandir(BUILDMAN_PROJECTS_SETTINGS_DIR) as $item)
    {
      if($item{0} == '.' || !is_dir(BUILDMAN_PROJECTS_SETTINGS_DIR . '/' . $item))
        continue;

      $project = self :: createFromIni($item,
                                       BUILDMAN_PROJECTS_SETTINGS_DIR . '/' . $item . '/settings.ini',
                                       $common_ini);
      $projects[] = $project;
    }
    return $projects;
  }

  static function findProject($name)
  {
    foreach(self :: findAllProjects() as $project)
    {
      if($project->getName() == $name)
        return $project;
    }
  }

  function build($listener=null, $buildlog_processor=null)
  {
    if(!$this->_doBuild($listener, $buildlog_processor))
      $this->_notifySubscribersOnError();
  }

  protected function _doBuild($listener=null, $buildlog_processor=null)
  {
    $this->listener = $listener;
    $this->buildlog_processor = $buildlog_processor;

    try
    {
      $this->_prepare();

      $this->_resetBuildDate();

      $this->_notify("Starting fresh build of '" . $this->getName() . "' project...\n\n");

      $this->_notify("Updating working copy\n\n");

      if($this->existsWc())
        $this->_execCmd($this->getUpdateWcCmd());
      else
        $this->_execCmd($this->getCheckoutWcCmd());

      $changelog = $this->retrieveChangelog();

      $this->_notify("Syncing working copy with temp build directory...\n\n");

      $this->_syncProjectDirWithWc();

      $this->build = $this->createBuild($this->getWcRev(), $this->build_date);

      $this->_notify("New build is '" . $this->build->getBuildName() . "'\n\n");

      $is_ok = true;

      if(!$this->_tryBuild($this->build))
      {
        $is_ok = false;
        $this->_notify("Build had errors!\n\n");
      }
      else
        $this->_notify("Build is successful!\n\n");

      $this->build->createChangelog($changelog);

      $this->_notify("Copying build directory to web builds..\n\n");
      $this->build->copyFile($this->getProjectDir(), $this->build->getBuildName());

      $this->_notify("Zipping new build..\n\n");
      $this->build->zipFile($this->build->getBuildName(), $this->build->getBuildName() . '.zip');
      $this->_notify("Gzipping new build..\n\n");
      $this->build->gzipFile($this->build->getBuildName(), $this->build->getBuildName() . '.tar.gz');

      $this->_notify("Removing junk..\n\n");
      $this->build->removeFile($this->build->getBuildName());
      $this->_removeRedundantBuilds();

      $this->_notify("All done!\n\n");

      $this->build->createBuildlog($this->getBuildLog());
      return $is_ok;
    }
    catch(Exception $e)
    {
      $this->_notify("Build had errors!\n\n");
      $this->_notify($e->getMessage());
      return false;
    }
  }

  protected function _removeRedundantBuilds()
  {
    if(!$max = $this->getMaxBuilds())
      return;

    $c = 0;
    foreach($this->getBuilds() as $build)
    {
      $c++;
      if($c > $max)
        $this->removeBuild($build);
    }
  }

  protected function _notify($msg)
  {
    $fh = fopen($this->getLogFile(), 'a');
    fwrite($fh, $msg);
    fclose($fh);

    $this->log .= $msg;

    if($this->listener)
      $this->listener->notify($this, $msg);
  }

  protected function _notifySubscribersOnError()
  {
    if($subscribers = $this->getSubscribers())
    {
      if($this->build)
        $build_name = $this->build->getBuildName();
      else
        $build_name = 'unfinished';

      $build_title = "Project '" . $this->getName() . "' build '$build_name'";

      $build_log = $this->getBuildLog();

      $mailer = new lmbMailer();

      $mail = <<<EOD
BUILD ERROR

$build_title

The build log is as follows

================= BUILD.LOG =================
$build_log

EOD;

      $mailer->sendPlainMail($subscribers,
                             BUILDMAN_MAIL_ADDR,
                             'BUILD ERROR: ' . $build_title,
                             $mail);
    }
  }

  function getBuildLog()
  {
    $contents = $this->log;
    if($this->buildlog_processor)
      return $this->buildlog_processor->process($contents);

    return $contents;
  }

  function retrieveChangelog($from=null, $to=null)
  {
    $svn = BUILDMAN_SVN_BIN;
    $scm_opts = $this->_getRaw('scm_opts');
    $rev = ($from && $to) ? "-r$from:$to" : '';
    $wc = $this->getWc();
    return `$svn log $scm_opts -v $rev $wc`;
  }

  function _tryBuild($build)
  {
    $build->markInProgress();

    try
    {
      if($cmd = $this->getBuildCmd())
        $this->_execCmd($cmd);

      $build->markOk();
      return true;
    }
    catch(Exception $e)
    {
      $build->markError();
      return false;
    }
  }

  function createBuild($rev, $time)
  {
    $dir = $this->getProjectBuildsDir();
    $build = Build :: createBuild($dir, $this->getName(), $rev, $time);
    return $build;
  }

  function removeBuild($build)
  {
    lmbFs :: rm($build->getBuildDir());
  }

  function getBuilds()
  {
    $dir = $this->getProjectBuildsDir();
    $builds = array();
    foreach(scandir($dir) as $item)
    {
      if($item != '.' && $item != '..' && is_dir($dir . '/' . $item) && preg_match('~r(\d+)~',$item,$matches)) {
        $builds[intval($matches[1])] = new Build($dir . '/' . $item);
      }

    }
    krsort($builds);
    return array_values($builds);
  }

  function getLastBuild()
  {
    if($builds = $this->getBuilds())
      return $builds[0];
  }

  function getProjectBuildsDir()
  {
    lmbFs :: mkdir(BUILDMAN_PROJECTS_BUILDS_DIR);
    return BUILDMAN_PROJECTS_BUILDS_DIR . $this->getName();
  }

  function getProjectDir()
  {
    lmbFs :: mkdir(BUILDMAN_PROJECTS_SANDBOX_DIR);
    return BUILDMAN_PROJECTS_SANDBOX_DIR . $this->getName();
  }

  function getSettingsDir()
  {
    return BUILDMAN_PROJECTS_SETTINGS_DIR . $this->getName();
  }

  function getBuildCmd()
  {
    return $this->_getFilled('build_cmd');
  }

  function existsWc()
  {
    return is_dir($this->getWc());
  }

  function getCheckoutWcCmd()
  {
    $scm_opts = $this->_getRaw('scm_opts');
    return BUILDMAN_SVN_BIN . ' co --non-interactive ' . $scm_opts . ' ' . $this->getRepository() . ' ' . $this->getWc();
  }

  function getUpdateWcCmd()
  {
    $scm_opts = $this->_getRaw('scm_opts');
    return BUILDMAN_SVN_BIN . ' up --non-interactive ' . $scm_opts . ' ' . $this->getWc();
  }

  function getWcRev()
  {
    return $this->_getRev($this->getWc());
  }

  function getRepositoryRev()
  {
    return $this->_getRev($this->getRepository());
  }

  protected function _getRev($path)
  {
    $svn = BUILDMAN_SVN_BIN;
    $scm_opts = $this->_getRaw('scm_opts');
    $cmd = "$svn info --xml $scm_opts $path";
    $xmlstr = `$cmd`;
    $xml = new SimpleXMLElement($xmlstr);
    return $xml->entry[0]->commit['revision'];
  }

  function getWc()
  {
    lmbFs :: mkdir(LIMB_VAR_DIR . '/wc/');
    return BUILDMAN_PROJECTS_WC_DIR . $this->getName();
  }

  function getIsChanged()
  {
    return $this->getLastBuildRev() != $this->getRepositoryRev();
  }

  function getLastBuildRevFile()
  {
    return LIMB_VAR_DIR . '/.'. $this->getName() . '.rev';
  }

  function getLastBuildDate()
  {
    return $this->_getFileContents($this->getLastBuildDateFile());
  }

  function getLastBuildRev()
  {
    return $this->_getFileContents($this->getLastBuildRevFile());
  }

  function getBuildsAmount()
  {
    return sizeof($this->getBuilds());
  }

  protected function _updateLastRev()
  {
    file_put_contents($this->getLastBuildRevFile(), $this->build_rev);
  }

  protected function _updateLastBuildDate()
  {
    file_put_contents($this->getLastBuildDateFile(), $this->build_date);
  }

  protected function _prepare()
  {
    $this->log = '';
    $this->build = null;

    if(file_exists($this->getLogFile()))
      unlink($this->getLogFile());
  }

  function getLogFile()
  {
    return LIMB_VAR_DIR . '/.' . $this->getName() . '.' . date('Y-m-d_H-i-s', $this->build_date) . '.log';
  }

  function getLockFile()
  {
    return LIMB_VAR_DIR . '/.' . $this->getName() . '.lock';
  }

  function getIsLocked()
  {
    return file_exists($this->getLockFile());
  }

  protected function _syncProjectDirWithWc()
  {
    $rsync_opts = $this->_getRaw('rsync_opts');
    $this->_execCmd(BUILDMAN_RSYNC_BIN . ' -CaO --include=*.exe --include=tags --include=core --delete ' .
                    $rsync_opts . ' ' .
                    $this->_cygwinPath($this->getWc()) . '/ ' .
                    $this->_cygwinPath($this->getProjectDir()));
  }

  protected function _cygwinPath($path)
  {
    $path = lmbFs :: normalizePath($path);

    if(lmbSys :: isWin32())
      return preg_replace('~^(\w+):~', '/cygdrive/$1', preg_replace('~\\\\+~', '/', strtolower($path)));

    return $path;
  }

  protected function _resetBuildDate()
  {
    $this->build_date = time();
  }

  function setBuildDate($build_date)
  {
    $this->build_date = $build_date;
  }

  function getBuildDate()
  {
    if(!$this->build_date)
      $this->_resetBuildDate();

    return $this->build_date;
  }

  protected function _resetSyncRev()
  {
    $this->build_rev = $this->getWcRev();
  }

  protected function _execCmd($cmd)
  {
    if(!$cmd)
      return;

    $proc = popen("$cmd 2>&1", 'r');

    $this->_notify("Executing '$cmd'...\n");
    while(!feof($proc))
    {
     $log = fread($proc, 1000);
     $this->_notify($log);
    }
    $this->_notify("\nfinished.\n\n");

    $res = pclose($proc);

    if($res != 0)
    {
      $msg = "ERROR: '$cmd' execution failed, return status is '$res'";
      $this->_notify("$msg\n");
      throw new Exception($msg);
    }
  }

  protected function _getFileContents($file)
  {
    if(!file_exists($file))
      return '';

    return trim(file_get_contents($file));
  }

  protected function _fillTemplate($str)
  {
    return str_replace(array('%wc%',
                             '%project_dir%'
                             ),
                       array($this->getWc(),
                             $this->getProjectDir(),
                             ),
                             $str);
  }

  protected function _getFilled($name)
  {
    $value = parent :: _getRaw($name);
    return $this->_fillTemplate($value);
  }
}

?>
