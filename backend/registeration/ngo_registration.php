<?php

if (empty($_POST["ngoSignupName"])) {
    die("Name is required");
}

if ( ! filter_var($_POST["ngoSignupEmail"], FILTER_VALIDATE_EMAIL)) {
    die("Valid email is required");
}

if (strlen($_POST["ngoSignupPhone"]) != 10) {
    die("Phone number should be 10 digits only");
}

if (strlen($_POST["ngoSignupPassword"]) < 8) {
    die("Password must be at least 8 characters");
}

if ( ! preg_match("/[a-z]/i", $_POST["ngoSignupPassword"])) {
    die("Password must contain at least one letter");
}

if ( ! preg_match("/[0-9]/", $_POST["ngoSignupPassword"])) {
    die("Password must contain at least one number");
}

if ($_POST["ngoSignupPassword"] !== $_POST["ngoSignupConfirmPassword"]) {
    die("Passwords must match");
}

$password_hash = password_hash($_POST["ngoSignupPassword"], PASSWORD_DEFAULT);

$mysqli = require __DIR__ . "/../database.php";

$sql = "INSERT INTO ngos (  name, email, ngo_type, phone, address, owner_password_hash)
        VALUES (?, ?, ?, ?, ?, ?)";
        
$stmt = $mysqli->stmt_init();

if ( ! $stmt->prepare($sql)) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("ssssss",
                    $_POST["ngoSignupName"],
                    $_POST["ngoSignupEmail"],
                    $_POST["ngoSignupType"],
                    $_POST["ngoSignupPhone"],
                    $_POST["ngoSignupAddress"],
                    $password_hash);
                  
if ($stmt->execute()) {

    //print_r("Awesome... the sql went through");
    header("Location: http://localhost:8080/SahAI/pages/auth/auth-portal.html");
    exit;
    
} else {
    
    if ($mysqli->errno === 1062) {
        die("email already taken");
    } else {
        die($mysqli->error . " " . $mysqli->errno);
    }
}