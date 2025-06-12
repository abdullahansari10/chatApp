<?php
session_start();
include('./required/config.php');

$username = $_SESSION['username'] ?? '';
$input_data = trim($_POST['input_data'] ?? '');
$receiver = trim($_POST['receiver'] ?? '');

if (!empty($username) && !empty($input_data) && !empty($receiver)) {
    $stmt = $conn->prepare("INSERT INTO users_data (message, sender_username, receiver_username) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $input_data, $username, $receiver);

    if ($stmt->execute()) {
        echo "Message inserted successfully.";
    } else {
        echo "Error inserting message: " . $stmt->error;
    }
} else {
    echo "Required fields missing.";
}
?>

