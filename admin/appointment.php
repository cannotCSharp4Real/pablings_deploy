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
$list110 = $database->query("select * from appointment;");
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
        
    <title>Appointment</title>
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
                    <td class="menu-btn menu-icon-barber ">
                        <a href="barber.php" class="non-style-link-menu "><div><p class="menu-text">Barber</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule ">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active">
                        <a href="appointment.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Appointment</p></a></div>
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
                <a href="appointment.php"><button type="button" class="btn-primary" style="min-width: 110px;">&#8592; Back</button></a>
                <span style="font-size: 28px; font-weight: 600; flex: 1; text-align: left;">Appointment Manager</span>
                <div style="flex: 1;"></div>
                <div style="text-align: right;">
                    <div style="font-size: 16px; color: #888;">Today's Date</div>
                    <div style="font-size: 22px; font-weight: 600; letter-spacing: 1px; margin-top: 2px;"><?php date_default_timezone_set('Asia/Kolkata'); echo date('Y-m-d'); ?></div>
                </div>
            </div>
            <p style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">All Appointments (<?php echo $list110->rowCount(); ?>)</p>
            <form action="" method="post" style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                <label for="date" style="font-weight: 500;">Date:</label>
                <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="min-width: 160px;">
                <label for="barber" style="font-weight: 500;">Barber:</label>
                <select name="docid" id="barber" class="box filter-container-items" style="min-width: 220px;">
                    <option value="" disabled selected hidden>Choose Barber Name from the list</option>
                    <?php 
                        $list11 = $database->query("select  * from  barber order by docname asc;");
                        for ($y=0;$y<$list11->rowCount();$y++){
                            $row00=$list11->fetch_assoc();
                            $sn=$row00["docname"];
                            $id00=$row00["docid"];
                            echo "<option value=".$id00.">$sn</option>";
                        };
                    ?>
                </select>
                <button type="submit" name="filter" class="btn-primary" style="min-width: 110px; display: flex; align-items: center; gap: 6px;">&#128269; Filter</button>
            </form>
            <div class="abc scroll" style="padding: 0;">
                <table class="sub-table scrolldown" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="border-bottom: 3px solid #1976d2;">
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Customer name</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Appointment number</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Barber</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Session Title</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Session Date & Time</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Appointment Date</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($_POST){
                                $sqlpt1="";
                                if(!empty($_POST["sheduledate"])){
                                    $sheduledate=$_POST["sheduledate"];
                                    $sqlpt1=" schedule.scheduledate='$sheduledate' ";
                                }
                                $sqlpt2="";
                                if(!empty($_POST["docid"])){
                                    $docid=$_POST["docid"];
                                    $sqlpt2=" barber.docid=$docid ";
                                }
                                $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,barber.docname,customer.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join customer on customer.id=appointment.pid inner join barber on schedule.docid=barber.docid";
                                $sqllist=array($sqlpt1,$sqlpt2);
                                $sqlkeywords=array(" where "," and ");
                                $key2=0;
                                foreach($sqllist as $key){
                                    if(!empty($key)){
                                        $sqlmain.=$sqlkeywords[$key2].$key;
                                        $key2++;
                                    };
                                };
                            }else{
                                $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,barber.docname,customer.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join customer on customer.id=appointment.pid inner join barber on schedule.docid=barber.docid  order by schedule.scheduledate desc";
                            }
                            $result= $database->query($sqlmain);
                            if($result->rowCount()==0){
                                echo '<tr><td colspan="7" style="text-align:center; padding: 40px 0;">No appointments found.</td></tr>';
                            } else {
                                for ($x=0; $x<$result->rowCount(); $x++) {
                                    $row=$result->fetch_assoc();
                                    $appoid=$row["appoid"];
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $pname=$row["pname"];
                                    $apponum=$row["apponum"];
                                    $appodate=$row["appodate"];
                                    echo '<tr>';
                                    echo '<td style="font-weight:600; padding: 12px 8px;">'.htmlspecialchars($pname).'</td>';
                                    echo '<td style="text-align:center;font-size:23px;font-weight:500; color: #1976d2; padding: 12px 8px;">'.htmlspecialchars($apponum).'</td>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($docname).'</td>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($title).'</td>';
                                    echo '<td style="text-align:center;font-size:12px; padding: 12px 8px;">'.htmlspecialchars($scheduledate).' <br>'.htmlspecialchars($scheduletime).'</td>';
                                    echo '<td style="text-align:center; padding: 12px 8px;">'.htmlspecialchars($appodate).'</td>';
                                    echo '<td style="padding: 12px 8px;">';
                                    echo '<div style="display: flex; gap: 12px;">';
                                    echo '<a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button class="btn-primary" style="display: flex; align-items: center; gap: 6px;"><span style="font-size: 18px;">&#128465;</span> Cancel</button></a>';
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
            
                                            for ($y=0;$y<$list11->rowCount();$y++){
                                                $row00=$list11->fetch_assoc();
                                                $sn=$row00["docname"];
                                                $id00=$row00["docid"];
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
                    $sqlmain= "select * from barber where docid='$id'";
                    $result= $database->query($sqlmain);
                    $row=$result->fetch_assoc();
                    $name=$row["docname"];
                    $email=$row["docemail"];
                    $spe=$row["specialties"];
                    
                    $spcil_res= $database->query("select sname from specialties where id='$spe'");
                    $spcil_array= $spcil_res->fetch_assoc();
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
            }
        }

        ?>
        </div>

    </div>

</body>
</html>