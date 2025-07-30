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
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];

    
    if($_GET){
        //import database
        include("../connection.php");
        $id=$_GET["id"];
        $result001= $database->query("select * from customer where pid=$id;");
        $email=($result001->fetch(PDO::FETCH_ASSOC))["pemail"];
        $sql= $database->query("delete from webuser where email='$email';");
        $sql= $database->query("delete from customer where pemail='$email';");
        //print_r($email);
        header("location: ../logout.php");
    }


?>