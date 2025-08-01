<?php
//learn from w3schools.com
//Unset all the server side variables

session_start();

$_SESSION["user"]="";
$_SESSION["usertype"]="";

// Set the new timezone
date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');

$_SESSION["date"]=$date;

if($_POST){
    $_SESSION["personal"]=array(
        'fname'=>$_POST['fname'],
        'lname'=>$_POST['lname'],
        'address'=>$_POST['address'],
        'dob'=>$_POST['dob']
    );

    // Remove debug output that causes headers already sent error
    // print_r($_SESSION["personal"]);
    header("location: create-account.php");
    exit();
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
        
    <title>Sign Up</title>
    
    
</head>
<body>

    <center>
    <div class="container">
        <table border="0" style="margin: 0;padding: 0;width: 60%;">
            <tr>
                <td><p class="header-text">Let's Get Started</p></td>
            </tr>
            <div class="form-body">
                <tr>
                    <td><p class="sub-text">Add Your Personal Details to Continue</p></td>
                </tr>
                <tr>
                    <form action="" method="POST" >
                    <td class="label-td">
                        <label for="name" class="form-label">Name: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="text" name="fname" class="input-text" placeholder="First Name" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="text" name="lname" class="input-text" placeholder="Last Name" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label for="address" class="form-label">Address: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="text" name="address" class="input-text" placeholder="Address" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label for="dob" class="form-label">Date of Birth: </label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <input type="date" name="dob" class="input-text" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                    </td>
                </tr>

                <tr>
                    <td>
                        <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" value="Next" class="login-btn btn-primary btn">
                    </td>
                </tr>
                <tr>
                    <td>
                        <br>
                        <label for="" class="sub-text" style="font-weight: 280;">Already have an account&#63; </label>
                        <a href="login.php" class="hover-link1 non-style-link">Login</a>
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