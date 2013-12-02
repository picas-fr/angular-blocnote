<?php

// config & init
require_once 'src/bootstrap.php';


if (!file_exists(NOTES_DB_FILE)) {
    @touch(NOTES_DB_FILE);
}

if (!file_exists($a = NOTES_DB_FILE) || !is_writable($a)) :
    die("<p style=\"color:red;font-weight:bold\">Can not create or write in DB file '$a'! Please set correct rights on it for your srever user and reload the page.</p>");
else:
    die("<p style=\"color:green;font-weight:bold\">OK - DB file '$a' seems to be writable by server user.</p>");
endif;

?>
