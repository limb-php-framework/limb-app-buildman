<?php

class DefaultChangeLogExtractor
{
  function extract($wc, $from=null, $to=null)
  {
    $svn = BUILDMAN_SVN_BIN;
    $rev = ($from && $to) ? "-r$from:$to" : '';
    return `$svn log -v $rev $wc`;
  }
}

?>
