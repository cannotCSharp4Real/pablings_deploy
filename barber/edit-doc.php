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
include("../connection.php");

// Process form submission
if($_POST){

        $result= $database->query("select * from webuser");
        $name=$_POST['name'];
        $oldemail=$_POST["oldemail"];
        $spec=$_POST['spec'];
        $email=$_POST['email'];
        $password=$_POST['password'];
        $cpassword=$_POST['cpassword'];
        $id=$_POST['id00'];
        
        if ($password==$cpassword){
            $error='3';
            $result= $database->query("select barber.id from barber inner join webuser on barber.docemail=webuser.email where webuser.email='$email';");

            if($result->num_rows==1){
                $id2=$result->fetch_assoc()["id"];
            }else{
                $id2=$id;
            }
            
            // echo $id2."jdfjdfdh"; // Removed debug output
            if($id2!=$id){
                $error='1';
            }else{


                $sql1="update barber set docemail='$email',docname='$name',docpassword='$password',specialties=$spec where id=$id ;";
                $database->query($sql1);

                $sql1="update webuser set email='$email' where email='$oldemail' ;";
                $database->query($sql1);

                $error= '4';
                
            }
            
        }else{
            $error='2';
        }
    
    
        
        
    }else{
        $error='3';
    }
    

    header("location: settings.php?action=edit&error=".$error."&id=".$id);
    exit();
?>