<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }

    }else{
        header("location: ../login.php");
    }
    

    //import database
    include("../connection.php");
    $userrow = $database->query("select * from customer where pemail='$useremail'");
    $userfetch=$userrow->fetch(PDO::FETCH_ASSOC);
    
    // Check if user exists before accessing array keys
    if($userfetch) {
        $userid= $userfetch["id"];
        $username=$userfetch["pname"];
    } else {
        // User not found, redirect to login
        header("location: ../login.php");
        exit();
    }

    
    if($_GET){
        $id=$_GET["id"];
        $result001= $database->query("select * from customer where id=$id;");
        $customer_data = $result001->fetch(PDO::FETCH_ASSOC);
        
        if($customer_data) {
            $email = $customer_data["pemail"];
            $sql= $database->query("delete from webuser where email='$email';");
            $sql= $database->query("delete from customer where pemail='$email';");
            header("location: ../logout.php");
        } else {
            // Customer not found
            header("location: ../logout.php");
        }
    }


?>