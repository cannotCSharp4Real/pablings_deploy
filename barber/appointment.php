<?php
// Move all PHP code to the top before any HTML
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" || $_SESSION['usertype']!='d'){
        header("location: ../login.php");
        exit();
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

// Import database
include("../connection.php");
$userrow = $database->query("select * from barber where docemail='$useremail'");
$userfetch = $userrow->fetch(PDO::FETCH_ASSOC);
$userid = $userfetch["id"];
$username = $userfetch["docname"];
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
        
    <title>Appointments</title>
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
            padding: 32px 24px 24px 24px;
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
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
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
                                    <p class="profile-title"><?php echo substr($username,0,13)  ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail,0,22)  ?></p>
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
                    <td class="menu-btn">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">My Appointments</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn">
                        <a href="customer.php" class="non-style-link-menu"><div><p class="menu-text">My Customer</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" >
                    <a href="appointment.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Appointment Manager</p>
                                           
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php 
                        date_default_timezone_set('Asia/Kolkata');
                        $today = date('Y-m-d');
                        echo $today;
                        // Update SQL join for customer table
                        $list110 = $database->query("select * from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join customer on customer.id=appointment.pid inner join barber on schedule.docid=barber.id  where  barber.id=$userid ");
                        ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">My Appointments (<?php echo $list110->rowCount(); ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
                        <tr>
                           <td width="10%">
                           </td> 
                        <td width="5%" style="text-align: center;">
                        Date:
                        </td>
                        <td width="30%">
                        <form action="" method="post">
                            <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                        </td>
                    <td width="12%">
                        <input type="submit"  name="filter" value=" Filter" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                        </form>
                    </td>
                    </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <?php
                    // Update main SQL for appointments
                    $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,barber.docname,customer.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join customer on customer.id=appointment.pid inner join barber on schedule.docid=barber.id  where  barber.id=$userid ";
                    if($_POST){
                        if(!empty($_POST["sheduledate"])){
                            $sheduledate=$_POST["sheduledate"];
                            $sqlmain.=" and schedule.scheduledate='$sheduledate' ";
                        };
                    }
                ?>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                                <th class="table-headin">
                                    Customer name
                                </th>
                                <th class="table-headin">
                                    Appointment number
                                </th>
                                <th class="table-headin">
                                    Session Title
                                    </th>
                                <th class="table-headin" >
                                    Session Date & Time
                                </th>
                                <th class="table-headin">
                                    Appointment Date
                                </th>
                                <th class="table-headin">
                                    Events
                                </tr>
                        </thead>
                        <tbody>
                            <?php
                                $result= $database->query($sqlmain);
                                if($result->rowCount()==0){
                                    echo '<tr>
                                    <td colspan="7">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound2.svg" width="25%">
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                    <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                }
                                else{
                                while($row=$result->fetch(PDO::FETCH_ASSOC)){
                                    $appoid=$row["appoid"];
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $pname=$row["pname"];
                                    $apponum=$row["apponum"];
                                    $appodate=$row["appodate"];
                                    echo '<tr >
                                        <td style="font-weight:600;"> &nbsp;'.
                                        substr($pname,0,25)
                                        .'</td >
                                        <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                        '.$apponum.'
                                        </td>
                                        <td>
                                        '.substr($title,0,15).'
                                        </td>
                                        <td style="text-align:center;;">
                                            '.substr($scheduledate,0,10).' @'.substr($scheduletime,0,5).'
                                        </td>
                                        <td style="text-align:center;">
                                            '.$appodate.'
                                        </td>
                                        <td>
                                        <div style="display:flex;justify-content: center;">
                                       <a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>
                                       &nbsp;&nbsp;&nbsp;</div>
                                        </td>
                                    </tr>';
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
    <?php
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='add-session'){
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <a class="close" href="schedule.php">&times;</a> 
                        <div style="display: flex;justify-content: center;">
                        <div class="abc">
                        <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                                <td class="label-td" colspan="2">'.
                                   ""
                                .'</td>
                            </tr>
                            <tr>
                                <td>
                                    <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Session.</p><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                <form action="add-session.php" method="POST" class="add-new-form">
                                    <label for="title" class="form-label">Session Title : </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="text" name="title" class="input-text" placeholder="Name of this Session" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="docid" class="form-label">Select Barber: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <select name="docid" id="" class="box" >
                                    <option value="" disabled selected hidden>Choose Barber Name from the list</option><br/>';
                                        $list11 = $database->query("select  * from  barber;");
                                        while($row00=$list11->fetch(PDO::FETCH_ASSOC)){
                                            $sn=$row00["docname"];
                                            $id00=$row00["id"];
                                            echo "<option value=".$id00.">$sn</option><br/>";
                                        };
                        echo     '       </select><br><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="nop" class="form-label">Number of Customer/Appointment Numbers : </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="number" name="nop" class="input-text" min="0"  placeholder="The final appointment number for this session depends on this number" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="date" class="form-label">Session Date: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="date" name="date" class="input-text" min="'.date('Y-m-d').'" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <label for="time" class="form-label">Schedule Time: </label>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-td" colspan="2">
                                    <input type="time" name="time" class="input-text" placeholder="Time" required><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <input type="submit" value="Place this Session" class="login-btn btn-primary btn" name="shedulesubmit">
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
        }elseif($action=='session-added'){
            $titleget=$_GET["title"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                    <br><br>
                        <h2>Session Placed.</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                        '.substr($titleget,0,40).' was scheduled.<br><br>
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
                        <br><br><br><br>
                        </div>
                    </center>
            </div>
            </div>
            ';
        }elseif($action=='drop'){
            $nameget=$_GET["name"];
            $session=$_GET["session"];
            $apponum=$_GET["apponum"];
            echo '
            <div id="popup1" class="overlay">
                    <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="appointment.php">&times;</a>
                        <div class="content">
                            You want to delete this record<br><br>
                            Customer Name: &nbsp;<b>'.substr($nameget,0,40).'</b><br>
                            Appointment number &nbsp; : <b>'.substr($apponum,0,40).'</b><br><br>
                        </div>
                        <div style="display: flex;justify-content: center;">
                        <a href="delete-appointment.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                        <a href="appointment.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>
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
                            eDoc Web App<br>
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
    }
}
    ?>
    </div>
</body>
</html>