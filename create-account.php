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
    $debug_info .= "POST data: " . print_r($_POST, true) . "\n";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $cpassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $tel = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $fname = isset($_POST['fname']) ? trim($_POST['fname']) : '';
    $lname = isset($_POST['lname']) ? trim($_POST['lname']) : '';
    $address = isset($_POST['address']) ? trim($_POST['address']) : '';
    $dob = isset($_POST['dob']) ? $_POST['dob'] : '';

    $debug_info .= "Form submitted with email: $email\n";
    $debug_info .= "Password length: " . strlen($password) . "\n";
    $debug_info .= "Phone: $tel\n";

    // Enhanced validation
    if (empty($email) || empty($password) || empty($cpassword) || empty($tel) || 
        empty($fname) || empty($lname) || empty($address) || empty($dob)) {
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
            
            // Check if webuser table exists
            $stmt = $pdo->prepare("SELECT EXISTS (
                SELECT FROM information_schema.tables 
                WHERE table_schema = 'public' 
                AND table_name = 'webuser'
            )");
            $stmt->execute();
            $table_exists = $stmt->fetchColumn();
            
            if (!$table_exists) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database tables not created. Please contact administrator.</label>';
                $debug_info .= "ERROR: webuser table does not exist\n";
            } else {
                // Check if email already exists in webuser table
                $stmt = $pdo->prepare("SELECT email FROM webuser WHERE email = ?");
                $stmt->execute([$email]);
                $result = $stmt->fetch();
                
                if ($result) {
                    $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is already registered.</label>';
                    $debug_info .= "Email already exists in database\n";
                } else {
                    $debug_info .= "Email not found in database, proceeding with registration...\n";
                    
                    // Combine first and last name
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
                        
                        $success = '<label class="form-label" style="color:rgb(0, 255, 0);text-align:center;">Account created successfully! Redirecting to login...</label>';
                        
                        // Redirect to login page after a delay
                        echo '<script>setTimeout(function(){ window.location.href = "login.php"; }, 3000);</script>';
                        
                    } catch (Exception $e) {
                        // Rollback transaction on error
                        $pdo->rollback();
                        $debug_info .= "Transaction rolled back due to error: " . $e->getMessage() . "\n";
                        error_log("Transaction error: " . $e->getMessage());
                        throw $e;
                    }
                }
            }
        } catch (PDOException $e) {
            $error_details = $e->getMessage();
            $debug_info .= "PDO Error: " . $error_details . "\n";
            error_log("Account creation error: " . $error_details);
            
            // Check for specific database errors
            if (strpos($error_details, 'duplicate key') !== false) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">This email is already registered.</label>';
            } elseif (strpos($error_details, 'relation') !== false && strpos($error_details, 'does not exist') !== false) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database tables not found. Please run create_tables.php first.</label>';
            } elseif (strpos($error_details, 'connection') !== false) {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database connection error. Please try again later.</label>';
            } else {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database error. Please contact support.</label>';
                if (isset($_GET['debug'])) {
                    $error .= '<br><small>' . htmlspecialchars($error_details) . '</small>';
                }
            }
        } catch (Exception $e) {
            $debug_info .= "General Error: " . $e->getMessage() . "\n";
            error_log("General account creation error: " . $e->getMessage());
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Account creation failed. Please try again.</label>';
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
                            <p class="sub-text">Add Your Details to Continue</p>
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
                            <td class="label-td">
                                <label for="fname" class="form-label">First Name: </label>
                            </td>
                            <td class="label-td">
                                <label for="lname" class="form-label">Last Name: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td">
                                <input type="text" name="fname" class="input-text" placeholder="First Name" required value="<?php echo isset($_POST['fname']) ? htmlspecialchars($_POST['fname']) : ''; ?>">
                            </td>
                            <td class="label-td">
                                <input type="text" name="lname" class="input-text" placeholder="Last Name" required value="<?php echo isset($_POST['lname']) ? htmlspecialchars($_POST['lname']) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="address" class="form-label">Address: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="text" name="address" class="input-text" placeholder="Address" required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="dob" class="form-label">Date of Birth: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="date" name="dob" class="input-text" required value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="email" class="form-label">Email: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="email" name="email" class="input-text" placeholder="Email Address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="phone" class="form-label">Phone Number: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="tel" name="phone" class="input-text" placeholder="Phone Number (10-11 digits)" required pattern="[0-9]{10,11}" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="password" class="form-label">Password: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="password" name="password" class="input-text" placeholder="Password (minimum 8 characters)" required minlength="8">
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="confirm_password" class="form-label">Confirm Password: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <input type="password" name="confirm_password" class="input-text" placeholder="Confirm Password" required minlength="8">
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
                            </td>
                        </tr>
                    </form>
                </table>
            </div>
        </div>
    </center>
</body>
</html>
