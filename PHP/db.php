<?php 
// Configure the postgres parameters here to your own settings when running locally
// Do not overwrite this when pushing to git
$dbconn=pg_connect(getenv("HEROKU_POSTGRESQL_ONYX_URL")); 
?>
