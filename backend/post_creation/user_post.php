<?php


echo "<pre>";
print_r($_POST);
echo "</pre>";





require '../../backend/database.php'; // Ensure correct path
session_start(); // Start session to access session variables

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Ensure user is authenticated
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "Unauthorized: User not logged in."]);
        exit;
    }

    $user_id = $_SESSION['user_id']; // Get user ID from session

    // Check if database connection is working
    if (!$mysqli) {
        echo json_encode(["error" => "Database connection failed."]);
        exit;
    }

    // Sanitize inputs to prevent XSS
    $postHeading = htmlspecialchars(trim($_POST['postHeading'] ?? ''));
    $postDescription = htmlspecialchars(trim($_POST['postDescription'] ?? ''));
    $postLocation = htmlspecialchars(trim($_POST['postLocation'] ?? ''));
    $postLabel = htmlspecialchars(trim($_POST['postLabel'] ?? ''));
    $postUrgency = htmlspecialchars(trim($_POST['postUrgency'] ?? ''));
    $additionalContext = htmlspecialchars(trim($_POST['additionalContext'] ?? ''));

    // Validate required fields
    if (empty($postHeading) || empty($postDescription) || empty($postLocation) || empty($postLabel) || empty($postUrgency)) {
        echo json_encode(["error" => "All required fields must be filled."]);
        exit;
    }

    // Validate ENUM values for labels and urgency
    $validLabels = ['financial', 'medical', 'housing_needed', 'legal_help', 'education', 'volunteer_request', 'food'];
    $validUrgency = ['critical', 'urgent', 'moderate', 'low_priority'];

    if (!in_array($postLabel, $validLabels) || !in_array($postUrgency, $validUrgency)) {
        echo json_encode(["error" => "Invalid label or urgency value."]);
        exit;
    }
    
    
    
    
    // Prepare SQL statement
    $sql = "INSERT INTO User_Posts (user_id, heading, description, location, labels, urgency, additional_context, created_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

    // Debug Step: Check SQL before preparing
    echo "DEBUG: Preparing SQL Statement...<br>";

    if ($stmt = $mysqli->prepare($sql)) {
    echo "DEBUG: SQL Statement Prepared Successfully.<br>";

    // Debug Step: Check data before inserting
    echo "DEBUG: Data to be inserted - User ID: $user_id, Heading: $postHeading, Description: $postDescription, Location: $postLocation, Label: $postLabel, Urgency: $postUrgency, Additional Context: $additionalContext <br>";

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


