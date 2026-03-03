<?php
require_once __DIR__ . '/../../config/config2.php';

$conn = new mysqli(
  DB_SERVER,
  DB_USERNAME,
  DB_PASSWORD,
  DB_NAME
);

if ($conn->connect_error) {
  die('DB connection failed');
}

mysqli_set_charset($conn, 'utf8');

