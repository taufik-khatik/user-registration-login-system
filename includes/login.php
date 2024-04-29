<?php
session_start();

require_once 'config.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  header('Location: dashboard.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate email and password
    if (empty($email) || empty($password)) {
        $response = array('success' => false, 'message' => 'Please enter email and password.');
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id']; // Set the user_id session variable
                $response = array('success' => true, 'message' => 'Login successful.');
                echo json_encode($response);
                exit();
            } else {
                $response = array('success' => false, 'message' => 'Invalid password.');
                echo json_encode($response);
                exit();
            }
        } else {
            // User not found
            $response = array('success' => false, 'message' => 'Invalid email or password.');
            echo json_encode($response);
            exit();
        }
    }    
} else {
    $response = array('success' => false, 'message' => 'Invalid request method.');
    echo json_encode($response);
    exit();
}

// Close database connection
$conn->close();
?>
