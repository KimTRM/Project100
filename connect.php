<?php
static $dbHost = 'localhost';
static $dbUsername = 'root';
static $dbPassword = '';
static $dbName = 'project100';

$conn = mysqli_connect($dbHost, $dbUsername, $dbPassword, $dbName);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
