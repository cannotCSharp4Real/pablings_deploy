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
        @media print {
            body * {
                visibility: visible;
            }
            #printableArea, #printableArea * {
                visibility: visible;
            }
            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                box-sizing: border-box;
            }
            .no-print {
                display: none;
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
        
        // Ensure popup is visible when view action is triggered
        window.onload = function() {
            if(window.location.href.indexOf('action=view') > -1) {
                var popup = document.getElementById('popup1');
                if(popup) {
                    popup.style.display = 'block';
                }
            }
        }
    </script>
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
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-barber">
                        <a href="barber.php" class="non-style-link-menu"><div><p class="menu-text">Barber</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-schedule menu-active menu-icon-schedule-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-customer">
                        <a href="customer.php" class="non-style-link-menu"><div><p class="menu-text">Customer</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                <a href="schedule.php"><button type="button" class="btn-primary" style="min-width: 110px;">&#8592; Back</button></a>
                <div style="flex: 1;">
                    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 8px;">Schedule Manager</h2>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 16px; color: #888;">Today's Date</div>
                    <div style="font-size: 22px; font-weight: 600; letter-spacing: 1px; margin-top: 2px;"><?php 
                        date_default_timezone_set('Asia/Kolkata');
                        $today = date('Y-m-d');
                        echo $today;
                        $list110 = $database->query("select  * from  schedule;");
                    ?></div>
                </div>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 8px;">Schedule a Session</h2>
                <a href="?action=add-session&id=none&error=0" class="non-style-link"><button class="btn-primary" style="min-width: 140px;">+ Add Session</button></a>
            </div>
            
            <p style="font-size: 18px; font-weight: 500; margin-bottom: 8px;">All Sessions (<?php echo $list110->rowCount(); ?>)</p>
            
            <div class="filter-container">
                <form action="" method="post" style="display: flex; align-items: center; gap: 16px; background: #fff; padding: 16px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label style="font-weight: 500; min-width: 60px;">Date:</label>
                        <input type="date" name="sheduledate" id="date" class="input-text" style="min-width: 150px;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label style="font-weight: 500; min-width: 60px;">Barber:</label>
                        <select name="docid" class="input-text" style="min-width: 200px;">
                            <option value="" disabled selected hidden>Choose Barber Name from the list</option>
                            <?php 
                                $list11 = $database->query("select  * from  barber order by docname asc;");
                                for ($y=0;$y<$list11->rowCount();$y++){
                                    $row00=$list11->fetch(PDO::FETCH_ASSOC);
                                    $sn=$row00["docname"];
                                    $id00=$row00["id"];
                                    echo "<option value=".$id00.">$sn</option><br/>";
                                };
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="filter" value="Filter" class="btn-primary" style="min-width: 100px;">Filter</button>
                </form>
            </div>
                
                <?php
                    if($_POST){
                        //print_r($_POST);
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
                        //echo $sqlpt2;
                        //echo $sqlpt1;
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
                        //echo $sqlmain;
                        
                        
                        //
                    }else{
                        $sqlmain= "select schedule.scheduleid,schedule.title,barber.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join barber on schedule.docid=barber.id  order by schedule.scheduledate desc";
                    }

                ?>
                  
                            <div class="abc scroll" style="padding: 0;">
                <table class="sub-table scrolldown" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="border-bottom: 3px solid #1976d2;">
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Session Title</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Barber</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Scheduled Date & Time</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Max Bookings</th>
                            <th class="table-headin" style="font-size: 18px; font-weight: 600; padding: 12px 8px;">Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $result= $database->query($sqlmain);
                            if($result->rowCount()==0){
                                echo '<tr><td colspan="5" style="text-align:center; padding: 40px 0;">No sessions found.</td></tr>';
                            } else {
                                for ( $x=0; $x<$result->rowCount();$x++){
                                    $row=$result->fetch(PDO::FETCH_ASSOC);
                                    $scheduleid=$row["scheduleid"];
                                    $title=$row["title"];
                                    $docname=$row["docname"];
                                    $scheduledate=$row["scheduledate"];
                                    $scheduletime=$row["scheduletime"];
                                    $nop=$row["nop"];
                                    echo '<tr>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars(substr($title,0,30)).'</td>';
                                    echo '<td style="padding: 12px 8px;">'.htmlspecialchars(substr($docname,0,20)).'</td>';
                                    echo '<td style="padding: 12px 8px; text-align: center;">'.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'</td>';
                                    echo '<td style="padding: 12px 8px; text-align: center;">'.$nop.'</td>';
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
            // View action triggered
            error_log("View action triggered for schedule ID: " . $id);
            $sqlmain= "select schedule.scheduleid,schedule.title,barber.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join barber on schedule.docid=barber.id  where  schedule.scheduleid=$id";
            $result= $database->query($sqlmain);
            
            if($result->rowCount() == 0) {
                echo '<div id="popup1" class="overlay" style="display: block; z-index: 1000;">
                        <div class="popup" style="width: 80%; max-width: 800px; height: auto; margin: 50px auto;">
                        <center>
                            <h2>Error</h2>
                            <a class="close" href="schedule.php">&times;</a>
                            <div class="content">
                                Schedule not found with ID: '.$id.'
                            </div>
                            <div style="display: flex;justify-content: center;">
                            <a href="schedule.php" class="non-style-link"><button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
                            </div>
                        </center>
                </div>
                </div>';
                exit();
            }
            
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
            
            echo '<div id="popup1" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); z-index: 9999; display: block;">
                    <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 8px; padding: 30px; max-width: 800px; width: 90%; max-height: 90%; overflow-y: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h2 style="margin: 0; color: #333;">Session Details</h2>
                            <a href="schedule.php" style="font-size: 24px; text-decoration: none; color: #333; font-weight: bold;">&times;</a>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <p><strong>Session Title:</strong> '.$title.'</p>
                            <p><strong>Barber:</strong> '.$docname.'</p>
                            <p><strong>Date:</strong> '.$scheduledate.'</p>
                            <p><strong>Time:</strong> '.$scheduletime.'</p>
                            <p><strong>Max Bookings:</strong> '.$nop.'</p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h3>Registered Customers ('.$result12->rowCount().'/'.$nop.')</h3>
                            <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; max-height: 300px; overflow-y: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="border-bottom: 2px solid #ddd;">
                                            <th style="padding: 10px; text-align: left;">Customer ID</th>
                                            <th style="padding: 10px; text-align: left;">Customer Name</th>
                                            <th style="padding: 10px; text-align: center;">Appointment #</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                                    
                                    $result= $database->query($sqlmain12);
                                    if($result->rowCount()==0){
                                        echo '<tr><td colspan="3" style="padding: 20px; text-align: center; color: #666;">No customers registered yet.</td></tr>';
                                    } else {
                                        for ( $x=0; $x<$result->rowCount();$x++){
                                            $row=$result->fetch(PDO::FETCH_ASSOC);
                                            $apponum=$row["apponum"];
                                            $pid=$row["pid"];
                                            $pname=$row["pname"];
                                            
                                            echo '<tr style="border-bottom: 1px solid #eee;">
                                                <td style="padding: 10px;">'.substr($pid,0,15).'</td>
                                                <td style="padding: 10px; font-weight: 600;">'.substr($pname,0,25).'</td>
                                                <td style="padding: 10px; text-align: center; font-size: 18px; font-weight: 500; color: #0066cc;">'.$apponum.'</td>
                                            </tr>';
                                        }
                                    }
                                    
                                    echo '</tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div style="text-align: center;">
                            <button onclick="printPage()" style="background: #0066cc; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-right: 10px;">
                                🖨️ Print Details
                            </button>
                            <a href="schedule.php" style="background: #666; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; display: inline-block;">
                                Close
                            </a>
                        </div>
                    </div>
                </div>';  
    }
}
        
    ?>
    </div>
</body>
</html>