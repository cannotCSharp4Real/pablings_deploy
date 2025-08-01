<?php
// Move all PHP code to the top, before any HTML output
session_start();

if(isset($_SESSION["user"])){
    if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
        header("location: ../login.php");
        exit();
    }else{
        $useremail=$_SESSION["user"];
    }
}else{
    header("location: ../login.php");
    exit();
}

//import database
include("../connection.php");
$userrow = $database->query("select * from customer where pemail='$useremail'");
$userfetch=$userrow->fetch();
$userid= $userfetch["id"];
$username=$userfetch["pname"];

// Get the list of doctors for the datalist
$barberList = $database->query("select docname,docemail from barber;")->fetchAll();
$barberCount = count($barberList);

// Handle search functionality
if($_POST){
    $keyword=$_POST["search"];
    $sqlmain= "select * from barber where docemail='$keyword' or docname='$keyword' or docname like '$keyword%' or docname like '%$keyword' or docname like '%$keyword%'";
}else{
    $sqlmain= "select * from barber order by id desc";
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
                                    <p class="profile-title" style="white-space:normal;word-break:break-all;"><?php echo htmlspecialchars($username) ?></p>
                                    <p class="profile-subtitle" style="white-space:normal;word-break:break-all;"><?php echo htmlspecialchars($useremail) ?></p>
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
                        <a href="index.php" class="non-style-link-menu "><div><p class="menu-text">Home</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-active">
                        <a href="barber.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">All Barber</p></div></a>
                    </td>
                </tr>
                
                <tr class="menu-row" >
                    <td class="menu-btn">
                        <a href="schedule.php" class="non-style-link-menu"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
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
                    <td width="13%">
                        <a href="barber.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Barber name or Email" list="barber">&nbsp;&nbsp;
                            
                            <?php
                                echo '<datalist id="barber">';
                                // Replace $list11->data_seek(0); as PDO does not support data_seek
                                // Replace $list11->num_rows with a PDO-compatible row count
                                foreach ($barberList as $row00) {
                                    $d = $row00["docname"];
                                    $c = $row00["docemail"];
                                    echo "<option value='$d'><br/>";
                                    echo "<option value='$c'><br/>";
                                }
                                echo ' </datalist>';
                            ?>
                            
                            <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
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
               
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Barber (<?php echo $barberCount; ?>)</p>
                    </td>
                </tr>
                  
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                                <th class="table-headin">
                                    Barber Name
                                </th>
                                <th class="table-headin">
                                    Email
                                </th>
                                <th class="table-headin">
                                    Specialties
                                </th>
                                <th class="table-headin">
                                    Events
                                </th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                                // For $result->num_rows, fetch all rows first
                                $barberRows = $result->fetchAll();
                                if (count($barberRows) == 0) {
                                    echo '<tr>
                                    <td colspan="4">
                                    <br><br><br><br>
                                    <center>
                                    <img src="../img/notfound.svg" width="25%">
                                    
                                    <br>
                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We  couldnt find anything related to your keywords !</p>
                                    <a class="non-style-link" href="barber.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Barber &nbsp;</font></button>
                                    </a>
                                    </center>
                                    <br><br><br><br>
                                    </td>
                                    </tr>';
                                }
                                else{
                                foreach ($barberRows as $row) {
                                    $docid = $row["id"];
                                    $name = $row["docname"];
                                    $email = $row["docemail"];
                                    $spe = isset($row["specialties"]) ? $row["specialties"] : "";
                                    $spcil_name = "N/A";
                                    if ($spe !== "" && is_numeric($spe)) {
                                        $spcil_res = $database->query("select sname from specialties where id='$spe'");
                                        $spcil_array = $spcil_res->fetch();
                                        if ($spcil_array && isset($spcil_array["sname"])) {
                                            $spcil_name = $spcil_array["sname"];
                                        }
                                    }
                                    echo '<tr>
                                        <td> &nbsp;'.
                                        substr($name,0,30)
                                        .'</td>
                                        <td>
                                        '.substr($email,0,20).'
                                        </td>
                                        <td>
                                            '.substr($spcil_name,0,20).'
                                        </td>

                                        <td>
                                        <div style="display:flex;justify-content: center;">
                                        
                                        <a href="?action=view&id='.$docid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                       &nbsp;&nbsp;&nbsp;
                                       <a href="?action=session&id='.$docid.'&name='.$name.'"  class="non-style-link"><button  class="btn-primary-soft btn button-icon menu-icon-session-active"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Sessions</font></button></a>
                                        </div>
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
    // Handle view and session actions
    if(isset($_GET['action'])){
        $action = $_GET['action'];
        $id = $_GET['id'];
        
        if($action == 'view'){
            // Get barber details for view modal
            $sqlmain = "select * from barber where id=$id";
            $result = $database->query($sqlmain);
            $row = $result->fetch();
            $name = $row["docname"];
            $email = $row["docemail"];
            $spe = isset($row["specialties"]) ? $row["specialties"] : "";
            $spcil_name = "N/A";
            if ($spe !== "" && is_numeric($spe)) {
                $spcil_res = $database->query("select sname from specialties where id='$spe'");
                $spcil_array = $spcil_res->fetch();
                if ($spcil_array && isset($spcil_array["sname"])) {
                    $spcil_name = $spcil_array["sname"];
                }
            }
            ?>
            <!-- View Modal -->
            <div id="viewModal" class="modal" style="display: block; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
                <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; color: #333;">Pablings Barberhop</h2>
                        <span class="close" onclick="closeModal('viewModal')" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                    </div>
                    <h3 style="color: #333; margin-bottom: 20px;">View Details</h3>
                    <div style="margin-bottom: 15px;">
                        <strong>Name:</strong> <?php echo htmlspecialchars($name); ?>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <strong>Email:</strong> <?php echo htmlspecialchars($email); ?>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <strong>Specialties:</strong> <?php echo htmlspecialchars($spcil_name); ?>
                    </div>
                    <div style="text-align: center;">
                        <button onclick="closeModal('viewModal')" class="login-btn btn-primary btn" style="padding: 10px 30px;">OK</button>
                    </div>
                </div>
            </div>
            <?php
        }
        elseif($action == 'session'){
            $name = $_GET['name'];
            ?>
            <!-- Session Modal -->
            <div id="sessionModal" class="modal" style="display: block; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
                <div class="modal-content" style="background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 50%; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                        <h2 style="margin: 0; color: #333;">Pablings Barberhop</h2>
                        <span class="close" onclick="closeModal('sessionModal')" style="color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
                    </div>
                    <h3 style="color: #333; margin-bottom: 20px;">Redirect to Barber sessions?</h3>
                    <p style="margin-bottom: 20px;">You want to view All sessions by (<?php echo htmlspecialchars($name); ?>).</p>
                    <div style="text-align: center;">
                        <button onclick="redirectToSessions(<?php echo $id; ?>)" class="login-btn btn-primary btn" style="padding: 10px 30px;">Yes</button>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <script>
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            // Remove the action parameters from URL
            window.history.replaceState({}, document.title, window.location.pathname);
        }

        function redirectToSessions(barberId) {
            // Redirect to schedule.php without any parameters
            window.location.href = 'schedule.php';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modals = document.getElementsByClassName('modal');
            for(var i = 0; i < modals.length; i++) {
                if (event.target == modals[i]) {
                    modals[i].style.display = "none";
                    // Remove the action parameters from URL
                    window.history.replaceState({}, document.title, window.location.pathname);
                }
            }
        }
    </script>
</body>
</html>
  
