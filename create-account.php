<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Import database connection
require_once("connection.php");

// Set the new timezone
date_default_timezone_set('Asia/Manila');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Initialize variables
$error = '';
$success = '';
$debug_info = '';

// DEBUG: Check session data
if (isset($_GET['debug'])) {
    $debug_info .= "DEBUG INFO:\n";
    $debug_info .= "Session ID: " . session_id() . "\n";
    $debug_info .= "Session personal data: " . (isset($_SESSION["personal"]) ? "EXISTS" : "MISSING") . "\n";
    if (isset($_SESSION["personal"])) {
        $debug_info .= "Personal data: " . print_r($_SESSION["personal"], true) . "\n";
    }
    $debug_info .= "POST data: " . print_r($_POST, true) . "\n";
}

// Create dummy session data for testing (REMOVE IN PRODUCTION)
if (!isset($_SESSION["personal"])) {
    $_SESSION["personal"] = [
        'fname' => 'Test',
        'lname' => 'User',
        'address' => '123 Test Street',
        'dob' => '1990-01-01'
    ];
    $debug_info .= "Created dummy session data for testing\n";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['newemail']) ? trim($_POST['newemail']) : '';
    $password = isset($_POST['newpassword']) ? $_POST['newpassword'] : '';
    $cpassword = isset($_POST['cpassword']) ? $_POST['cpassword'] : '';
    $tel = isset($_POST['newtel']) ? trim($_POST['newtel']) : '';

    $debug_info .= "Form submitted with email: $email\n";
    $debug_info .= "Password length: " . strlen($password) . "\n";
    $debug_info .= "Phone: $tel\n";

    // Enhanced validation
    if (empty($email) || empty($password) || empty($cpassword) || empty($tel)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please fill in all fields.</label>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please enter a valid email address.</label>';
    } elseif ($password !== $cpassword) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Passwords do not match.</label>';
    } elseif (strlen($password) < 8) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password must be at least 8 characters long.</label>';
    } elseif (!preg_match('/^[0-9]{10,11}$/', $tel)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please enter a valid phone number (10-11 digits only).</label>';
    } else {
        try {
            $debug_info .= "Starting database operations...\n";
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT email FROM webuser WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            
            if ($result) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is already registered.</label>';
                $debug_info .= "Email already exists in database\n";
            } else {
                $debug_info .= "Email not found in database, proceeding with registration...\n";
                
                // Get personal details from session
                $personal = $_SESSION["personal"];
                $fname = $personal['fname'];
                $lname = $personal['lname'];
                $address = $personal['address'];
                $dob = $personal['dob'];
                $name = $fname . ' ' . $lname;
                
                $debug_info .= "Personal details: Name=$name, Address=$address, DOB=$dob\n";
                
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $debug_info .= "Password hashed successfully\n";
                
                // Begin transaction
                $pdo->beginTransaction();
                $debug_info .= "Transaction started\n";
                
                try {
                    // Insert into customer table first
                    $stmt = $pdo->prepare("INSERT INTO customer (pemail, pname, ppassword, paddress, pdob, ptel) VALUES (?, ?, ?, ?, ?, ?)");
                    $success_customer = $stmt->execute([$email, $name, $hashed_password, $address, $dob, $tel]);
                    
                    $debug_info .= "Customer insert result: " . ($success_customer ? "SUCCESS" : "FAILED") . "\n";
                    
                    if (!$success_customer) {
                        throw new Exception("Failed to create customer record");
                    }
                    
                    // Insert into webuser table
                    $stmt = $pdo->prepare("INSERT INTO webuser (email, usertype) VALUES (?, 'p')");
                    $success_webuser = $stmt->execute([$email]);
                    
                    $debug_info .= "Webuser insert result: " . ($success_webuser ? "SUCCESS" : "FAILED") . "\n";
                    
                    if (!$success_webuser) {
                        throw new Exception("Failed to create webuser record");
                    }
                    
                    // Commit transaction
                    $pdo->commit();
                    $debug_info .= "Transaction committed successfully\n";
                    
                    // Clear session data
                    unset($_SESSION["personal"]);
                    
                    $success = '<label class="form-label" style="color:rgb(0, 255, 0);text-align:center;">Account created successfully! You can now login.</label>';
                    
                    // Optional: Redirect to login page after a delay
                    echo '<script>setTimeout(function(){ window.location.href = "login.php"; }, 3000);</script>';
                    
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $pdo->rollback();
                    $debug_info .= "Transaction rolled back due to error: " . $e->getMessage() . "\n";
                    error_log("Transaction error: " . $e->getMessage());
                    throw $e;
                }
            }
        } catch (PDOException $e) {
            $error_details = $e->getMessage();
            $debug_info .= "PDO Error: " . $error_details . "\n";
            error_log("Account creation error: " . $error_details);
            
            // Check for specific database errors
            if (strpos($error_details, 'duplicate key') !== false) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is already registered.</label>';
            } elseif (strpos($error_details, 'connection') !== false) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database connection error. Please try again later.</label>';
            } else {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database error: ' . htmlspecialchars($error_details) . '</label>';
            }
        } catch (Exception $e) {
            $debug_info .= "General Error: " . $e->getMessage() . "\n";
            error_log("General account creation error: " . $e->getMessage());
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Account creation failed: ' . htmlspecialchars($e->getMessage()) . '</label>';
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
    <title>Create Account - Pabling's Barbershop</title>
    <style>
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
        }
        .error-message {
            color: rgb(255, 62, 62);
            text-align: center;
            display: block;
            margin: 10px 0;
        }
        .success-message {
            color: rgb(0, 255, 0);
            text-align: center;
            display: block;
            margin: 10px 0;
        }
        .debug-info {
            background: #f0f0f0;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 12px;
        }
        .input-text {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-primary {
            background-color: #1b62b3;
            color: white;
        }
        .btn-primary-soft {
            background-color: #f0f0f0;
            color: #333;
        }
    </style>
</head>
<body>
    <center>
        <div class="container">
            <div class="form-container">
                <table border="0" style="width: 100%;">
                    <tr>
                        <td colspan="2">
                            <p class="header-text">Create Account</p>
                            <p class="sub-text">Add Your Login Details to Continue</p>
                        </td>
                    </tr>
                    
                    <?php if (isset($_GET['debug']) && $debug_info): ?>
                    <tr>
                        <td colspan="2">
                            <div class="debug-info"><?php echo htmlspecialchars($debug_info); ?></div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
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
                                <input type="tel" name="newtel" class="input-text" placeholder="Phone Number (10-11 digits)" required pattern="[0-9]{10,11}" value="<?php echo isset($_POST['newtel']) ? htmlspecialchars($_POST['newtel']) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="newpassword" class="form-label">Password: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="password" name="newpassword" class="input-text" placeholder="Password (minimum 8 characters)" required minlength="8">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="cpassword" class="form-label">Confirm Password: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="password" name="cpassword" class="input-text" placeholder="Confirm Password" required minlength="8">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <br>
                                <?php if ($error): ?>
                                    <div class="error-message"><?php echo $error; ?></div>
                                <?php endif; ?>
                                <?php if ($success): ?>
                                    <div class="success-message"><?php echo $success; ?></div>
                                <?php endif; ?>
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
                                <br><br>
                                <p><small>For debugging, add ?debug=1 to the URL</small></p>
                            </td>
                        </tr>
                    </form>
                </table>
            </div>
        </div>
    </center>
</body>
</html>
