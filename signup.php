<?php
// Move all PHP code to the top before any HTML output
session_start();

// Unset all the server side variables
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Set the new timezone
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Handle form submission
if ($_POST) {
    $_SESSION["personal"] = array(
        'fname' => $_POST['fname'],
        'lname' => $_POST['lname'],
        'address' => $_POST['address'],
        'dob' => $_POST['dob']
    );
    
    // Redirect to next page
    header("Location: create-account.php");
    exit(); // Always exit after redirect
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
    <title>Sign Up</title>
</head>
<body>
    <div class="center-wrapper">
        <div class="signup-card">
            <form action="" method="POST">
                <h1 class="signup-header">Let's Get Started</h1>
                <p class="signup-subtext">Add Your Personal Details to Continue</p>
                <div class="signup-field-row">
                    <div class="signup-field">
                        <label for="fname" class="form-label">First Name:</label>
                        <input type="text" name="fname" class="input-text" placeholder="First Name" required>
                    </div>
                    <div class="signup-field">
                        <label for="lname" class="form-label">Last Name:</label>
                        <input type="text" name="lname" class="input-text" placeholder="Last Name" required>
                    </div>
                </div>
                <div class="signup-field">
                    <label for="address" class="form-label">Address:</label>
                    <input type="text" name="address" class="input-text" placeholder="Address" required>
                </div>
                <div class="signup-field">
                    <label for="dob" class="form-label">Date of Birth:</label>
                    <input type="date" name="dob" class="input-text" required>
                </div>
                <div class="signup-btn-row">
                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                    <input type="submit" value="Next" class="login-btn btn-primary btn">
                </div>
                <div class="signup-login">
                    <span>Already have an account? </span><a href="login.php" class="signup-link">Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
