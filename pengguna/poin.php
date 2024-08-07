<?php
function getUserInfo($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT username, email, points FROM user WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>