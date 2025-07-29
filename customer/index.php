<?php
// Start session before any HTML output
session_start();

// Import database connection
include("../connection.php");

// Check if user is logged in and is a customer
if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" or $_SESSION['usertype'] != 'p') {
        header("location: ../login.php");
        exit();
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
    exit();
}

// Get user information using PDO
try {
    $stmt = $pdo->prepare("SELECT * FROM customer WHERE pemail = ?");
    $stmt->execute([$useremail]);
    $userfetch = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($userfetch) {
        $userid = $userfetch["id"]; // Using 'id' as per create_tables.php
        $username = $userfetch["pname"];
    } else {
        // If user not found, redirect to login
        header("location: ../login.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("Database error occurred. Please try again later.");
}

// Set timezone and get today's date
date_default_timezone_set('Asia/Manila');
$today = date('Y-m-d');

// Get statistics using PDO
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM customer");
    $stmt->execute();
    $customer_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM barber");
    $stmt->execute();
    $barber_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    // Note: These tables might not exist yet, so we'll handle gracefully
    $appointment_count = 0;
    $schedule_count = 0;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM appointment WHERE appodate >= ?");
        $stmt->execute([$today]);
        $appointment_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (PDOException $e) {
        // Table might not exist, default to 0
        $appointment_count = 0;
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM schedule WHERE scheduledate = ?");
        $stmt->execute([$today]);
        $schedule_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (PDOException $e) {
        // Table might not exist, default to 0
        $schedule_count = 0;
    }

} catch (PDOException $e) {
    // Set default values if queries fail
    $customer_count = 0;
    $barber_count = 0;
    $appointment_count = 0;
    $schedule_count = 0;
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
    <title>Dashboard</title>
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
                    <td class="menu-btn menu-active">
                        <a href="index.php" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Home</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn">
                        <a href="barber.php" class="non-style-link-menu"><div><p class="menu-text">All Barber</p></div></a>
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
        <div class="dash-body" style="margin-top: 15px">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;" >
                <tr>
                    <td colspan="1" class="nav-bar" >
                        <p style="font-size: 23px;font-weight: 600;margin:0;">Home</p>
                    </td>
                    <td width="25%"></td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" >
                        <center>
                            <table class="filter-container barber-header customer-header" style="border: none;width:95%;margin:0;" border="0" >
                                <tr>
                                    <td style="padding:0;">
                                        <h3 style="margin:0 0 8px 0;">Welcome!</h3>
                                        <h1 style="margin:0 0 8px 0;"><?php echo htmlspecialchars($username)  ?>.</h1>
                                        <p style="margin:0 0 12px 0;">Haven't had any idea about barber? No problem, let's jump to 
                                            <a href="barber.php" class="non-style-link"><b>"All Barber"</b></a> section or 
                                            <a href="schedule.php" class="non-style-link"><b>"Sessions"</b> </a><br>
                                            Track your past and future appointments history.<br>Also find out the expected arrival time of your barber..
                                        </p>
                                        <h3 style="margin:16px 0 8px 0;">Channel a Barber Here</h3>
                                        <form action="schedule.php" method="post" style="display: flex;gap:8px;margin:0;">
                                            <input type="search" name="search" class="input-text " placeholder="Search barber and we will find the session available" list="barber" style="width:45%;">
                                            <?php
                                            echo '<datalist id="barber">';
                                            try {
                                                $stmt = $pdo->prepare("SELECT docname, docemail FROM barber");
                                                $stmt->execute();
                                                $barbers = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                foreach ($barbers as $barber) {
                                                    $d = htmlspecialchars($barber["docname"]);
                                                    echo "<option value='$d'><br/>";
                                                }
                                            } catch (PDOException $e) {
                                                // If barber table doesn't exist or query fails, just show empty datalist
                                            }
                                            echo '</datalist>';
                                            ?>
                                            <input type="Submit" value="Search" class="login-btn btn-primary btn">
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <table border="0" width="100%">
                            <tr>
                                <td width="50%">
                                    <center>
                                        <table class="filter-container" style="border: none;" border="0">
                                            <tr>
                                                <td colspan="4">
                                                    <p style="font-size: 20px;font-weight:600;padding-left: 12px;">Status</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $barber_count; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                All Barber &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $customer_count; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard">
                                                                All Customer &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex; ">
                                                        <div>
                                                            <div class="h1-dashboard" >
                                                                <?php echo $appointment_count; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" >
                                                                New Booking &nbsp;&nbsp;
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items"  style="padding:20px;margin:auto;width:95%;display: flex;padding-top:21px;padding-bottom:21px;">
                                                        <div>
                                                            <div class="h1-dashboard">
                                                                <?php echo $schedule_count; ?>
                                                            </div><br>
                                                            <div class="h3-dashboard" style="font-size: 15px">
                                                                Today Sessions
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </center>
                                </td>
                                <td>
                                    <p style="font-size: 20px;font-weight:600;padding-left: 40px;" class="anime">Your Upcoming Booking</p>
                                    <center>
                                        <div class="abc scroll" style="height: 250px;padding: 0;margin: 0;">
                                            <table width="85%" class="sub-table scrolldown" border="0" >
                                                <thead>
                                                    <tr>
                                                        <th class="table-headin">Appoint. Number</th>
                                                        <th class="table-headin">Session Title</th>
                                                        <th class="table-headin">Barber</th>
                                                        <th class="table-headin">Scheduled Date & Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    try {
                                                        // This query joins multiple tables that might not exist yet
                                                        $stmt = $pdo->prepare("
                                                            SELECT s.scheduleid, s.title, s.scheduledate, s.scheduletime, 
                                                                   a.apponum, b.docname 
                                                            FROM schedule s 
                                                            INNER JOIN appointment a ON s.scheduleid = a.scheduleid 
                                                            INNER JOIN customer c ON c.id = a.pid 
                                                            INNER JOIN barber b ON s.docid = b.id  
                                                            WHERE c.id = ? AND s.scheduledate >= ? 
                                                            ORDER BY s.scheduledate ASC
                                                        ");
                                                        $stmt->execute([$userid, $today]);
                                                        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                        
                                                        if (empty($appointments)) {
                                                            echo '<tr>
                                                                <td colspan="4">
                                                                    <br><br><br><br>
                                                                    <center>
                                                                        <img src="../img/notfound2.svg" width="30%"> 
                                                                        <br>
                                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nothing to show here!</p>
                                                                        <a class="non-style-link" href="schedule.php">
                                                                            <button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">
                                                                                &nbsp; Channel a Barber &nbsp;
                                                                            </button>
                                                                        </a>
                                                                    </center>
                                                                    <br><br><br><br>
                                                                </td>
                                                            </tr>';
                                                        } else {
                                                            foreach ($appointments as $appointment) {
                                                                echo '<tr>
                                                                    <td style="padding:30px;font-size:25px;font-weight:700;"> &nbsp;' .
                                                                    htmlspecialchars($appointment['apponum']) .
                                                                    '</td>
                                                                    <td style="padding:20px;"> &nbsp;' .
                                                                    htmlspecialchars(substr($appointment['title'], 0, 30)) .
                                                                    '</td>
                                                                    <td>' .
                                                                    htmlspecialchars(substr($appointment['docname'], 0, 20)) .
                                                                    '</td>
                                                                    <td style="text-align:center;">' .
                                                                    htmlspecialchars(substr($appointment['scheduledate'], 0, 10)) . ' ' . 
                                                                    htmlspecialchars(substr($appointment['scheduletime'], 0, 5)) .
                                                                    '</td>
                                                                </tr>';
                                                            }
                                                        }
                                                    } catch (PDOException $e) {
                                                        // If tables don't exist, show the no appointments message
                                                        echo '<tr>
                                                            <td colspan="4">
                                                                <br><br><br><br>
                                                                <center>
                                                                    <img src="../img/notfound2.svg" width="30%"> 
                                                                    <br>
                                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Database tables not ready!</p>
                                                                    <p>Please contact administrator to set up the system.</p>
                                                                </center>
                                                                <br><br><br><br>
                                                            </td>
                                                        </tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </center>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
