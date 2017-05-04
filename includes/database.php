<?php

$dbHost = 'localhost';
$dbPort = 3306;
$dbName = 'keir05';
$dbUsername = 'student';
$dbPassword = '';

$mysqli = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName, $dbPort);

if ($mysqli->connect_error) {
    die("Error connecting to database: $mysqli->connect_error.");
}