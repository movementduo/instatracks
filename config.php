<?php

# config values

require_once('config-secret.php');

define("TEMPLATES",APP_ROOT.'templates/');
define("COMPONENTS",APP_ROOT.'components/');
define("FFMPEG_ASSETS",APP_ROOT.'app/assets/');
define('TMP_DIR',APP_ROOT.'build/');

date_default_timezone_set('Europe/London');