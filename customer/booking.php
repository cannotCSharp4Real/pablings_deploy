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
    try {
        $sqlmain = "select * from schedule inner join barber on schedule.docid=barber.id where schedule.scheduleid=$id";
        $result = $database->query($sqlmain);
        
        if($result->rowCount() > 0) {
            $sessionData = $result->fetch(PDO::FETCH_ASSOC);
            $sql2 = "select * from appointment where scheduleid=$id";
            $result12 = $database->query($sql2);
            $apponum = $result12->rowCount() + 1;
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
    <title>Booking</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f7f7f7;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        .menu {
            width: 240px;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            padding: 20px;
        }
        
        .dash-body {
            flex: 1;
            padding: 32px 24px;
            background: #f7f7f7;
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
        
        .btn {
            cursor: pointer;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
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
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
            }
            .booking-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="menu">
            <h3 style="margin-bottom: 20px;">Welcome, <?php echo htmlspecialchars($username); ?></h3>
            <p style="margin-bottom: 20px; color: #666;"><?php echo htmlspecialchars($useremail); ?></p>
            <a href="../logout.php" class="btn back-btn">Log out</a>
            <br><br>
            <a href="index.php" class="btn back-btn">Home</a>
            <br><br>
            <a href="schedule.php" class="btn back-btn">Back to Sessions</a>
        </div>
        
        <div class="dash-body">
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