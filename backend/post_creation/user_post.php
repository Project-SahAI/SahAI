<?php

require '../../backend/database.php'; // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if database connection is working
    if (!$mysqli) {
        die(json_encode(["error" => "Database connection failed."]));
    }

    // Sanitize inputs to prevent XSS
    $postHeading = htmlspecialchars(trim($_POST['postHeading'] ?? ''));
    $postDescription = htmlspecialchars(trim($_POST['postDescription'] ?? ''));
    $postLocation = htmlspecialchars(trim($_POST['postLocation'] ?? ''));
    $postLabel = htmlspecialchars(trim($_POST['postLabel'] ?? ''));
    $postUrgency = htmlspecialchars(trim($_POST['postUrgency'] ?? ''));
    $additionalContext = htmlspecialchars(trim($_POST['additionalContext'] ?? ''));
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 1; // Default to 1 if not provided

    // Validate required fields
    if (empty($postHeading) || empty($postDescription) || empty($postLocation) || empty($postLabel) || empty($postUrgency)) {
        die(json_encode(["error" => "All required fields must be filled."]));
    }

    // Validate ENUM values for labels and urgency
    $validLabels = ['financial', 'medical', 'housing_needed', 'legal_help', 'education', 'volunteer_request', 'food'];
    $validUrgency = ['critical', 'urgent', 'moderate', 'low_priority'];

    if (!in_array($postLabel, $validLabels) || !in_array($postUrgency, $validUrgency)) {
        die(json_encode(["error" => "Invalid label or urgency value."]));
    }

    // Check if user_id exists in individual_users table
    $userCheckSql = "SELECT user_id FROM individual_users WHERE user_id = ?";
    if ($userCheckStmt = $mysqli->prepare($userCheckSql)) {
        $userCheckStmt->bind_param("i", $user_id);
        $userCheckStmt->execute();
        $userCheckStmt->store_result();

        if ($userCheckStmt->num_rows === 0) {
            die(json_encode(["error" => "Invalid user_id: $user_id does not exist."]));
        }
        $userCheckStmt->close();
    }

    // Prepare SQL statement (Include user_id)
    $sql = "INSERT INTO User_Posts (user_id, heading, description, location, labels, urgency, additional_context, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    if ($stmt = $mysqli->prepare($sql)) {
        // Bind parameters, including user_id (integer)
        $stmt->bind_param("issssss", $user_id, $postHeading, $postDescription, $postLocation, $postLabel, $postUrgency, $additionalContext);

        if ($stmt->execute()) {
            echo json_encode(["success" => "Need added successfully!"]);
        } else {
            echo json_encode(["error" => "Database error: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Failed to prepare SQL statement. MySQL Error: " . $mysqli->error]);
    }

    $mysqli->close();
} else {
    echo json_encode(["error" => "Invalid request method."]);
}

