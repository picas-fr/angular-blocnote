<?php
// errors
@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// set a default timezone to avoid PHP5 warnings
$dtmz = @date_default_timezone_get();
date_default_timezone_set($dtmz?:'Europe/Paris');

// env
define('SRC', __DIR__);
define('REST_DEBUG', true);
define('NOTES_DB_FILE', 'db.json');

// classes
require_once SRC.'/RESTapi.php';
require_once SRC.'/JsonFileModelAbstract.php';
require_once SRC.'/BlocNote.php';

