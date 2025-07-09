<?php
session_start();

// Import database
require_once("connection.php");

// Unset all server-side variables
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Set the new timezone
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Initialize error variable
$error = '';

if ($_POST) {
    // Check if database connection exists
    if (!isset($pdo) || !$pdo) {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database connection error. Please try again later.</label>';
    } else {
        // Get form data and sanitize
        $fname = isset($_SESSION['personal']['fname']) ? trim($_SESSION['personal']['fname']) : '';
        $lname = isset($_SESSION['personal']['lname']) ? trim($_SESSION['personal']['lname']) : '';
        $name = $fname . " " . $lname;
        $address = isset($_SESSION['personal']['address']) ? trim($_SESSION['personal']['address']) : '';
        $dob = isset($_SESSION['personal']['dob']) ? $_SESSION['personal']['dob'] : '';
        $email = isset($_POST['newemail']) ? trim($_POST['newemail']) : '';
        $newpassword = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
        $cpassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';
        $tele = isset($_POST['tele']) ? trim($_POST['tele']) : '';
        
        // Validate required fields
        if (empty($email) || empty($newpassword) || empty($cpassword)) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please fill in all required fields.</label>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please enter a valid email address.</label>';
        } elseif (strlen($newpassword) < 6) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password must be at least 6 characters long.</label>';
        } elseif ($newpassword !== $cpassword) {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Confirmation Error! Reconfirm Password</label>';
        } else {
            try {
                // Check if email exists
                $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
                $stmt->execute([$email]);
                $result = $stmt->fetch();
                
                if ($result) {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>';
                } else {
                    // Hash the password before storing it
                    $hashedPassword = password_hash($newpassword, PASSWORD_DEFAULT);
                    
                    // Begin transaction
                    $pdo->beginTransaction();
                    
                    try {
                        // Insert new customer
                        $stmt1 = $pdo->prepare("INSERT INTO customer (pemail, pname, ppassword, paddress, pdob, ptel) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt1->execute([$email, $name, $hashedPassword, $address, $dob, $tele]);
                        
                        // Insert new webuser
                        $stmt2 = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'p')");
                        $stmt2->execute([$email]);
                        
                        // Commit transaction
                        $pdo->commit();
                        
                        // Set session variables
                        $_SESSION["user"] = $email;
                        $_SESSION["usertype"] = "p";
                        $_SESSION["username"] = $fname;
                        
                        // Redirect to customer dashboard
                        header('Location: customer/index.php');
                        exit();
                        
                    } catch (PDOException $e) {
                        // Rollback transaction on error
                        $pdo->rollback();
                        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Error creating account. Please try again.</label>';
                        error_log("Registration error: " . $e->getMessage());
                    }
                }
            } catch (PDOException $e) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database error. Please try again.</label>';
                error_log("Database error: " . $e->getMessage());
            }
        }
    }
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
    <title>Create Account</title>
    <style>
        .container {
            animation: transitionIn-X 0.5s;
        }
    </style>
</head>
<body>
    <center>
        <div class="container">
            <table border="0" style="width: 69%;">
                <tr>
                    <td colspan="2">
                        <p class="header-text">Let's Get Started</p>
                        <p class="sub-text">It's Okay, Now Create User Account.</p>
                    </td>
                </tr>
                <form action="" method="POST">
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="newemail" class="form-label">Email: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="email" name="newemail" class="input-text" placeholder="Email Address" required value="<?php echo isset($_POST['newemail']) ? htmlspecialchars($_POST['newemail']) : ''; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="tele" class="form-label">Mobile Number: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="tel" name="tele" class="input-text" placeholder="ex: 0712345678" pattern="[0]{1}[0-9]{9}" value="<?php echo isset($_POST['tele']) ? htmlspecialchars($_POST['tele']) : ''; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="newpassword" class="form-label">Create New Password: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="password" name="newpassword" class="input-text" placeholder="New Password" required minlength="6">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="cpassword" class="form-label">Confirm Password: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password" required minlength="6">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php echo $error; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                        </td>
                        <td>
                            <input type="submit" value="Sign Up" class="login-btn btn-primary btn">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <br>
                            <label for="" class="sub-text" style="font-weight: 280;">Already have an account&#63; </label>
                            <a href="login.php" class="hover-link1 non-style-link">Login</a>
                            <br><br><br>
                        </td>
                    </tr>
                </form>
            </table>
        </div>
    </center>
</body>
</html>
