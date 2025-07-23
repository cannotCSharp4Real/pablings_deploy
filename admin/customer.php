<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Customer</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .menu {
            width: 240px;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            min-height: 100vh;
            padding-top: 0;
            position: sticky;
            top: 0;
        }
        .dash-body {
            flex: 1;
            padding: 16px 8px 16px 8px;
            background: #f7f7f7;
            min-width: 0;
        }
        .logout-btn {
            width: 90%;
            margin: 20px 5% 0 5%;
        }
        .profile-title, .profile-subtitle {
            white-space: normal;
            word-break: break-all;
        }
        .heading-main12 {
            font-size: 22px;
            font-weight: 600;
            margin: 24px 0 12px 0;
        }
        .heading-sub12 {
            font-size: 16px;
            color: #888;
        }
        .btn-primary-soft, .btn-primary {
            min-width: 120px;
            font-size: 16px;
            border-radius: 6px;
        }
        .btn-icon-back {
            margin-bottom: 16px;
        }
        .filter-container {
            margin: 16px 0 24px 0;
        }
        .abc.scroll {
            max-height: 350px;
            overflow-y: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 16px;
        }
        .sub-table {
            width: 100%;
        }
        .notfound-img {
            width: 120px;
            margin: 24px 0 12px 0;
            display: block;
        }
        @media (max-width: 900px) {
            .container {
                flex-direction: column;
            }
            .menu {
                width: 100%;
                min-height: unset;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }
            .dash-body {
                padding: 16px 8px;
            }
        }
        @media (max-width: 600px) {
            .heading-main12 {
                font-size: 18px;
            }
            .dash-body {
                padding: 8px 2px;
            }
            .logout-btn {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <?php

    //learn from w3schools.com

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='a'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    

    //import database
    include("../connection.php");

    
    ?>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@edoc.com</p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                <a href="../logout.php" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-barber ">
                        <a href="barber.php" class="non-style-link-menu "><div><p class="menu-text">Barber</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-customer  menu-active menu-icon-customer-active">
                        <a href="customer.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">Customer</p></a></div>
                    </td>
                </tr>

            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%">

                    <a href="customer.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                        
                    </td>
                    <td>
                        
                        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                            <a href="customer.php"><button type="button" class="btn-primary" style="min-width: 110px;">&larr; Back</button></a>
                            <form action="" method="post" class="header-search" style="flex: 1; display: flex; align-items: center; gap: 12px;">
                                <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Customer name or Email" list="customer" style="flex: 1; min-width: 250px;">
                                <?php
                                    echo '<datalist id="customer">';
                                    $list11 = $database->query("select  pname,pemail from customer;");
                                    for ($y=0;$y<$list11->rowCount();$y++){
                                        $row00=$list11->fetch_assoc();
                                        $d=$row00["pname"];
                                        $c=$row00["pemail"];
                                        echo "<option value='$d'><br/>";
                                        echo "<option value='$c'><br/>";
                                    };
                                    echo ' </datalist>';
                                ?>
                                <button type="submit" class="btn-primary" style="min-width: 110px;">Search</button>
                            </form>
                        </div>
                        <p style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">All Customer (<?php echo $list11->rowCount(); ?>)</p>
                        <div class="abc scroll" style="padding: 0;">
                            <table class="sub-table scrolldown" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #1976d2;">
                                        <th class="table-headin" style="font-size: 16px; font-weight: 600; padding: 12px 8px;">Name</th>
                                        <th class="table-headin" style="font-size: 16px; font-weight: 600; padding: 12px 8px;">Email</th>
                                        <th class="table-headin" style="font-size: 16px; font-weight: 600; padding: 12px 8px;">Date of Birth</th>
                                        <th class="table-headin" style="font-size: 16px; font-weight: 600; padding: 12px 8px;">Events</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if($_POST){
                                            $keyword=$_POST["search"];
                                            
                                            $sqlmain= "select * from customer where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%' ";
                                        }else{
                                            $sqlmain= "select * from customer order by pid desc";

                                        }



                                    ?>
                                      
                                    <tr>
                                       <td colspan="4">
                                           <center>
                                            <div class="abc scroll">
                                            <table width="93%" class="sub-table scrolldown"  style="border-spacing:0;">
                                            <thead>
                                            <tr>
                                                    <th class="table-headin">
                                                        
                                                    
                                                    Name
                                                    
                                                    </th>
                                                    <th class="table-headin">
                                                        
                                                    
                                                        Email
                                                    </th>
                                                    <th class="table-headin">
                                                        
                                                        Date of Birth
                                                        
                                                    </th>
                                                    <th class="table-headin">
                                                        
                                                        Events
                                                        
                                                    </tr>
                                            </thead>
                                            <tbody>
                                            
                                                <?php

                                                    
                                                    $result= $database->query($sqlmain);

                                                    if($result->rowCount()==0){
                                                        echo '<tr><td colspan="4" style="text-align:center; padding: 40px 0;">No customers found.</td></tr>';
                                                    } else {
                                                        for ($x=0; $x<$result->rowCount(); $x++) {
                                                            $row=$result->fetch_assoc();
                                                            $pid=$row["pid"];
                                                            $name=$row["pname"];
                                                            $email=$row["pemail"];
                                                            $dob=$row["pdob"];
                                                            echo '<tr>';
                                                            echo '<td style="padding: 12px 8px;">'.htmlspecialchars($name).'</td>';
                                                            echo '<td style="padding: 12px 8px;">'.htmlspecialchars($email).'</td>';
                                                            echo '<td style="padding: 12px 8px;">'.htmlspecialchars($dob).'</td>';
                                                            echo '<td style="padding: 12px 8px;">';
                                                            echo '<div style="display: flex; gap: 8px;">';
                                                            echo '<a href="?action=view&id='.$pid.'" class="non-style-link"><button class="btn-primary">View</button></a>';
                                                            echo '</div>';
                                                            echo '</td>';
                                                            echo '</tr>';
                                                        }
                                                    }
                                                 
                                                ?>
 
                                                </tbody>

                                            </table>
                                            </div>
                                            </center>
                                       </td> 
                                    </tr>
                                       
                                        
                                        
                                </table>
                            </div>
                        </div>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                        date_default_timezone_set('Asia/Kolkata');

                        $date = date('Y-m-d');
                        echo $date;
                        ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>


                </tr>
                       
                        
                        
            </table>
        </div>
    </div>
    <?php 
    if($_GET){
        
        $id=$_GET["id"];
        $action=$_GET["action"];
            $sqlmain= "select * from customer where pid='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $name=$row["pname"];
            $email=$row["pemail"];
            $dob=$row["pdob"];
            $address=$row["paddress"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <a class="close" href="customer.php">&times;</a>
                        <div class="content">

                        </div>
                        <div style="display: flex;justify-content: center;">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                </td>
                            </tr>
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Customer ID: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    P-'.$id.'<br><br>
                                </td>
                                
                            </tr>
                            
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Name: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$name.'<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="Email" class="form-label">Email: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                '.$email.'<br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="spec" class="form-label">Address: </label>
                                    
                                </td>
                            </tr>
                            <tr>
                            <td class="label-td" colspan="2">
                            '.$address.'<br><br>
                            </td>
                            </tr>
                            <tr>
                                
                                <td class="label-td" colspan="2">
                                    <label for="name" class="form-label">Date of Birth: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    '.$dob.'<br><br>
                                </td>
                                
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="customer.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                
                                    
                                </td>
                
                            </tr>
                           

                        </table>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';
        
    };

?>
</div>

</body>
</html>