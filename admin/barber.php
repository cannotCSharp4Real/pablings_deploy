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
                    <td class="menu-btn menu-icon-barber menu-active menu-icon-barber-active">
                        <a href="barber.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Barber</p></a></div>
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
                    <td class="menu-btn menu-icon-customer">
                        <a href="customer.php" class="non-style-link-menu"><div><p class="menu-text">Customer</p></a></div>
                    </td>
                </tr>

            </table>
        </div>
        <div class="dash-body">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                <a href="barber.php"><button type="button" class="btn-primary" style="min-width: 110px;">&#8592; Back</button></a>
                <form action="" method="post" class="header-search" style="flex: 1; display: flex; align-items: center; gap: 12px;">
                    <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Barber name or Email" list="barber" style="flex: 1; min-width: 250px;">
                    <?php
                        echo '<datalist id="barber">';
                        $list11 = $database->query("select docname,docemail from barber;");
                        for ($y=0;$y<$list11->rowCount();$y++){
                            $row00=$list11->fetch(PDO::FETCH_ASSOC);
                            $d=$row00["docname"];
                            $c=$row00["docemail"];
                            echo "<option value='$d'><br/>";
                            echo "<option value='$c'><br/>";
                        };
                        echo ' </datalist>';
                    ?>
                    <button type="submit" class="btn-primary" style="min-width: 110px;">Search</button>
                </form>
                <div style="flex: 1;"></div>
                <div style="text-align: right;">
                    <div style="font-size: 16px; color: #888;">Today's Date</div>
                    <div style="font-size: 22px; font-weight: 600; letter-spacing: 1px; margin-top: 2px;"><?php date_default_timezone_set('Asia/Kolkata'); echo date('Y-m-d'); ?></div>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; margin-bottom: 16px;">
                <a href="?action=add&id=none&error=0" class="non-style-link"><button class="btn-primary" style="min-width: 140px;">+ Add New</button></a>
            </div>
            <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 8px;">Add New Barber</h2>
            <p style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">All Barber (<?php echo $list11->rowCount(); ?>)</p>
            <div class="abc scroll" style="padding: 0;">
                <table class="sub-table scrolldown" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="border-bottom: 3px solid #1976d2;">
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Barber Name</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Email</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Specialties</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($_POST){
                                $keyword=$_POST["search"];
                                $sqlmain= "select * from barber where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";
                            }else{
                                $sqlmain= "select * from barber order by id desc";
                            }
                            $result= $database->query($sqlmain);
                            if($result->rowCount()==0){
                                echo '<tr><td colspan="4" style="text-align:center; padding: 40px 0;">No barbers found.</td></tr>';
                            } else {
                                for ($x=0; $x<$result->rowCount(); $x++) {
                                    $row=$result->fetch(PDO::FETCH_ASSOC);
                                    $docid=$row["id"];
                                    $name=$row["docname"];
                                    $email=$row["docemail"];
                                    $spe=$row["specialties"];
                                    $spcil_res= $database->query("select sname from specialties where id='$spe'");
                                    $spcil_array= $spcil_res->fetch(PDO::FETCH_ASSOC);
                                    $spcil_name=$spcil_array["sname"];
                                    echo '<tr>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($name).'</td>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($email).'</td>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($spcil_name).'</td>';
                                    echo '<td style="padding: 12px 8px;">';
                                    echo '<div style="display: flex; gap: 12px;">';
                                    echo '<a href="?action=edit&id='.$docid.'&error=0" class="non-style-link"><button class="btn-primary" style="display: flex; align-items: center; gap: 6px;"><span style="font-size: 18px;">&#9998;</span> Edit</button></a>';
                                    echo '<a href="?action=view&id='.$docid.'" class="non-style-link"><button class="btn-primary" style="display: flex; align-items: center; gap: 6px;"><span style="font-size: 18px;">&#128065;</span> View</button></a>';
                                    echo '<a href="?action=drop&id='.$docid.'&name='.$name.'" class="non-style-link"><button class="btn-primary" style="display: flex; align-items: center; gap: 6px;"><span style="font-size: 18px;">&#128465;</span> Remove</button></a>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php 
            if($_GET){
                
                $id=$_GET["id"];
                $action=$_GET["action"];
                if($action=='drop'){
                    $nameget=$_GET["name"];
                    echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                                <h2>Are you sure?</h2>
                                <a class="close" href="barber.php">&times;</a>
                                <div class="content">
                                    You want to delete this record<br>('.substr($nameget,0,40).').
                                    
                                </div>
                                <div style="display: flex;justify-content: center;">
                                <a href="delete-barber.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                                <a href="barber.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>

                                </div>
                            </center>
                    </div>
                    </div>
                    ';
                }elseif($action=='view'){
                    $sqlmain= "select * from barber where id='$id'";
                    $result= $database->query($sqlmain);
                    $row=$result->fetch(PDO::FETCH_ASSOC);
                    $name=$row["docname"];
                    $email=$row["docemail"];
                    $spe=$row["specialties"];
                    
                    $spcil_res= $database->query("select sname from specialties where id='$spe'");
                    $spcil_array= $spcil_res->fetch(PDO::FETCH_ASSOC);
                    $spcil_name=$spcil_array["sname"];
                    echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                                <h2></h2>
                                <a class="close" href="barber.php">&times;</a>
                                <div class="content">
                                    Pablings Barbershop<br>
                                    
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
                                            <label for="spec" class="form-label">Specialties: </label>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                    <td class="label-td" colspan="2">
                                    '.$spcil_name.'<br><br>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <a href="barber.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a>
                                        
                                            
                                        </td>
                
                                    </tr>
                                   

                                </table>
                                </div>
                            </center>
                            <br><br>
                    </div>
                    </div>
                    ';
                }elseif($action=='add'){
                        $error_1=$_GET["error"];
                        $errorlist= array(
                            '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
                            '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
                            '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                            '4'=>"",
                            '0'=>'',

                        );
                        if($error_1!='4'){
                        echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            
                                <a class="close" href="barber.php">&times;</a> 
                                <div style="display: flex;justify-content: center;">
                                <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                        <td class="label-td" colspan="2">'.
                                            $errorlist[$error_1]
                                        .'</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Barber.</p><br><br>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <form action="add-new.php" method="POST" class="add-new-form">
                                        <td class="label-td" colspan="2">
                                            <label for="name" class="form-label">Name: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="name" class="input-text" placeholder="Barber Name" required><br>
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Email" class="form-label">Email: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="email" name="email" class="input-text" placeholder="Email Address" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label">Choose specialties: </label>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <select name="spec" id="" class="box" >';
                                                
            
                                                $list11 = $database->query("select  * from  specialties order by sname asc;");
            
                                                for ($y=0;$y<$list11->rowCount();$y++){
                                                    $row00=$list11->fetch(PDO::FETCH_ASSOC);
                                                    $sn=$row00["sname"];
                                                    $id00=$row00["id"];
                                                    echo "<option value=".$id00.">$sn</option><br/>";
                                                };
            
            
            
                                                
                        echo     '       </select><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="password" class="form-label">Password: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="password" name="password" class="input-text" placeholder="Defind a Password" required><br>
                                </td>
                            </tr><tr>
                                <td class="label-td" colspan="2">
                                    <label for="cpassword" class="form-label">Conform Password: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="password" name="cpassword" class="input-text" placeholder="Conform Password" required><br>
                                </td>
                            </tr>
                            
                
                            <tr>
                                <td colspan="2">
                                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                
                                    <input type="submit" value="Add" class="login-btn btn-primary btn">
                                </td>
                
                            </tr>
                           
                            </form>
                            </tr>
                        </table>
                        </div>
                        </div>
                    </center>
                    <br><br>
            </div>
            </div>
            ';

            }else{
                echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            <br><br><br><br>
                                <h2>New Record Added Successfully!</h2>
                                <a class="close" href="barber.php">&times;</a>
                                <div class="content">
                                    
                                    
                                </div>
                                <div style="display: flex;justify-content: center;">
                                
                                <a href="barber.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>

                                </div>
                                <br><br>
                            </center>
                    </div>
                    </div>
        ';
            }
        }elseif($action=='edit'){
            $sqlmain= "select * from barber where id='$id'";
            $result= $database->query($sqlmain);
            $row=$result->fetch(PDO::FETCH_ASSOC);
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $spcil_res= $database->query("select sname from specialties where id='$spe'");
            $spcil_array= $spcil_res->fetch(PDO::FETCH_ASSOC);
            $spcil_name=$spcil_array["sname"];

            $error_1=$_GET["error"];
                $errorlist= array(
                    '1'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Already have an account for this Email address.</label>',
                    '2'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;">Password Conformation Error! Reconform Password</label>',
                    '3'=>'<label for="promter" class="form-label" style="color:rgb(255, 62, 62);text-align:center;"></label>',
                    '4'=>"",
                    '0'=>'',

                );

            if($error_1!='4'){
                    echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                            
                                <a class="close" href="barber.php">&times;</a> 
                                <div style="display: flex;justify-content: center;">
                                <div class="abc">
                                <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                        <td class="label-td" colspan="2">'.
                                            $errorlist[$error_1]
                                        .'</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Barber Details.</p>
                                        Barber ID : '.$id.' (Auto Generated)<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <form action="edit-doc.php" method="POST" class="add-new-form">
                                            <label for="Email" class="form-label">Email: </label>
                                            <input type="hidden" value="'.$id.'" name="id00">
                                            <input type="hidden" name="oldemail" value="'.$email.'" >
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        <input type="email" name="email" class="input-text" placeholder="Email Address" value="'.$email.'" required><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        
                                        <td class="label-td" colspan="2">
                                            <label for="name" class="form-label">Name: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="text" name="name" class="input-text" placeholder="Barber Name" value="'.$name.'" required><br>
                                        </td>
                                        
                                    </tr>

                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label">Choose specialties: (Current'.$spcil_name.')</label>
                                            
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <select name="spec" id="" class="box">';
                                                
                
                                                $list11 = $database->query("select  * from  specialties;");
                
                                                for ($y=0;$y<$list11->rowCount();$y++){
                                                    $row00=$list11->fetch(PDO::FETCH_ASSOC);
                                                    $sn=$row00["sname"];
                                                    $id00=$row00["id"];
                                                    echo "<option value=".$id00.">$sn</option><br/>";
                                                };
                
                
                
                                                
                        echo     '       </select><br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="password" class="form-label">Password: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="password" name="password" class="input-text" placeholder="Defind a Password" required><br>
                                        </td>
                                    </tr><tr>
                                        <td class="label-td" colspan="2">
                                            <label for="cpassword" class="form-label">Conform Password: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <input type="password" name="cpassword" class="input-text" placeholder="Conform Password" required><br>
                                        </td>
                                    </tr>
                                    
                        
                                    <tr>
                                        <td colspan="2">
                                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        
                                            <input type="submit" value="Save" class="login-btn btn-primary btn">
                                        </td>
                        
                                    </tr>
                                
                                    </form>
                                    </tr>
                                </table>
                                </div>
                                </div>
                            </center>
                            <br><br>
                    </div>
                    </div>
                    ';
        }else{
            echo '
                <div id="popup1" class="overlay">
                        <div class="popup">
                        <center>
                        <br><br><br><br>
                            <h2>Edit Successfully!</h2>
                            <a class="close" href="barber.php">&times;</a>
                            <div class="content">
                                
                                
                            </div>
                            <div style="display: flex;justify-content: center;">
                            
                            <a href="barber.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>

                            </div>
                            <br><br>
                        </center>
                </div>
                </div>
    ';



        }; };
    };

?>
</div>

</body>
</html>