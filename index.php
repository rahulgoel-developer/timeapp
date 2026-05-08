<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Tracker Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: sans-serif; margin: 40px; line-height: 1.6; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .form-group { margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .success-msg { color: green; font-weight: bold; }
        
        .activity-section { margin-bottom: 20px; }
        .activity-section h4 { margin-bottom: 10px; color: #333; }
        .button-group { display: flex; flex-wrap: wrap; gap: 10px; }
        .activity-div { 
            background-color: #007bff; 
            color: white; 
            padding: 12px 15px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 14px;
            transition: all 0.2s;
            min-width: 150px;
            text-align: center;
            border: 2px solid transparent;
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
    </style>
</head>
<body>

    <h2>Log Activity</h2>
    <div class="form-group">
        <label>Select Completed Activity:</label>
        
        <div class="activity-section">
            <h4>Daily Activities</h4>
            <div class="button-group">
                <?php
                $daily = $conn->query("SELECT id, activity_name FROM daily_activities");
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
                    
                    echo "<div class='activity-div' onclick='logActivity(\"daily_{$row['id']}\")'>
                            <div class='activity-name'>{$row['activity_name']}</div>
                            <div class='activity-time'>$time_str</div>
                          </div>";
                }
                ?>
            </div>
        </div>
        
        <div class="activity-section">
            <h4>Office Work</h4>
            <div class="button-group">
                <?php
                $office = $conn->query("SELECT id, activity_name FROM office_work_to_do WHERE status != 'completed'");
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
                    
                    echo "<div class='activity-div' onclick='logActivity(\"office_{$row['id']}\")'>
                            <div class='activity-name'>{$row['activity_name']}</div>
                            <div class='activity-time'>$time_str</div>
                          </div>";
                }
                ?>
            </div>
        </div>
        
        <div id="response"></div>
    </div>

    <hr>

    <h3>Daily Schedule (Since <?php echo APP_START_DATE; ?>)</h3>
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
    </script>
</body>
</html>