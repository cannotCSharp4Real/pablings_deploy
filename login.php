<?php
session_start();

// Import database connection
require_once("connection.php");

// Check if connection was successful
if (!isset($pdo)) {
    die("Database connection failed. Please check your database configuration.");
}

// Set the timezone
date_default_timezone_set('Asia/Manila'); // Changed to Philippines timezone
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Initialize the error variable
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['useremail']) ? trim($_POST['useremail']) : '';
    $password = isset($_POST['userpassword']) ? $_POST['userpassword'] : '';

    // Validate input
    if (empty($email) || empty($password)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please fill in all fields.</label>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Please enter a valid email address.</label>';
    } else {
        try {
            // Use prepared statements to prevent SQL injection
            $stmt = $pdo->prepare("SELECT * FROM webuser WHERE email = ?");
            $stmt->execute([$email]);
            $result = $stmt->fetch();
            
            if ($result) {
                $utype = $result['usertype'];

                // Check user type and credentials
                if ($utype == 'p') {
                    $stmt = $pdo->prepare("SELECT * FROM customer WHERE pemail = ?");
                    $stmt->execute([$email]);
                    $customer = $stmt->fetch();
                    
                    if ($customer && password_verify($password, $customer['ppassword'])) {
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = 'p';
                        $_SESSION['username'] = $customer['pname'];
                        header('Location: customer/index.php');
                        exit;
                    } else {
                        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                    }
                } elseif ($utype == 'a') {
                    $stmt = $pdo->prepare("SELECT * FROM admin WHERE aemail = ?");
                    $stmt->execute([$email]);
                    $admin = $stmt->fetch();
                    
                    if ($admin && password_verify($password, $admin['apassword'])) {
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = 'a';
                        $_SESSION['username'] = $admin['aname'];
                        header('Location: admin/index.php');
                        exit;
                    } else {
                        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                    }
                } elseif ($utype == 'd') {
                    $stmt = $pdo->prepare("SELECT * FROM barber WHERE docemail = ?");
                    $stmt->execute([$email]);
                    $barber = $stmt->fetch();
                    
                    if ($barber && password_verify($password, $barber['docpassword'])) {
                        $_SESSION['user'] = $email;
                        $_SESSION['usertype'] = 'd';
                        $_SESSION['username'] = $barber['docname'];
                        header('Location: barber/index.php');
                        exit;
                    } else {
                        $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Wrong credentials: Invalid email or password</label>';
                    }
                }
            } else {
                $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">We can\'t find any account for this email.</label>';
            }
        } catch (PDOException $e) {
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Database error. Please try again later.</label>';
            error_log("Login error: " . $e->getMessage());
        } catch (Exception $e) {
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">System error. Please try again later.</label>';
            error_log("Login exception: " . $e->getMessage());
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
    <link rel="stylesheet" href="css/login.css">
    <title>Login - Pabling's Barbershop</title>
</head>
<body>
    <div class="login-bg">
        <div class="login-center">
            <div class="login-card">
                <h2 class="login-header">Welcome Back!</h2>
                <p class="login-subtext">Login with your details to continue</p>
                <form action="" method="POST">
                    <div class="login-field">
                        <label for="useremail" class="form-label">Email:</label>
                        <input type="email" name="useremail" class="input-text" placeholder="Email Address" required value="<?php echo isset($_POST['useremail']) ? htmlspecialchars($_POST['useremail']) : ''; ?>">
                    </div>
                    <div class="login-field">
                        <label for="userpassword" class="form-label">Password:</label>
                        <input type="password" name="userpassword" class="input-text" placeholder="Password" required>
                    </div>
                    <div class="login-error"><?php echo $error; ?></div>
                    <button type="submit" class="login-btn btn-primary btn">Login</button>
                </form>
            </div>
            <div class="login-signup">
                <span>Don't have an account? </span><a href="signup.php" class="signup-link">Sign Up</a>
            </div>
        </div>
    </div>
</body>
</html>
