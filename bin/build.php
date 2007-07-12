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

foreach(Project :: findAllProjects() as $project)
  $project->build(new CliListener());

?>