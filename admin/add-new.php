<?php
session_start();
if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
        header("location: ../login.php");
        exit();
    }
}else{
    header("location: ../login.php");
    exit();
}
include("../connection.php");

if($_POST){
    //print_r($_POST);
    $result= $database->query("select * from webuser");
    $name=$_POST['name'];
    $spec=$_POST['spec'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $cpassword=$_POST['cpassword'];
    
    if ($password==$cpassword){
        $error='3';
        $result= $database->query("select * from webuser where email='$email';");
        if($result->rowCount()==1){
            $error='1';
        }else{

            // Hash the password before storing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql1="insert into barber(docemail,docname,docpassword,specialties) values('$email','$name','$hashed_password',$spec);";
            $sql2="insert into webuser(email,usertype) values('$email','d')";
            $database->query($sql1);
            $database->query($sql2);

            //echo $sql1;
            //echo $sql2;
            $error= '4';
            
        }
        
    }else{
        $error='2';
    }

    
    
    
}else{
    //header('location: signup.php');
    $error='3';
}


header("location: barber.php?action=add&error=".$error);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Barber</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
</style>
</head>
<body>
    
   

</body>
</html>