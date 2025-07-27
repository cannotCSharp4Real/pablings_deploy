<?php
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

include("../connection.php");
$userrow = $database->query("select * from customer where pemail='$useremail'");
$userfetch=$userrow->fetch(PDO::FETCH_ASSOC);
$userid= $userfetch["id"];
$username=$userfetch["pname"];

// Handle booking form submission
if(isset($_POST["booknow"])){
    $scheduleid = $_POST["scheduleid"];
    $apponum = $_POST["apponum"];
    $date = $_POST["date"];
    $stmt = $database->prepare("INSERT INTO appointment (pid, scheduleid, apponum, appodate) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userid, $scheduleid, $apponum, $date]);
    header("Location: appointment.php?action=booking-added&id=$apponum");
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Get session details
$sessionData = null;
$apponum = 0;
if(isset($_GET["id"])){
    $id = $_GET["id"];
    $sqlmain = "select * from schedule inner join barber on schedule.docid=barber.id where schedule.scheduleid=$id";
    $result = $database->query($sqlmain);
    
    if($result->rowCount() > 0) {
        $sessionData = $result->fetch(PDO::FETCH_ASSOC);
        $sql2 = "select * from appointment where scheduleid=$id";
        $result12 = $database->query($sql2);
        $apponum = $result12->rowCount() + 1;
    }
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
    <title>Booking</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f7f7f7 !important;
        }
        .container {
            display: flex !important;
            min-height: 100vh;
        }
        .menu {
            width: 240px;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            min-height: 100vh;
            position: sticky;
            top: 0;
        }
        .dash-body {
            flex: 1 !important;
            padding: 32px 24px;
            background: #f7f7f7 !important;
            min-width: 0;
        }
        .booking-container {
            display: flex !important;
            gap: 24px;
            margin-top: 24px;
            width: 100%;
        }
        .session-details {
            flex: 2 !important;
            background: #fff !important;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid #e0e0e0;
        }
        .appointment-number {
            flex: 1 !important;
            background: #fff !important;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        .appointment-number-display {
            font-size: 70px !important;
            font-weight: 800 !important;
            color: #1e88e5 !important;
            background-color: #e3f2fd !important;
            border-radius: 8px;
            padding: 20px;
            margin: 16px 0;
            display: block !important;
        }
        .book-now-btn {
            width: 100% !important;
            margin-top: 16px;
        }
        .logout-btn {
            width: 90%;
            margin: 20px 5%;
        }
        .session-details h2 {
            font-size: 25px !important;
            color: #1e88e5 !important;
            margin-bottom: 24px !important;
        }
        .appointment-number h3 {
            font-size: 20px !important;
            color: #1e88e5 !important;
            margin-bottom: 16px !important;
        }
        .session-details p {
            font-size: 18px !important;
            line-height: 30px !important;
            margin: 8px 0 !important;
        }
        @media (max-width: 900px) {
            .container {
                flex-direction: column !important;
            }
            .menu {
                width: 100%;
                min-height: unset;
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }
            .booking-container {
                flex-direction: column !important;
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
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo htmlspecialchars($username) ?></p>
                                    <p class="profile-subtitle"><?php echo htmlspecialchars($useremail) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-barber">
                        <a href="barber.php" class="non-style-link-menu"><div><p class="menu-text">All Barber</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php"><button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="schedule.php" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Barber name or Email or Date (YYYY-MM-DD)" list="barber">&nbsp;&nbsp;
                            <?php
                                echo '<datalist id="barber">';
                                $list11 = $database->query("select DISTINCT * from barber;");
                                $list12 = $database->query("select DISTINCT * from schedule GROUP BY title;");
                                foreach($list11 as $row00){
                                    $d=$row00["docname"];
                                    echo "<option value='$d'><br/>";
                                }
                                foreach($list12 as $row00){
                                    $d=$row00["title"];
                                    echo "<option value='$d'><br/>";
                                }
                                echo '</datalist>';
                            ?>
                            <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;"><?php echo $today; ?></p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="4">
                        <!-- Debug: sessionData exists: <?php echo $sessionData ? 'YES' : 'NO'; ?> -->
                        <!-- Debug: GET id: <?php echo isset($_GET['id']) ? $_GET['id'] : 'NOT SET'; ?> -->
                        <?php if($sessionData): ?>
                        <!-- Debug: Inside sessionData condition -->
                        <div class="booking-container">
                            <div class="session-details">
                                <h2 style="font-size: 25px; color: #1e88e5; margin-bottom: 24px;">Session Details</h2>
                                <div style="font-size: 18px; line-height: 30px;">
                                    <p><strong>Barber name:</strong> <?php echo htmlspecialchars($sessionData['docname']); ?></p>
                                    <p><strong>Barber Email:</strong> <?php echo htmlspecialchars($sessionData['docemail']); ?></p>
                                    <p><strong>Session Title:</strong> <?php echo htmlspecialchars($sessionData['title']); ?></p>
                                    <p><strong>Session Scheduled Date:</strong> <?php echo htmlspecialchars($sessionData['scheduledate']); ?></p>
                                    <p><strong>Session Starts:</strong> <?php echo htmlspecialchars($sessionData['scheduletime']); ?></p>
                                    <p><strong>Channeling fee:</strong> <b>LKR.2 000.00</b></p>
                                </div>
                            </div>
                            
                            <div class="appointment-number">
                                <h3 style="font-size: 20px; color: #1e88e5; margin-bottom: 16px;">Your Appointment Number</h3>
                                <div class="appointment-number-display">
                                    <?php echo $apponum; ?>
                                </div>
                                
                                <form action="booking.php" method="post">
                                    <input type="hidden" name="scheduleid" value="<?php echo $sessionData['scheduleid']; ?>">
                                    <input type="hidden" name="apponum" value="<?php echo $apponum; ?>">
                                    <input type="hidden" name="date" value="<?php echo $today; ?>">
                                    <input type="submit" class="login-btn btn-primary btn book-now-btn" value="Book now" name="booknow">
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Debug: Inside else condition -->
                        <div style="text-align: center; padding: 40px 0;">
                            <p style="font-size: 18px; color: #666;">Session not found or invalid ID.</p>
                            <a href="schedule.php" class="non-style-link">
                                <button class="login-btn btn-primary-soft btn" style="margin-top: 16px;">Back to Sessions</button>
                            </a>
                        </div>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>