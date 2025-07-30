<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
            exit();
        }

    }else{
        header("location: ../login.php");
        exit();
    }
    
    
    if($_GET){
        //import database
        include("../connection.php");
        $id=$_GET["id"];
        $result001= $database->query("select * from barber where id=$id;");
        $barber_data = $result001->fetch(PDO::FETCH_ASSOC);
        
        if($barber_data) {
            $email = $barber_data["docemail"];
            $sql= $database->query("delete from webuser where email='$email';");
            $sql= $database->query("delete from barber where docemail='$email';");
            header("location: barber.php");
        } else {
            // Barber not found
            header("location: barber.php");
        }
    }


?> 