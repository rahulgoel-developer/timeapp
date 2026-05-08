<?php
include 'config.php';

if (isset($_POST['action']) && $_POST['action'] == 'log_activity') {
    $raw_data = $_POST['data'];
    $parts = explode('_', $raw_data);
    $type = $parts[0]; // daily or office
    $item_id = $parts[1];

    // 1. Find the last log entry to determine start time
    $last_log = $conn->query("SELECT ended_at FROM activity_log ORDER BY ended_at DESC LIMIT 1");
    
    if ($last_log->num_rows > 0) {
        $row = $last_log->fetch_assoc();
        $started_at = $row['ended_at'];
    } else {
        // If no previous log, default to start of today or hardcoded date
        $started_at = date('Y-m-d H:i:s', strtotime('-1 hour')); 
    }

    $ended_at = date('Y-m-d H:i:s');
    
    // 2. Calculate duration in seconds
    $start_ts = strtotime($started_at);
    $end_ts = strtotime($ended_at);
    $duration = $end_ts - $start_ts;

    // 3. Prepare Insert
    $activity_id = ($type == 'daily') ? $item_id : 'NULL';
    $office_id = ($type == 'office') ? $item_id : 'NULL';

    $sql = "INSERT INTO activity_log (activity_id, office_work_id, started_at, ended_at, duration_in_seconds) 
            VALUES ($activity_id, $office_id, '$started_at', '$ended_at', $duration)";

    if ($conn->query($sql)) {
        echo "Activity logged! Duration: " . floor($duration / 60) . " minutes.";
        
        // If it was office work, update its status
        if($type == 'office') {
            $conn->query("UPDATE office_work_to_do SET status = 'completed' WHERE id = $item_id");
        }
    } else {
        echo "Error: " . $conn->error;
    }
}
?>