<?php
session_start();
include "../database.php"; // Ensure this file initializes $mysqli

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginemail = trim($_POST["individualLoginEmail"]);
    $loginpassword = trim($_POST["individualLoginPassword"]); // Fix: Store plain password

    if (!empty($loginemail) && !empty($loginpassword)) {
        $stmt = $mysqli->prepare("SELECT user_id, password_hash FROM individual_users WHERE email = ?");
        $stmt->bind_param("s", $loginemail);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();

            // Fix: Verify password against stored hash
            if (password_verify($loginpassword, $hashed_password)) {
                $_SESSION["user_id"] = $id;
                $_SESSION["loginemail"] = $loginemail;
                echo json_encode(["success" => true]);
                exit;
            } else {
                echo json_encode(["success" => false, "error" => "Invalid credentials."]);
                exit;
            }
        } else {
            echo json_encode(["success" => false, "error" => "User not found."]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "error" => "All fields are required."]);
        exit;
    }
}
?>