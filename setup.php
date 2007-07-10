<?php
set_time_limit(0);

set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR .
                 dirname(__FILE__) . '/lib/' . PATH_SEPARATOR);

if(file_exists(dirname(__FILE__) . '/setup.override.php'))
  require_once(dirname(__FILE__) . '/setup.override.php');

@define('LIMB_USE_NATIVE_SESSION_DRIVER', true);
@define('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');
@define('WACT_CONFIG_DIRECTORY', dirname(__FILE__) . '/settings/wact/');

define('BUILDMAN_VERSION', trim(file_get_contents(dirname(__FILE__) . '/VERSION')));
@define('BUILDMAN_HOST', 'buildman');
@define('BUILDMAN_WEB_SERVER', 'http://' . BUILDMAN_HOST);
@define('BUILDMAN_WEB_DIR', '/');
@define('BUILDMAN_SVN_BIN', 'svn');
@define('BUILDMAN_RSYNC_BIN', 'rsync');
@define('BUILDMAN_CP_BIN', 'cp');
@define('BUILDMAN_CAT_BIN', 'cat');
@define('BUILDMAN_ZIP_BIN', 'zip');
@define('BUILDMAN_GZIP_BIN', 'gzip');
@define('BUILDMAN_TAR_BIN', 'tar');
@define('BUILDMAN_MAIL_ADDR', 'buildman@localhost');
@define('BUILDMAN_PROJECTS_SETTINGS_DIR', dirname(__FILE__) . '/projects/');
@define('BUILDMAN_PROJECTS_SANDBOX_DIR', LIMB_VAR_DIR . '/projects/');
@define('BUILDMAN_PROJECTS_WC_DIR', LIMB_VAR_DIR . '/wc/');
@define('BUILDMAN_PROJECTS_BUILDS_DIR', dirname(__FILE__) . '/www/builds/');

require_once(dirname(__FILE__) . '/common.inc.php');
?>
