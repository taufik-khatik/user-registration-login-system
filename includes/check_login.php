<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $response = array('logged_in' => true);
    echo json_encode($response);
    exit();
} else {
    $response = array('logged_in' => false);
    echo json_encode($response);
    exit();
}
?>
