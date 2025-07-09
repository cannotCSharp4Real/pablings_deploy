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

// Import database
include("connection.php");

// Initialize error variable
$error = '<label for="promter" class="form-label"></label>';

if ($_POST) {
    // Check if database connection exists
    if (!isset($database) || !$database) {
        $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database connection error. Please try again later.</label>';
    } else {
        // Get form data
        $fname = $_SESSION['personal']['fname'];
        $lname = $_SESSION['personal']['lname'];
        $name = $fname . " " . $lname;
        $address = $_SESSION['personal']['address'];
        $dob = $_SESSION['personal']['dob'];
        $email = $_POST['newemail'];
        $newpassword = $_POST['newpassword'];
        $cpassword = $_POST['cpassword'];
        $tele = $_POST['tele'] ?? '';
        
        // Check if passwords match
        if ($newpassword == $cpassword) {
            // Use prepared statement to check if email exists
            $stmt = $database->prepare("SELECT * FROM webuser WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>';
            } else {
                // Insert new user with prepared statements
                $stmt1 = $database->prepare("INSERT INTO customer (pemail, pname, ppassword, paddress, pdob) VALUES (?, ?, ?, ?, ?)");
                $stmt1->bind_param("sssss", $email, $name, $newpassword, $address, $dob);
                
                $stmt2 = $database->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'p')");
                $stmt2->bind_param("s", $email);
                
                if ($stmt1->execute() && $stmt2->execute()) {
                    // Set session variables
                    $_SESSION["user"] = $email;
                    $_SESSION["usertype"] = "p";
                    $_SESSION["username"] = $fname;
                    
                    // Redirect to customer dashboard
                    header('Location: customer/index.php');
                    exit();
                } else {
                    $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Error creating account. Please try again.</label>';
                }
            }
        } else {
            $error = '<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Confirmation Error! Reconfirm Password</label>';
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
        .container{
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
                            <input type="email" name="newemail" class="input-text" placeholder="Email Address" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="tele" class="form-label">Mobile Number: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="tel" name="tele" class="input-text" placeholder="ex: 0712345678" pattern="[0]{1}[0-9]{9}">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="newpassword" class="form-label">Create New Password: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="password" name="newpassword" class="input-text" placeholder="New Password" required>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="cpassword" class="form-label">Confirm Password: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password" required>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php echo $error ?>
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
