<?php

session_start();
header("Content-Type: application/json; charset=UTF-8");

echo json_encode([
    "logged_in" => isset($_SESSION["user_id"]),
    "name" => $_SESSION["user_name"] ?? "",
    "email" => $_SESSION["user_email"] ?? ""
], JSON_UNESCAPED_UNICODE);

?>
