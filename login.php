<?php
session_start();

// Import database connection
require_once("connection.php");

// Unset all the server-side variables
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Set the timezone
date_default_timezone_set('Singapore');
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
            // Check if database connection exists
            if (!isset($pdo)) {
                throw new Exception("Database connection error. Please try again later.");
            }

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
            $error = '<label class="form-label" style="color:rgb(255, 62, 62);text-align:center;">' . $e->getMessage() . '</label>';
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
    <title>Login</title>
</head>
<body>
    <center>
        <div class="container">
            <table border="0" style="margin: 0;padding: 0;width: 60%;">
                <tr>
                    <td><p class="header-text">Welcome Back!</p></td>
                </tr>
                <div class="form-body">
                    <tr>
                        <td><p class="sub-text">Login with your details to continue</p></td>
                    </tr>
                    <tr>
                        <form action="" method="POST">
                            <td class="label-td">
                                <label for="useremail" class="form-label">Email: </label>
                            </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <input type="email" name="useremail" class="input-text" placeholder="Email Address" required value="<?php echo isset($_POST['useremail']) ? htmlspecialchars($_POST['useremail']) : ''; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <label for="userpassword" class="form-label">Password: </label>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-td">
                            <input type="password" name="userpassword" class="input-text" placeholder="Password" required>
                        </td>
                    </tr>
                    <tr>
                        <td><br><?php echo $error; ?></td>
                    </tr>
                    <tr>
                        <td>
                            <input type="submit" value="Login" class="login-btn btn-primary btn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <br>
                            <label for="" class="sub-text" style="font-weight: 280;">Don't have an account&#63; </label>
                            <a href="signup.php" class="hover-link1 non-style-link">Sign Up</a>
                            <br><br><br>
                        </td>
                    </tr>
                        </form>
                </div>
            </table>
        </div>
    </center>
</body>
</html>
