<?php
define("DB_HOST", '127.0.0.1');
define("DB_USER", 'addis');
define("DB_PASS", 'addis');
define("DB_NAME", 'addis_complaint');


// create connection
$connection = new mysqli("localhost", "addis", "addis", "addis_complaint");

if ($connection->connect_error) {
    die('Connection to DB Failed' . $connection->connect_error);
}