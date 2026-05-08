<?php 
include 'config.php'; 

// Check if user is authenticated
if (!isset($_COOKIE['timeapp_auth']) || $_COOKIE['timeapp_auth'] !== 'authenticated') {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Tracker Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { 
            font-family: sans-serif; 
            margin: 20px; 
            line-height: 1.6; 
            min-height: 100vh;
            padding-bottom: 50px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            margin-bottom: 30px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 12px; 
            text-align: left; 
            word-wrap: break-word;
        }
        th { background-color: #f4f4f4; }
        .form-group { 
            margin-bottom: 20px; 
            background: #f9f9f9; 
            padding: 15px; 
            border-radius: 5px; 
        }
        .success-msg { color: green; font-weight: bold; }
        
        .activity-section { margin-bottom: 20px; }
        .activity-section h4 { margin-bottom: 10px; color: #333; }
        .button-group { display: flex; flex-wrap: wrap; gap: 10px; }
        .activity-div { 
            background-color: #007bff; 
            color: white; 
            padding: 20px 18px; 
            border-radius: 8px; 
            cursor: pointer; 
            font-size: 16px;
            transition: all 0.2s;
            min-width: 200px;
            min-height: 120px;
            text-align: center;
            border: 2px solid transparent;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .activity-div:hover { 
            background-color: #0056b3; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .activity-div:active {
            background-color: #004085;
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .activity-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .activity-time {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .schedule-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 20px;
            background: white;
        }
        
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s;
        }
        
        .schedule-header:hover {
            background: #e9ecef;
        }
        
        .schedule-header h3 {
            margin: 0;
            color: #333;
        }
        
        .toggle-icon {
            font-size: 18px;
            color: #666;
            transition: transform 0.3s ease;
        }
        
        .schedule-content {
            overflow: hidden;
            transition: max-height 0.3s ease;
            max-height: 1000px;
        }
        
        .schedule-content.collapsed {
            max-height: 0;
        }
        
        .toggle-icon.collapsed {
            transform: rotate(-90deg);
        }
        
        .progress-container {
            width: 100%;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            margin: 8px 0;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .progress-bar.minimal {
            background-color: #dc3545;
        }
        
        .progress-bar.started {
            background-color: #fd7e14;
        }
        
        .progress-bar.halfway {
            background-color: #ffc107;
        }
        
        .progress-bar.almost-complete {
            background-color: #20c997;
        }
        
        .progress-bar.complete {
            background-color: #28a745;
        }
        
        .progress-text {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }
        
        .activity-div {
            padding-bottom: 15px;
        }
        
        /* Mobile Responsive Design */
        @media (max-width: 768px) {
            body {
                margin: 10px;
                padding-bottom: 30px;
            }
            
            .button-group {
                flex-direction: column;
                gap: 15px;
            }
            
            .activity-div {
                min-width: 100%;
                min-height: 100px;
                padding: 15px;
                font-size: 15px;
            }
            
            .activity-name {
                font-size: 16px;
                margin-bottom: 8px;
            }
            
            .activity-time {
                font-size: 13px;
                margin-bottom: 8px;
            }
            
            .progress-text {
                font-size: 12px;
            }
            
            .form-group {
                padding: 10px;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px;
            }
            
            .schedule-header {
                padding: 10px;
            }
            
            .schedule-header h3 {
                font-size: 18px;
            }
        }
        
        @media (max-width: 480px) {
            body {
                margin: 5px;
            }
            
            .activity-div {
                min-height: 90px;
                padding: 12px;
                font-size: 14px;
            }
            
            .activity-name {
                font-size: 15px;
            }
            
            .activity-time {
                font-size: 12px;
            }
            
            .progress-text {
                font-size: 11px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 6px;
            }
        }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Log Activity</h2>
        <button onclick="logout()" style="background: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer;">Logout</button>
    </div>
    <div class="form-group">
        <label>Select Completed Activity:</label>
        
        <div class="activity-section">
            <h4>Daily Activities</h4>
            <div class="button-group">
                <?php
                $daily = $conn->query("SELECT id, activity_name, duration_minutes FROM daily_activities");
                while($row = $daily->fetch_assoc()) {
                    // Calculate today's time investment for this activity
                    $today = date('Y-m-d');
                    $time_sql = "SELECT SUM(duration_in_seconds) as today_sec FROM activity_log 
                                 WHERE activity_id = {$row['id']} 
                                 AND DATE(started_at) = '$today'";
                    $time_res = $conn->query($time_sql);
                    $time_data = $time_res->fetch_assoc();
                    $today_seconds = $time_data['today_sec'] ?? 0;
                    $today_hours = floor($today_seconds / 3600);
                    $today_minutes = floor(($today_seconds % 3600) / 60);
                    $time_str = ($today_hours > 0 || $today_minutes > 0) ? "Today: {$today_hours}h {$today_minutes}m" : "Today: 0h 0m";
                    
                    // Calculate completion percentage
                    $target_seconds = $row['duration_minutes'] * 60;
                    $completion_percentage = min(100, round(($today_seconds / $target_seconds) * 100, 1));
                    
                    // Determine progress bar color based on completion
                    if ($completion_percentage >= 100) {
                        $progress_color = 'complete';
                    } elseif ($completion_percentage >= 75) {
                        $progress_color = 'almost-complete';
                    } elseif ($completion_percentage >= 50) {
                        $progress_color = 'halfway';
                    } elseif ($completion_percentage >= 25) {
                        $progress_color = 'started';
                    } else {
                        $progress_color = 'minimal';
                    }
                    
                    echo "<div class='activity-div' onclick='logActivity(\"daily_{$row['id']}\")'>
                            <div class='activity-name'>{$row['activity_name']}</div>
                            <div class='activity-time'>$time_str</div>
                            <div class='progress-container'>
                                <div class='progress-bar {$progress_color}' style='width: {$completion_percentage}%'></div>
                            </div>
                            <div class='progress-text'>{$completion_percentage}% of {$row['duration_minutes']}min target</div>
                          </div>";
                }
                ?>
            </div>
        </div>
        
        <div class="activity-section">
            <h4>Office Work</h4>
            <div class="button-group">
                <?php
                $office = $conn->query("SELECT id, activity_name, duration_minutes FROM office_work_to_do WHERE status != 'completed'");
                while($row = $office->fetch_assoc()) {
                    // Calculate today's time investment for this office work
                    $today = date('Y-m-d');
                    $time_sql = "SELECT SUM(duration_in_seconds) as today_sec FROM activity_log 
                                 WHERE office_work_id = {$row['id']} 
                                 AND DATE(started_at) = '$today'";
                    $time_res = $conn->query($time_sql);
                    $time_data = $time_res->fetch_assoc();
                    $today_seconds = $time_data['today_sec'] ?? 0;
                    $today_hours = floor($today_seconds / 3600);
                    $today_minutes = floor(($today_seconds % 3600) / 60);
                    $time_str = ($today_hours > 0 || $today_minutes > 0) ? "Today: {$today_hours}h {$today_minutes}m" : "Today: 0h 0m";
                    
                    // Calculate completion percentage
                    $target_seconds = $row['duration_minutes'] * 60;
                    $completion_percentage = min(100, round(($today_seconds / $target_seconds) * 100, 1));
                    
                    // Determine progress bar color based on completion
                    if ($completion_percentage >= 100) {
                        $progress_color = 'complete';
                    } elseif ($completion_percentage >= 75) {
                        $progress_color = 'almost-complete';
                    } elseif ($completion_percentage >= 50) {
                        $progress_color = 'halfway';
                    } elseif ($completion_percentage >= 25) {
                        $progress_color = 'started';
                    } else {
                        $progress_color = 'minimal';
                    }
                    
                    echo "<div class='activity-div' onclick='logActivity(\"office_{$row['id']}\")'>
                            <div class='activity-name'>{$row['activity_name']}</div>
                            <div class='activity-time'>$time_str</div>
                            <div class='progress-container'>
                                <div class='progress-bar {$progress_color}' style='width: {$completion_percentage}%'></div>
                            </div>
                            <div class='progress-text'>{$completion_percentage}% of {$row['duration_minutes']}min target</div>
                          </div>";
                }
                ?>
            </div>
        </div>
        
        <div id="response"></div>
    </div>

    <hr>

    <div class="schedule-container">
        <div class="schedule-header" onclick="toggleSchedule()">
            <h3>Daily Schedule (Since <?php echo APP_START_DATE; ?>)</h3>
            <span class="toggle-icon" id="scheduleToggle">▼</span>
        </div>
        <div class="schedule-content" id="scheduleContent">
            <table>
        <thead>
            <tr>
                <th>Time Range (Duration)</th>
                <th>Activity Name</th>
                <th>Total Invested Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch schedule
            $query = "SELECT s.*, d.activity_name 
                      FROM schedule s 
                      LEFT JOIN daily_activities d ON s.activity_id = d.id 
                      ORDER BY s.start_time ASC";
            $result = mysqli_query($conn, $query);
    
            while ($row = mysqli_fetch_assoc($result)) {
                // 1. Calculate the duration of the scheduled slot
                $start = new DateTime($row['start_time']);
                $end = new DateTime($row['end_time']);
                $interval = $start->diff($end);
                $slot_dur = $interval->format('%h hours %i minutes');
    
                // 2. Calculate Total Invested Time for this activity
                $invested_str = "0 hours 0 minutes";
                if ($row['activity_id']) {
                    $act_id = $row['activity_id'];
                    
                    // Sum seconds from activity_log starting from our hardcoded date
                    $log_sql = "SELECT SUM(duration_in_seconds) as total_sec FROM activity_log 
                                WHERE activity_id = $act_id 
                                AND started_at >= '" . APP_START_DATE . " 00:00:00'";
                    
                    $log_res = mysqli_query($conn, $log_sql);
                    $log_data = mysqli_fetch_assoc($log_res);
                    $seconds = $log_data['total_sec'] ?? 0;
                    
                    $h = floor($seconds / 3600);
                    $m = floor(($seconds % 3600) / 60);
                    $invested_str = "{$h} hours {$m} minutes";
                }
    
                echo "<tr>
                        <td>{$row['start_time']} to {$row['end_time']} ($slot_dur)</td>
                        <td>" . ($row['activity_name'] ?? $row['detail']) . "</td>
                        <td><strong>$invested_str</strong></td>
                      </tr>";
            }
            ?>
        </tbody>
            </table>
        </div>
    </div>

    <script>
    function logActivity(activityValue) {
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: { action: 'log_activity', data: activityValue },
            success: function(res) {
                $('#response').html('<p class="success-msg">' + res + '</p>');
                setTimeout(() => { location.reload(); }, 1500);
            }
        });
    }
    
    function toggleSchedule() {
        const content = document.getElementById('scheduleContent');
        const toggle = document.getElementById('scheduleToggle');
        
        content.classList.toggle('collapsed');
        toggle.classList.toggle('collapsed');
    }
    
    function logout() {
        // Clear the authentication cookie
        document.cookie = 'timeapp_auth=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        
        // Redirect to login page
        window.location.href = 'login.php';
    }
    </script>
</body>
</html>