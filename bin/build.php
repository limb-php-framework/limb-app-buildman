<?php
require_once(dirname(__FILE__) . '/../setup.php');
require_once('src/model/Project.class.php');

class CliListener
{
  function notify($notifier, $msg)
  {
    echo $msg;
  }
}

if(!isset($argv[1]))
{
  foreach(Project :: findAllProjects() as $project)
  {
    $project->build(new CliListener());
  }
}
else
{
  if($project = Project :: findProject($argv[1]))
  {
    $project->build(new CliListener());
  }
  else
  {
    echo "Error: project '" . $argv[1] . "' not found\n";
    exit(1);
  }
}

