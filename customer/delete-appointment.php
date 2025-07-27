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

// Get user information
$userrow = $database->query("select * from customer where pemail='$useremail'");
$userfetch=$userrow->fetch(PDO::FETCH_ASSOC);
$userid= $userfetch["id"];

// Check if appointment ID is provided
if(isset($_GET["id"])){
    $appointmentid = $_GET["id"];
    
    try {
        // First, verify that this appointment belongs to the current user
        $checkQuery = "SELECT * FROM appointment WHERE appoid = ? AND pid = ?";
        $checkStmt = $database->prepare($checkQuery);
        $checkStmt->execute([$appointmentid, $userid]);
        
        if($checkStmt->rowCount() > 0) {
            // Delete the appointment
            $deleteQuery = "DELETE FROM appointment WHERE appoid = ? AND pid = ?";
            $deleteStmt = $database->prepare($deleteQuery);
            $deleteStmt->execute([$appointmentid, $userid]);
            
            // Redirect with success message
            header("location: appointment.php?action=deleted&id=".$appointmentid);
            exit();
        } else {
            // Appointment not found or doesn't belong to user
            header("location: appointment.php?action=error&message=appointment_not_found");
            exit();
        }
    } catch (Exception $e) {
        // Database error
        header("location: appointment.php?action=error&message=database_error");
        exit();
    }
} else {
    // No appointment ID provided
    header("location: appointment.php?action=error&message=no_id_provided");
    exit();
}
?>