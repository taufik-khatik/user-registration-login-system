<?php
session_start();

require_once 'config.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  header('Location: dashboard.php');
  exit();
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate mobile number
function validateMobile($mobile) {
    return preg_match('/^[0-9]{10}$/', $mobile);
}

// Function to validate password
function validatePassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
} 

// Function to check if password and confirm password match
function checkPasswordMatch($password, $confirm_password) {
    return $password === $confirm_password;
}

// Function to check if email already exists
function checkEmailExists($conn, $email) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0;
}

// Function to upload profile photo
function uploadProfilePhoto($profile_photo) {
  if ($profile_photo['error'] === UPLOAD_ERR_OK) {
      $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
      $max_size = 2 * 1024 * 1024; // 2MB

      // Check file type and size
      if (in_array($profile_photo['type'], $allowed_types) && $profile_photo['size'] <= $max_size) {
          $temp_name = $profile_photo['tmp_name'];
          $ext = pathinfo($profile_photo['name'], PATHINFO_EXTENSION);
          $new_name = uniqid() . '.' . $ext;
          $destination = '../assets/uploads/' . $new_name;

          if (move_uploaded_file($temp_name, $destination)) {
              return $new_name; // File uploaded successfully
          } else {
              return 'File move failed';
          }
      } else {
          return 'Invalid file type or size';
      }
  } elseif (isset($profile_photo['error']) && $profile_photo['error'] === UPLOAD_ERR_NO_FILE) {
      return null; // No file uploaded
  } else {
      return 'File upload error';
  }
}

// Function to register a new user with parameterized query
function registerUser($conn, $first_name, $last_name, $email, $mobile, $hashed_password, $profile_photo) {
    $sql = "INSERT INTO users (first_name, last_name, email, mobile, password, profile_photo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $mobile, $hashed_password, $profile_photo);
        if ($stmt->execute()) {
            $stmt->close();
            return true; // Registration successful
        } else {
            $stmt->close();
            return false; // Registration failed
        }
    } else {
        error_log("Error: " . $conn->error);
        return false; // Prepare statement failed
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['mobile']) && !empty($_POST['password'])) {

        // Get form data
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $password = $_POST['password'];
        $profile_photo = $_FILES['profile_photo'];

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Validate email
        if (!validateEmail($email)) {
            $response = array('success' => false, 'message' => 'Invalid email address');
            echo json_encode($response);
            exit();
        }

        // Check if email already exists
        if (checkEmailExists($conn, $email)) {
            $response = array('success' => false, 'message' => 'Email already exists');
            echo json_encode($response);
            exit();
        }

        // Validate mobile number
        if (!validateMobile($mobile)) {
            $response = array('success' => false, 'message' => 'Invalid mobile number');
            echo json_encode($response);
            exit();
        }

        // Validate password
        if (!validatePassword($password)) {
            $response = array('success' => false, 'message' => 'Password must 8 characters long, contain at least one uppercase letter, one lowercase letter, one number and one special character');
            echo json_encode($response);
            exit();
        }

        // Check if password and confirm password match
        if (!checkPasswordMatch($password, $_POST['confirm_password'])) {
            $response = array('success' => false, 'message' => 'Password and confirm password do not match');
            echo json_encode($response);
            exit();
        }

        // Check if profile photo upload failed
        $profile_photo = uploadProfilePhoto($profile_photo);
        if (!$profile_photo && $profile_photo !== null) {
            $response = array('success' => false, 'message' => $profile_photo);
            echo json_encode($response);
            exit();
        }

        // Call the registerUser function to insert user data
        if (registerUser($conn, $first_name, $last_name, $email, $mobile, $hashed_password, $profile_photo)) {
            $response = array('success' => true, 'message' => 'Registration successful.');
            echo json_encode($response);
            exit();
        } else {
            $response = array('success' => false, 'message' => 'Error registering user. Please try again.');
            echo json_encode($response);
            exit();
        }
    } else {
        $response = array('success' => false, 'message' => 'Validation errors occurred.');
        echo json_encode($response);
        exit();
    }
}

// Close database connection
$conn->close();
?>
