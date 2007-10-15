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

if (!isset($_SERVER['argv'][1])) {
  foreach(Project :: findAllProjects() as $project)
    $project->build(new CliListener());
} else {
  if ($project = Project :: findProject($_SERVER['argv'][1])) {
    $project->build(new CliListener());
  } else {
    echo 'Error: project '.$_SERVER['argv'][1].' not found';
  }
}

?>