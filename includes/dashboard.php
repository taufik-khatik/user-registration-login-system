<?php
session_start();

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    // User not found, redirect to login page
    header('Location: ../login.html');
    exit();
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Your custom styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="banner">
        <div class="row justify-content-center container">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title text-center">Dashboard</h2>
                    </div>
                    <div class="card-body">
                     
                      <div class="row mb-4">
                          <?php if (!empty($user['profile_photo'])) { ?>
                              <div class="col-md-4">
                                  <img src="../assets/uploads/<?php echo $user['profile_photo']; ?>" alt="Profile Photo" class="img-fluid profile-photo">
                              </div>
                          <?php } ?>
                          <div class="col-md-8 align-self-center mt-3">
                              <p><strong>Name:</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                              <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                              <p><strong>Mobile:</strong> <?php echo htmlspecialchars($user['mobile']); ?></p>
                          </div>
                          <div class="col-md-12 text-center">
                            <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
                            <a href="../index.html" class="btn btn-secondary mt-3">Back to Home</a>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Your custom script -->
    <script src="../assets/js/script.js"></script>

</body>

</html>
