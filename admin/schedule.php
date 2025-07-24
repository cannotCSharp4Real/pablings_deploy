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
// Move the session query logic here so $result is always defined before use
if($_POST && (!empty($_POST["sheduledate"]) || !empty($_POST["docid"]))) {
    $sqlpt1="";
    if(!empty($_POST["sheduledate"])){
        $sheduledate=$_POST["sheduledate"];
        $sqlpt1=" schedule.scheduledate='$sheduledate' ";
    }
    $sqlpt2="";
    if(!empty($_POST["docid"])){
        $docid=$_POST["docid"];
        $sqlpt2=" barber.id=$docid ";
    }
    $sqlmain= "select schedule.scheduleid,schedule.title,barber.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join barber on schedule.docid=barber.id ";
    $sqllist=array($sqlpt1,$sqlpt2);
    $sqlkeywords=array(" where "," and ");
    $key2=0;
    foreach($sqllist as $key){
        if(!empty($key)){
            $sqlmain.=$sqlkeywords[$key2].$key;
            $key2++;
        };
    };
} else {
    $sqlmain= "select schedule.scheduleid,schedule.title,barber.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join barber on schedule.docid=barber.id  order by schedule.scheduledate desc";
}
$result= $database->query($sqlmain);
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
        
    <title>Schedule</title>
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            box-shadow: 1px 0 0 #e0e0e0;
        }
        .dash-body {
            flex: 1;
            padding: 32px 24px 24px 24px;
            background: #f7f7f7;
            min-width: 0;
            display: flex;
            flex-direction: column;
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
        .all-sessions-header {
            font-size: 18px;
            font-weight: 500;
            margin-bottom: 8px;
            margin-top: 0;
            margin-left: 0;
            padding-left: 0;
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
    <script>
        function printPage() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
</head>
<body>
    
    
    
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0" style="width:100%;">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title">Administrator</p>
                                    <p class="profile-subtitle">admin@pablings.com</p>
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
                    <td class="menu-btn menu-icon-schedule menu-active menu-icon-schedule-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Schedule</p></div></a>
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
            <?php echo '<p class="all-sessions-header">All Sessions ('.($result ? $result->rowCount() : 0).')</p>'; ?>
            <div style="display: flex; justify-content: flex-end; margin-bottom: 16px; gap: 16px; align-items: center;">
                <h2 style="font-size: 22px; font-weight: 600; margin: 0;">Schedule a Session</h2>
                <a href="?action=add-session&id=none&error=0" class="non-style-link"><button class="btn-primary" style="min-width: 160px;">+ Add a Session</button></a>
            </div>
            <form action="" method="post" style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
                <label for="date" style="font-weight: 500;">Date:</label>
                <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="min-width: 160px;">
                <label for="barber" style="font-weight: 500;">Barber:</label>
                <select name="docid" id="barber" class="box filter-container-items" style="min-width: 220px;">
                    <option value="" disabled selected hidden>Choose Barber Name from the list</option>
                    <?php 
                        $list11 = $database->query("select  * from  barber order by docname asc;");
                        for ($y=0;$y<$list11->rowCount();$y++){
                            $row00=$list11->fetch(PDO::FETCH_ASSOC);
                            $sn=$row00["docname"];
                            $id00=$row00["id"];
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
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Session Title</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Barber</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Sheduled Date & Time</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Max num that can be booked</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($result->rowCount()==0){
                                echo '<tr><td colspan="5" style="text-align:center; padding: 40px 0;">No sessions found.</td></tr>';
                            } else {
                                for ($x=0; $x<$result->rowCount(); $x++) {
                                    $row=$result->fetch(PDO::FETCH_ASSOC);
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $nop=$row["nop"];
                                    echo '<tr>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($title).'</td>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($docname).'</td>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars($scheduledate).' '.htmlspecialchars($scheduletime).'</td>';
                                    echo '<td style="padding: 12px 8px; text-align:center;">'.htmlspecialchars($nop).'</td>';
                                    echo '<td style="padding: 12px 8px;">';
                                    echo '<div style="display: flex; gap: 12px;">';
                                    echo '<a href="?action=view&id='.$scheduleid.'" class="non-style-link"><button class="btn-primary" style="display: flex; align-items: center; gap: 6px;"><span style="font-size: 18px;">&#128065;</span> View</button></a>';
                                    echo '<a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button class="btn-primary" style="display: flex; align-items: center; gap: 6px;"><span style="font-size: 18px;">&#128465;</span> Remove</button></a>';
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
                                        
            
            
                                            $list11 = $database->query("select  * from  barber order by docname asc;");
            
                                            for ($y=0;$y<$list11->rowCount();$y++){
                                                $row00=$list11->fetch(PDO::FETCH_ASSOC);
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
                                        <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;
                                    
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
                    echo '
                    <div id="popup1" class="overlay">
                            <div class="popup">
                            <center>
                                <h2>Are you sure?</h2>
                                <a class="close" href="schedule.php">&times;</a>
                                <div class="content">
                                    You want to delete this record<br>('.substr($nameget,0,40).').
                                    
                                </div>
                                <div style="display: flex;justify-content: center;">
                                <a href="delete-session.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                                <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>
                                </div>
                            </center>
                    </div>
                    </div>
                    '; 
                }elseif($action=='view'){
                    $sqlmain= "select schedule.scheduleid,schedule.title,barber.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join barber on schedule.docid=barber.id  where  schedule.scheduleid=$id";
                    $result= $database->query($sqlmain);
                    $row=$result->fetch(PDO::FETCH_ASSOC);
                    $docname=$row["docname"];
                    $scheduleid=$row["scheduleid"];
                    $title=$row["title"];
                    $scheduledate=$row["scheduledate"];
                    $scheduletime=$row["scheduletime"];
                    
                   
                    $nop=$row['nop'];

                    $sqlmain12= "select * from appointment inner join customer on customer.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.scheduleid=$id;";
                    $result12= $database->query($sqlmain12);
                    
                    // Create a hidden div for printing
                    echo '<div id="printableArea" class="no-print" style="display: none;">';
                    echo '<div class="popup" style="width: 100%; height: auto">';
                    echo '<center>';
                    echo '<h2 style="margin-bottom: 20px;">Session Details</h2>';
                    echo '<table width="100%" border="1" style="border-collapse: collapse; margin-bottom: 20px;">';
                    echo '<tr><td style="padding: 10px; font-weight: bold;">Session Title:</td><td style="padding: 10px;">'.$title.'</td></tr>';
                    echo '<tr><td style="padding: 10px; font-weight: bold;">Barber:</td><td style="padding: 10px;">'.$docname.'</td></tr>';
                    echo '<tr><td style="padding: 10px; font-weight: bold;">Scheduled Date:</td><td style="padding: 10px;">'.$scheduledate.'</td></tr>';
                    echo '<tr><td style="padding: 10px; font-weight: bold;">Scheduled Time:</td><td style="padding: 10px;">'.$scheduletime.'</td></tr>';
                    echo '<tr><td style="padding: 10px; font-weight: bold;">Maximum Customers:</td><td style="padding: 10px;">'.$nop.'</td></tr>';
                    echo '</table>';
                    
                    echo '<h3>Customers Registered:</h3>';
                    echo '<table width="100%" border="1" style="border-collapse: collapse;">';
                    echo '<tr><th style="padding: 10px; text-align: center;">Customer ID</th><th style="padding: 10px; text-align: center;">Customer Name</th><th style="padding: 10px; text-align: center;">Appointment Number</th></tr>';
                    
                    if($result12->rowCount()==0){
                        echo '<tr><td colspan="3" style="padding: 10px; text-align: center;">No customers registered yet.</td></tr>';
                    }else{
                        for ( $x=0; $x<$result12->rowCount();$x++){
                            $row=$result12->fetch(PDO::FETCH_ASSOC);
                            $apponum=$row["apponum"];
                            $pid=$row["pid"];
                            $pname=$row["pname"];
                            $ptel=$row["ptel"];
                            
                            echo '<tr>';
                            echo '<td style="padding: 10px; text-align: center;">'.substr($pid,0,15).'</td>';
                            echo '<td style="padding: 10px; font-weight: 600; text-align: center;">'.substr($pname,0,25).'</td>';
                            echo '<td style="padding: 10px; text-align: center; font-size: 18px; font-weight: 500; color: #0066cc;">'.$apponum.'</td>';
                            echo '</tr>';
                        }
                    }
                    echo '</table>';
                    echo '</center>';
                    echo '</div>';
                    echo '</div>'; // End of printable area
                    
                    echo '
                    <div id="popup1" class="overlay">
                            <div class="popup" style="width: 100%; height: auto">
                            <center>
                                <h2></h2>
                                <a class="close" href="schedule.php">&times;</a>
                                <div class="content">
                                    
                                </div>
                                <div class="abc scroll" style="display: flex;justify-content: center;">
                                <table width="70%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <tr>
                                    <td colspan="2" style="text-align: center; padding-top: 20px;">
                                        <button class="login-btn btn-primary btn" onclick="printPage()" style="margin: 10px;">
                                            <img src="../img/icons/print.svg" width="15px" style="margin-right: 8px;">Print Session Details
                                        </button>
                                    </td>
                                </tr>
                                    <tr>
                                        <td>
                                            <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        
                                        <td class="label-td" colspan="2">
                                            <label for="name" class="form-label">Session Title: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            '.$title.'<br><br>
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="Email" class="form-label">Barber of this session: </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        '.$docname.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        '.$scheduledate.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                        '.$scheduletime.'<br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="spec" class="form-label"><b>Customer that Already registerd for this session:</b> ('.$result12->rowCount()."/".$nop.')</label>
                                            <br><br>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                    <td colspan="4">
                                        <center>
                                         <div class="abc scroll">
                                         <table width="100%" class="sub-table scrolldown" border="0">
                                         <thead>
                                         <tr>   
                                                <th class="table-headin">
                                                     Customer ID
                                                 </th>
                                                 <th class="table-headin">
                                                     Customer name
                                                 </th>
                                                 <th class="table-headin">
                                                     
                                                     Appointment number
                                                     
                                                 </th>
                                                 
                                             </thead>
                                             <tbody>';
                                             
                                            
                                            
                                             
                                            $result= $database->query($sqlmain12);
                                            
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
                                            for ( $x=0; $x<$result->rowCount();$x++){
                                                $row=$result->fetch(PDO::FETCH_ASSOC);
                                                $apponum=$row["apponum"];
                                                $pid=$row["pid"];
                                                $pname=$row["pname"];
                                                $ptel=$row["ptel"];
                                                
                                                echo '<tr style="text-align:center;">
                                                   <td>
                                                   '.substr($pid,0,15).
                                                   '</td>
                                                    <td style="font-weight:600;padding:25px">'.
                                                    
                                                    substr($pname,0,25)
                                                    .'</td >
                                                    <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                                    '.$apponum.'
                                                    
                                                    </td>
                                                    <td>
                                                    '.substr($ptel,0,25).'
                                                    </td>
                                                    
                                                    
                                                    
                                                    
                                                </tr>';
                                                
                                            }
                                        }
                                          
                                          
                                        echo '</tbody>
                                        
                                        </table>
                                        </div>
                                        </center>
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
</div>
</body>
</html>