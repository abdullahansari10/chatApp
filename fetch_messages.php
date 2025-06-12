<?php 
session_start();
include('./required/config.php');

$username = $_SESSION['username'] ?? '';
$receiver = trim($_GET['receiver'] ?? '');

if (empty($receiver)) {
    echo "Receiver not specified.";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users_data WHERE 
    (sender_username = ? AND receiver_username = ?) 
    OR 
    (sender_username = ? AND receiver_username = ?) 
    ORDER BY createdOn ASC");

$stmt->bind_param('ssss', $username, $receiver, $receiver, $username);
$stmt->execute();
$result = $stmt->get_result();

$messages = '';
while ($row = $result->fetch_assoc()) {
    $time = date("h:i A", strtotime($row['createdOn']));
    if ($row['sender_username'] == $username) {
        $messages .= '<div class="userMsg">' . htmlspecialchars($row['message']) . '<span class="userTime">'. $time . '</span></div>';
    } else {
        $messages .= '<div class="botMsg">' . htmlspecialchars($row['message']) . '<span class="botTime">'. $time . '</span></div>';
    }
}

echo $messages;
?>