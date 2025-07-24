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

$selecttype="My";
$current="My Customer Only";
if($_POST){
    if(isset($_POST["search"])){
        $keyword=$_POST["search12"];
        $sqlmain= "select * from customer where pemail='$keyword' or pname='$keyword' or pname like '$keyword%' or pname like '%$keyword' or pname like '%$keyword%' ";
        $selecttype="my";
    }
    if(isset($_POST["filter"])){
        if($_POST["showonly"]=='all'){
            $sqlmain= "select * from customer";
            $selecttype="All";
            $current="All customer";
        }else{
            $sqlmain= "select * from appointment inner join customer on customer.id=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";
            $selecttype="My";
            $current="My customer Only";
        }
    }
}else{
    $sqlmain= "select * from appointment inner join customer on customer.id=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.docid=$userid;";
    $selecttype="My";
}
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
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Appointments</p></a></div>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">My Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-customer menu-active menu-icon-customer-active">
                        <a href="customer.php" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">My Customer</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings   ">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></a></div>
                    </td>
                </tr>
                
            </table>
        </div>
        <?php       
            echo '<div class="dash-body">';
            echo '<table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">';
            echo '<tr >';
            echo '<td width="13%">';
            echo '<a href="customer.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>';
            echo '</td>';
            echo '<td>';
            echo '<form action="" method="post" class="header-search">';
            echo '<input type="search" name="search12" class="input-text header-searchbar" placeholder="Search Customer name or Email" list="customer">&nbsp;&nbsp;';
            echo '<datalist id="customer">';
            $list11 = $database->query($sqlmain);
            while($row00=$list11->fetch(PDO::FETCH_ASSOC)){
                $d=$row00["pname"];
                $c=$row00["pemail"];
                echo "<option value='$d'><br/>";
                echo "<option value='$c'><br/>";
            }
            echo ' </datalist>';
            echo '<input type="Submit" value="Search" name="search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">';
            echo '</form>';
            echo '</td>';
            echo '<td width="15%">';
            echo '<p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today\'s Date</p>';
            echo '<p class="heading-sub12" style="padding: 0;margin: 0;">'.date('Y-m-d').'</p>';
            echo '</td>';
            echo '<td width="10%">';
            echo '<button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>';
            echo '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="4" style="padding-top:10px;">';
            echo '<p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">'.$selecttype." Customer (".$list11->rowCount().")".'</p>';
            echo '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="4" style="padding-top:0px;width: 100%;" >';
            echo '<center>';
            echo '<table class="filter-container" border="0" >';
            echo '<form action="" method="post">';
            echo '<td  style="text-align: right;">Show Details About : &nbsp;</td>';
            echo '<td width="30%">';
            echo '<select name="showonly" id="" class="box filter-container-items" style="width:90% ;height: 37px;margin: 0;" >';
            echo '<option value="" disabled selected hidden>'.$current.'</option><br/>';
            echo '<option value="my">My Customer Only</option><br/>';
            echo '<option value="all">All Customer</option><br/>';
            echo '</select>';
            echo '</td>';
            echo '<td width="12%">';
            echo '<input type="submit"  name="filter" value=" Filter" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</center>';
            echo '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="4">';
            echo '<center>';
            echo '<div class="abc scroll">';
            echo '<table width="93%" class="sub-table scrolldown"  style="border-spacing:0;">';
            echo '<thead>';
            echo '<tr>';
            echo '<th class="table-headin">Name</th>';
            echo '<th class="table-headin">Date of Birth</th>';
            echo '<th class="table-headin">Events</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            $result= $database->query($sqlmain);
            if($result->rowCount()==0){
                echo '<tr><td colspan="4"><br><br><br><br><center><img src="../img/notfound2.svg" width="25%"><br><p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p><a class="non-style-link" href="customer.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Customer &nbsp;</font></button></a></center><br><br><br><br></td></tr>';
            }else{
                while($row=$result->fetch(PDO::FETCH_ASSOC)){
                    $pid=$row["id"];
                    $name=$row["pname"];
                    $email=$row["pemail"];
                    $dob=$row["pdob"];
                    echo '<tr><td> &nbsp;'.substr($name,0,35).'</td><td>'.substr($email,0,20).'</td><td>'.substr($dob,0,10).'</td><td ><div style="display:flex;justify-content: center;"><a href="?action=view&id='.$pid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a></div></td></tr>';
                }
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
            echo '</center>';
            echo '</td>';
            echo '</tr>';
            echo '</table>';
            echo '</div>';
        ?>
    </div>
    <?php 
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        $sqlmain= "select * from customer where id='$id'";
        $result= $database->query($sqlmain);
        $row=$result->fetch(PDO::FETCH_ASSOC);
        $name=$row["pname"];
        $email=$row["pemail"];
        $dob=$row["pdob"];
        $address=$row["paddress"];
        echo '<div id="popup1" class="overlay"><div class="popup"><center><a class="close" href="customer.php">&times;</a><div class="content"></div><div style="display: flex;justify-content: center;"><table width="80%" class="sub-table scrolldown add-doc-form-container" border="0"><tr><td><p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br></td></tr><tr><td class="label-td" colspan="2"><label for="name" class="form-label">Customer ID: </label></td></tr><tr><td class="label-td" colspan="2">P-'.$id.'<br><br></td></tr><tr><td class="label-td" colspan="2"><label for="name" class="form-label">Name: </label></td></tr><tr><td class="label-td" colspan="2">'.$name.'<br><br></td></tr><tr><td class="label-td" colspan="2"><label for="Email" class="form-label">Email: </label></td></tr><tr><td class="label-td" colspan="2">'.$email.'<br><br></td></tr><tr><td class="label-td" colspan="2"><label for="spec" class="form-label">Address: </label></td></tr><tr><td class="label-td" colspan="2">'.$address.'<br><br></td></tr><tr><td class="label-td" colspan="2"><label for="name" class="form-label">Date of Birth: </label></td></tr><tr><td class="label-td" colspan="2">'.$dob.'<br><br></td></tr><tr><td colspan="2"><a href="customer.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a></td></tr></table></div></center><br><br></div></div>';
    }
    ?>
</body>
</html>