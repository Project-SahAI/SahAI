<?php

if (empty($_POST["individualSignupName"])) {
    die("Name is required");
}

if ( ! filter_var($_POST["individualSignupEmail"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["individualSignupPhone"]) != 10) {
    die("Phone number should be 10 digits only");
}

if (strlen($_POST["individualSignupPassword"]) < 8) {
    die("Password must be at least 8 characters");
}

if ( ! preg_match("/[a-z]/i", $_POST["individualSignupPassword"])) {
    die("Password must contain at least one letter");
}

if ( ! preg_match("/[0-9]/", $_POST["individualSignupPassword"])) {
    die("Password must contain at least one number");
}

if ($_POST["individualSignupPassword"] !== $_POST["individualSignupConfirmPassword"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["individualSignupPassword"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/../database.php";

$sql = "INSERT INTO individual_users ( user_type, name, age, phone, address, email, password_hash)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        
$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$user_type = "user";

$stmt->bind_param("sssssss",
                    $user_type,
                    $_POST["individualSignupName"],
                    $_POST["individualSignupAge"],
                    $_POST["individualSignupPhone"],
                    $_POST["individualSignupAddress"],
                    $_POST["individualSignupEmail"],
                    $password_hash);
                  
if ($stmt->execute()) {

    //print_r("Awesome... the sql went through");
    header("Location: ../../pages/auth/auth-portal.html");
    exit;
    
} else {
    
    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}







