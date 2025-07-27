<?php
session_start();

// Check if user is logged in
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
        exit();
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

// Import database connection
include("../connection.php");

// Get current user information
$userrow = $database->query("select * from customer where pemail='$useremail'");
$userfetch=$userrow->fetch(PDO::FETCH_ASSOC);
$userid= $userfetch["id"];

if($_POST){
    $name = $_POST['name'];
    $oldemail = $_POST["oldemail"];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $id = $_POST['id00'];
    
    // Validate that the user is editing their own profile
    if($id != $userid) {
        header("location: settings.php?action=edit&error=unauthorized");
        exit();
    }
    
    if ($password == $cpassword){
        $error = '3';
        
        // Check if email already exists (excluding current user)
        $checkQuery = "SELECT id FROM customer WHERE pemail = ? AND id != ?";
        $checkStmt = $database->prepare($checkQuery);
        $checkStmt->execute([$email, $userid]);
        
        if($checkStmt->rowCount() > 0){
            $error = '1'; // Email already exists
        } else {
            try {
                // Update customer information
                $updateCustomerQuery = "UPDATE customer SET pemail = ?, pname = ?, ppassword = ?, paddress = ? WHERE id = ?";
                $updateCustomerStmt = $database->prepare($updateCustomerQuery);
                $updateCustomerStmt->execute([$email, $name, $password, $address, $userid]);
                
                // Update webuser email if it exists
                $updateWebuserQuery = "UPDATE webuser SET email = ? WHERE email = ?";
                $updateWebuserStmt = $database->prepare($updateWebuserQuery);
                $updateWebuserStmt->execute([$email, $oldemail]);
                
                // Update session with new email
                $_SESSION["user"] = $email;
                
                $error = '4'; // Success
            } catch (Exception $e) {
                $error = 'database_error';
            }
        }
    } else {
        $error = '2'; // Password mismatch
    }
} else {
    $error = '3'; // No POST data
}

header("location: settings.php?action=edit&error=".$error."&id=".$userid);
exit();
?>