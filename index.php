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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Time Tracker Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary-color: #22d3ee;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-bg: #0f172a;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--text-primary);
            line-height: 1.6;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(34, 211, 238, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(168, 85, 247, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 2;
        }
        
        /* Premium Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        
        .header .subtitle {
            color: var(--text-secondary);
            font-size: 14px;
            margin-top: 0.25rem;
        }
        
        .logout-btn {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-lg);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
            font-size: 14px;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        /* Premium Cards */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-header {
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .card-description {
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        /* Activity Grid */
        .activity-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 1.5rem;
        }
        
        .activity-card {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .activity-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
            pointer-events: none;
        }
        
        .activity-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: var(--shadow-xl);
        }
        
        .activity-card:active {
            transform: translateY(-2px) scale(1.01);
        }
        
        .activity-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .activity-time {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }
        
        /* Premium Progress Bars */
        .progress-container {
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-sm);
            height: 8px;
            overflow: hidden;
            margin-bottom: 0.5rem;
            position: relative;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: var(--radius-sm);
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .progress-bar.minimal { background: linear-gradient(90deg, var(--danger-color), #dc2626); }
        .progress-bar.started { background: linear-gradient(90deg, var(--warning-color), #d97706); }
        .progress-bar.halfway { background: linear-gradient(90deg, #fbbf24, #f59e0b); }
        .progress-bar.almost-complete { background: linear-gradient(90deg, var(--secondary-color), #06b6d4); }
        .progress-bar.complete { background: linear-gradient(90deg, var(--success-color), #059669); }
        
        .progress-text {
            font-size: 11px;
            opacity: 0.9;
            font-weight: 500;
        }
        
        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }
        
        .section-icon {
            width: 24px;
            height: 24px;
            margin-right: 0.75rem;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }
        
        /* Premium Table */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .schedule-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .schedule-header:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }
        
        .schedule-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .toggle-icon {
            font-size: 20px;
            transition: transform 0.3s ease;
        }
        
        .toggle-icon.collapsed {
            transform: rotate(-90deg);
        }
        
        .schedule-content {
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 2000px;
            overflow: hidden;
        }
        
        .schedule-content.collapsed {
            max-height: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        th {
            background: var(--light-bg);
            font-weight: 600;
            color: var(--text-primary);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        td {
            color: var(--text-secondary);
            font-size: 13px;
        }
        
        tr:hover {
            background: var(--light-bg);
        }
        
        /* Success Message */
        .success-msg {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            padding: 1rem;
            border-radius: var(--radius-lg);
            margin-top: 1rem;
            font-weight: 500;
            text-align: center;
            box-shadow: var(--shadow-md);
        }
        
        /* Premium Responsive Design */
        @media (max-width: 1024px) {
            .container {
                padding: 1.5rem;
            }
            
            .activity-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 1rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
                padding: 1.5rem;
            }
            
            .header h1 {
                font-size: 28px;
            }
            
            .header .subtitle {
                font-size: 16px;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .activity-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }
            
            .activity-card {
                padding: 2rem;
                min-height: 160px;
            }
            
            .activity-name {
                font-size: 20px;
                margin-bottom: 1rem;
            }
            
            .activity-time {
                font-size: 16px;
                margin-bottom: 1rem;
            }
            
            .progress-text {
                font-size: 14px;
                margin-bottom: 0.5rem;
            }
            
            .progress-container {
                height: 12px;
                margin-bottom: 1rem;
            }
            
            .section-title {
                font-size: 22px;
                margin-bottom: 1.5rem;
            }
            
            .section-icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }
            
            .card-title {
                font-size: 22px;
                margin-bottom: 1rem;
            }
            
            .card-description {
                font-size: 16px;
            }
            
            th, td {
                padding: 1rem;
                font-size: 16px;
            }
            
            .schedule-header {
                padding: 1.5rem;
            }
            
            .schedule-header h3 {
                font-size: 20px;
            }
            
            .toggle-icon {
                font-size: 24px;
            }
            
            .logout-btn {
                padding: 1rem 2rem;
                font-size: 16px;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0.75rem;
            }
            
            .header {
                padding: 1rem;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .header .subtitle {
                font-size: 14px;
            }
            
            .card {
                padding: 1.25rem;
            }
            
            .activity-card {
                padding: 1.75rem;
                min-height: 140px;
            }
            
            .activity-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
            }
            
            .activity-name {
                font-size: 18px;
                margin-bottom: 0.75rem;
            }
            
            .activity-time {
                font-size: 14px;
                margin-bottom: 0.75rem;
            }
            
            .progress-text {
                font-size: 12px;
                margin-bottom: 0.5rem;
            }
            
            .progress-container {
                height: 10px;
                margin-bottom: 0.75rem;
            }
            
            .section-title {
                font-size: 20px;
                margin-bottom: 1.25rem;
            }
            
            .section-icon {
                width: 24px;
                height: 24px;
                font-size: 12px;
            }
            
            .card-title {
                font-size: 20px;
                margin-bottom: 0.75rem;
            }
            
            .card-description {
                font-size: 14px;
            }
            
            th, td {
                padding: 0.8rem;
                font-size: 14px;
            }
            
            .schedule-header {
                padding: 1.25rem;
            }
            
            .schedule-header h3 {
                font-size: 18px;
            }
            
            .toggle-icon {
                font-size: 20px;
            }
            
            .logout-btn {
                padding: 0.8rem 1.5rem;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div>
                <h1>Time Tracker</h1>
                <div class="subtitle">Track your daily activities and productivity</div>
            </div>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </header>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Log Activity</h2>
                <p class="card-description">Select an activity to log your completed work</p>
            </div>
            
            <div class="activity-section">
                <div class="section-header">
                    <div class="section-icon">📅</div>
                    <h3 class="section-title">Daily Activities</h3>
                </div>
                <div class="activity-grid">
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
                    
                    echo "<div class='activity-card' onclick='logActivity(\"daily_{$row['id']}\")'>
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
                <div class="section-header">
                    <div class="section-icon">💼</div>
                    <h3 class="section-title">Office Work</h3>
                </div>
                <div class="activity-grid">
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
                    
                    echo "<div class='activity-card' onclick='logActivity(\"office_{$row['id']}\")'>
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

        <div class="table-container">
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