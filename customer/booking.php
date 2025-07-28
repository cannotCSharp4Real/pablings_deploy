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
    $time = $_POST["time"]; // Get the time from the form
    
    try {
        // Check if appointment number already exists
        $check_stmt = $database->prepare("SELECT COUNT(*) FROM appointment WHERE scheduleid = ? AND apponum = ?");
        $check_stmt->execute([$scheduleid, $apponum]);
        $exists = $check_stmt->fetchColumn();
        
        if($exists > 0) {
            // Find the next available appointment number
            $next_stmt = $database->prepare("SELECT MAX(apponum) FROM appointment WHERE scheduleid = ?");
            $next_stmt->execute([$scheduleid]);
            $max_apponum = $next_stmt->fetchColumn();
            $apponum = $max_apponum + 1;
        }
        
        $stmt = $database->prepare("INSERT INTO appointment (pid, scheduleid, apponum, appodate, appotime) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userid, $scheduleid, $apponum, $date, $time]);
        header("Location: appointment.php?action=booking-added&id=$apponum");
        exit();
    } catch (PDOException $e) {
        // Handle the error gracefully
        error_log("Booking error: " . $e->getMessage());
        $error_message = "Booking failed. Please try again.";
    }
}

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Get session details
$sessionData = null;
$apponum = 0;
if(isset($_GET["id"])){
    $id = $_GET["id"];
    try {
    $sqlmain = "select * from schedule inner join barber on schedule.docid=barber.id where schedule.scheduleid=$id";
    $result = $database->query($sqlmain);
    
    if($result->rowCount() > 0) {
        $sessionData = $result->fetch(PDO::FETCH_ASSOC);
        $sql2 = "select * from appointment where scheduleid=$id";
        $result12 = $database->query($sql2);
        $apponum = $result12->rowCount() + 1;
        
        // Check if this appointment number already exists for this schedule
        $check_sql = "select * from appointment where scheduleid=$id and apponum=$apponum";
        $check_result = $database->query($check_sql);
        while($check_result->rowCount() > 0) {
            $apponum++;
            $check_sql = "select * from appointment where scheduleid=$id and apponum=$apponum";
            $check_result = $database->query($check_sql);
        }
        }
    } catch (Exception $e) {
        error_log("Database error in booking.php: " . $e->getMessage());
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
        
        .booking-container {
            display: flex;
            gap: 24px;
            margin-top: 24px;
        }
        
        .session-details {
            flex: 2;
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid #e0e0e0;
        }
        
        .appointment-number {
            flex: 1;
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            text-align: center;
            border: 1px solid #e0e0e0;
        }
        
        .session-details h2 {
            font-size: 25px;
            color: #1e88e5;
            margin-bottom: 24px;
            font-weight: 600;
        }
        
        .session-details p {
            font-size: 18px;
            line-height: 30px;
            margin: 8px 0;
        }
        
        .session-details strong {
            font-weight: 600;
            color: #333;
        }
        
        .appointment-number h3 {
            font-size: 20px;
            color: #1e88e5;
            margin-bottom: 16px;
            font-weight: 600;
        }
        
        .appointment-number-display {
            font-size: 70px;
            font-weight: 800;
            color: #1e88e5;
            background-color: #e3f2fd;
            border-radius: 8px;
            padding: 20px;
            margin: 16px 0;
            display: block;
        }
        
        .btn-primary {
            background-color: #0A76D8;
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #006dd3;
        }
        
        .book-now-btn {
            width: 100%;
            margin-top: 16px;
        }
        
        .back-btn {
            background-color: #127edc;
            color: #fff;
            margin-bottom: 20px;
        }
        
        .back-btn:hover {
            background-color: #0A76D8;
        }
        
        .error-message {
            text-align: center;
            padding: 40px 0;
            color: #666;
            font-size: 18px;
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
            .booking-container {
                flex-direction: column;
            }
        }
        @media (max-width: 600px) {
            .heading-main12 {
                font-size: 18px;
            }
            .dash-body {
                padding: 8px 2px;
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
                    <td class="menu-btn menu-icon-home">
                        <a href="index.php" class="non-style-link-menu"><div><p class="menu-text">Home</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-barber">
                        <a href="barber.php" class="non-style-link-menu"><div><p class="menu-text">All Barber</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Scheduled Sessions</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu"><div><p class="menu-text">My Bookings</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu"><div><p class="menu-text">Settings</p></div></a>
                    </td>
                </tr>
            </table>
        </div>
        
        <div class="dash-body">
                        <?php if(isset($error_message)): ?>
                            <div style="background: #ffebee; color: #c62828; padding: 16px; border-radius: 8px; margin-bottom: 16px; text-align: center;">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>
                        <?php if($sessionData): ?>
                        <div class="booking-container">
                            <div class="session-details">
                    <h2>Session Details</h2>
                    <div>
                                    <p><strong>Barber name:</strong> <?php echo htmlspecialchars($sessionData['docname']); ?></p>
                                    <p><strong>Barber Email:</strong> <?php echo htmlspecialchars($sessionData['docemail']); ?></p>
                                    <p><strong>Session Title:</strong> <?php echo htmlspecialchars($sessionData['title']); ?></p>
                                    <p><strong>Session Scheduled Date:</strong> <?php echo htmlspecialchars($sessionData['scheduledate']); ?></p>
                                    <p><strong>Session Starts:</strong> <?php echo htmlspecialchars($sessionData['scheduletime']); ?></p>
                                    <p><strong>Channeling fee:</strong> <b>LKR.2 000.00</b></p>
                                </div>
                            </div>
                            
                            <div class="appointment-number">
                    <h3>Your Appointment Number</h3>
                                <div class="appointment-number-display">
                                    <?php echo $apponum; ?>
                                </div>
                                
                                <form action="booking.php" method="post">
                                    <input type="hidden" name="scheduleid" value="<?php echo $sessionData['scheduleid']; ?>">
                                    <input type="hidden" name="apponum" value="<?php echo $apponum; ?>">
                                    <input type="hidden" name="date" value="<?php echo $today; ?>">
                        <input type="hidden" name="time" value="<?php echo $sessionData['scheduletime']; ?>">
                        <input type="submit" class="btn btn-primary book-now-btn" value="Book now" name="booknow">
                                </form>
                            </div>
                        </div>
                        <?php else: ?>
            <div class="error-message">
                <p>
                    <?php if(isset($_GET["id"])): ?>
                        Session not found or invalid ID (ID: <?php echo htmlspecialchars($_GET["id"]); ?>).
                    <?php else: ?>
                        No session ID provided.
                    <?php endif; ?>
                </p>
                <br>
                <a href="schedule.php" class="btn btn-primary">Back to Sessions</a>
                        </div>
                        <?php endif; ?>
        </div>
    </div>
</body>
</html>