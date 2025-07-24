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
?>
    <?php
    
    

    //import database
    //include("../connection.php");



    if($_POST){
        //print_r($_POST);
        $result= $database->query("select * from webuser");
        $name=$_POST['name'];
        $oldemail=$_POST["oldemail"];
        $spec = isset($_POST['spec']) && is_numeric($_POST['spec']) ? intval($_POST['spec']) : 'NULL';
        $email=$_POST['email'];
        $password=$_POST['password'];
        $cpassword=$_POST['cpassword'];
        $id=$_POST['id00'];
        
        if ($password==$cpassword){
            $error='3';
            $result= $database->query("select barber.docid from barber inner join webuser on barber.docemail=webuser.email where webuser.email='$email';");
            //$resultqq= $database->query("select * from barber where docid='$id';");
            if($result->rowCount()==1){
                $id2=$result->fetch(PDO::FETCH_ASSOC)["docid"];
            }else{
                $id2=$id;
            }
            
            echo $id2."jdfjdfdh";
            if($id2!=$id){
                $error='1';
                //$resultqq1= $database->query("select * from barber where docemail='$email';");
                //$did= $resultqq1->fetch_assoc()["docid"];
                //if($resultqq1->num_rows==1){
                    
            }else{

                //$sql1="insert into barber(docemail,docname,docpassword,specialties) values('$email','$name','$password',$spec);";
                $sql1="update barber set docemail='$email',docname='$name',docpassword='$password',specialties=$spec where docid=$id ;";
                $database->query($sql1);
                
                $sql1="update webuser set email='$email' where email='$oldemail' ;";
                $database->query($sql1);
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
    

    header("location: barber.php?action=edit&error=".$error."&id=".$id);
    ?>
    
   

</body>
</html>