<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "lab-project";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات");
}

$conn->set_charset("utf8mb4");

?>
