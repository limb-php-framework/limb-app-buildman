<?php
//Builds project release.

$RELEASE_NAME = "buildman-0.0.1";
$RELEASE_DIR = "./release/";
$WORKING_COPY_DIR = dirname(__FILE__) . "/../";

$SVN = "svn";
$TAR = "$RELEASE_NAME.tar.gz";
$FULL_RELEASE_PATH = $RELEASE_DIR . $RELEASE_NAME;

//==================!!! don't edit below if not sure !!!==================

function cleanup()
{
  global $RELEASE_DIR;

  echo "Cleaning $RELEASE_DIR...\n";

  exec("rm -Rf $RELEASE_DIR", $out, $ret);

  if($ret != 0)
  {
    echo "...\nfailure\n";
    exit(1);
  }

  mkdir($RELEASE_DIR);
}

function svn_export_project()
{
  global $WORKING_COPY_DIR;
  global $FULL_RELEASE_PATH;
  global $SVN;

  echo "Exporting from svn...\n";

  exec("$SVN export $WORKING_COPY_DIR $FULL_RELEASE_PATH");
}

function process_release()
{
  global $FULL_RELEASE_PATH;

  exec("mv $FULL_RELEASE_PATH/projects-example $FULL_RELEASE_PATH/projects");
  exec("rm -rf $FULL_RELEASE_PATH/_fish");
  exec("rm -rf $FULL_RELEASE_PATH/build");
  mkdir("$FULL_RELEASE_PATH/var");
}

function make_archives()
{
  global $TAR;
  global $RELEASE_NAME;
  global $RELEASE_DIR;
  global $FULL_RELEASE_PATH;

  echo "Making release...\n";

  $DIR = getcwd();

  chdir($RELEASE_DIR);

  echo "Tarring release...\n";

  exec("tar -zcf $TAR $RELEASE_NAME");

  chdir($DIR);
}

cleanup();

svn_export_project();

process_release();

make_archives();

?>
