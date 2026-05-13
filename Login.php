<?php

session_start();
include "database.php";

function starts_good($value) {
    return preg_match("/^[\p{Arabic}a-zA-Z0-9]/u", trim($value));
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email == "" || $password == "") {
    header("Location: Account.php?error=login_empty#login");
    exit;
}

if (!starts_good($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: Account.php?error=email_invalid#login");
    exit;
}

$stmt = $conn->prepare("SELECT id, full_name, email, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user["password"])) {
    header("Location: Account.php?error=login_wrong#login");
    exit;
}

$_SESSION["user_id"] = $user["id"];
$_SESSION["user_name"] = $user["full_name"];
$_SESSION["user_email"] = $user["email"];

header("Location: Account.php");
exit;

?>
