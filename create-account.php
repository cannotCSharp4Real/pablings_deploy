<?php
session_start();

// Import database connection
require_once("connection.php");

// Set the new timezone
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Initialize variables
$error = '';
$success = '';

// Check if personal details are in session
if (!isset($_SESSION["personal"])) {
    header("Location: signup.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['newemail']) ? trim($_POST['newemail']) : '';
    $password = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
    $cpassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';
    $tel = isset($_POST['newtel']) ? trim($_POST['newtel']) : '';

    // Validate input
    if (empty($email) || empty($password) || empty($cpassword) || empty($tel)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please fill in all fields.</label>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please enter a valid email address.</label>';
    } elseif ($password !== $cpassword) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Passwords do not match.</label>';
    } elseif (strlen($password) < 6) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password must be at least 6 characters long.</label>';
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            
            if ($result) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is already registered.</label>';
            } else {
                // Get personal details from session
                $personal = $_SESSION["personal"];
                $fname = $personal['fname'];
                $lname = $personal['lname'];
                $address = $personal['address'];
                $dob = $personal['dob'];
                $name = $fname . ' ' . $lname;
                
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Begin transaction
                $pdo->beginTransaction();
                
                try {
                    // Insert into customer table
                    $stmt = $pdo->prepare("INSERT INTO customer (pemail, pname, ppassword, paddress, pdob, ptel) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$email, $name, $hashed_password, $address, $dob, $tel]);
                    
                    // Insert into webuser table
                    $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'p')");
                    $stmt->execute([$email]);
                    
                    // Commit transaction
                    $pdo->commit();
                    
                    // Clear session data
                    unset($_SESSION["personal"]);
                    
                    $success = '<label class="form-label" style="color:rgb(0, 255, 0);text-align:center;">Account created successfully! You can now login.</label>';
                    
                } catch (PDOException $e) {
                    // Rollback transaction on error
                    $pdo->rollback();
                    throw $e;
                }
            }
        } catch (PDOException $e) {
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database error. Please try again later.</label>';
            error_log("Account creation error: " . $e->getMessage());
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
</head>
<body>
    <center>
        <div class="container">
            <table border="0">
                <tr>
                    <td colspan="2">
                        <p class="header-text">Create Account</p>
                        <p class="sub-text">Add Your Login Details to Continue</p>
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
                            <label for="newtel" class="form-label">Phone Number: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="tel" name="newtel" class="input-text" placeholder="Phone Number" required value="<?php echo isset($_POST['newtel']) ? htmlspecialchars($_POST['newtel']) : ''; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <label for="newpassword" class="form-label">Password: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <input type="password" name="newpassword" class="input-text" placeholder="Password" required>
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
                        <td class="label-td" colspan="2">
                            <br><?php echo $error; ?><?php echo $success; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                        </td>
                        <td>
                            <input type="submit" value="Create Account" class="login-btn btn-primary btn">
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
