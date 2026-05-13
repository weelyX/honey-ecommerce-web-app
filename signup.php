<?php

session_start();
include "database.php";

function starts_good($value) {
    return preg_match("/^[\p{Arabic}a-zA-Z0-9]/u", trim($value));
}

$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$phone = trim($_POST["phone"] ?? "");
$address = trim($_POST["address"] ?? "");

if ($full_name == "" || $email == "" || $password == "" || $phone == "" || $address == "") {
    header("Location: Account.php?error=signup_empty#signup");
    exit;
}

if (!starts_good($full_name) || !starts_good($email) || !starts_good($phone) || !starts_good($address)) {
    header("Location: Account.php?error=invalid_start#signup");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: Account.php?error=email_invalid#signup");
    exit;
}

if (strlen($password) < 6) {
    header("Location: Account.php?error=password_short#signup");
    exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    header("Location: Account.php?error=email_exists#signup");
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $full_name, $email, $hashed_password, $phone, $address);
$stmt->execute();

$_SESSION["user_id"] = $conn->insert_id;
$_SESSION["user_name"] = $full_name;
$_SESSION["user_email"] = $email;

header("Location: Account.php?success=signup_success");
exit;

?>
