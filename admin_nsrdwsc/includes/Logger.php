<?php
class Logger
{
    private $db;
    private $user_id;
    private $username;
    private $user_type;
    private $user_ip;
    private $user_agent;
    private $session_id;

    public function __construct($db)
    {
        $this->db = $db;
        
        // Get user info from session if logged in
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->user_id = $_SESSION['user_id'] ?? $_SESSION['admin_id'] ?? null;
        $this->username = $_SESSION['username'] ?? 'Unknown';
        $this->user_type = $_SESSION['login_type'] ?? $_SESSION['user_type'] ?? 'user';
        $this->user_ip = $this->getClientIP();
        $this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $this->session_id = session_id();
    }

    private function getClientIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    // Log login attempt with session tracking
    public function logLogin($email, $status, $failure_reason = null)
    {
        $login_time = date('Y-m-d H:i:s');
        
        $query = "INSERT INTO tbl_login_logs 
                  (user_email, login_status, failure_reason, user_ip, user_agent, login_time, session_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);

        if (!$stmt) {
            error_log("Logger Prepare Failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("sssssss", $email, $status, $failure_reason, $this->user_ip, $this->user_agent, $login_time, $this->session_id);
        
        if ($stmt->execute()) {
            // Store the login log ID in session for logout tracking
            if ($status === 'success') {
                $_SESSION['login_log_id'] = $stmt->insert_id;
                $_SESSION['login_time'] = $login_time;
            }
            return true;
        }
        
        return false;
    }

    // Log logout and calculate session duration
    public function logLogout($email = null)
    {
        // If email not provided, try to get from session
        if (!$email && isset($_SESSION['email_id'])) {
            $email = $_SESSION['email_id'];
        }
        
        if (!$email) {
            error_log("Logout failed: No email provided or found in session");
            return false;
        }
        
        $logout_time = date('Y-m-d H:i:s');
        $session_id = $this->session_id;
        
        // Find the latest active session for this user
        $query = "SELECT login_log_id, login_time 
                  FROM tbl_login_logs 
                  WHERE user_email = ? 
                  AND session_id = ?
                  AND logout_time IS NULL 
                  AND login_status = 'success'
                  ORDER BY login_time DESC 
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ss", $email, $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $login_log_id = $row['login_log_id'];
            $login_time = $row['login_time'];
            
            // Calculate session duration in seconds
            $login_timestamp = strtotime($login_time);
            $logout_timestamp = strtotime($logout_time);
            $session_duration = $logout_timestamp - $login_timestamp;
            
            // Update the login log with logout time and duration
            $update_query = "UPDATE tbl_login_logs 
                            SET logout_time = ?, session_duration = ? 
                            WHERE login_log_id = ?";
            
            $update_stmt = $this->db->prepare($update_query);
            $update_stmt->bind_param("sii", $logout_time, $session_duration, $login_log_id);
            
            if ($update_stmt->execute()) {
                // Clear session tracking variables
                unset($_SESSION['login_log_id']);
                unset($_SESSION['login_time']);
                return true;
            }
        }
        
        return false;
    }

    // Get session duration for current user
    public function getCurrentSessionDuration()
    {
        if (isset($_SESSION['login_time'])) {
            $login_time = $_SESSION['login_time'];
            $current_time = date('Y-m-d H:i:s');
            $login_timestamp = strtotime($login_time);
            $current_timestamp = strtotime($current_time);
            return $current_timestamp - $login_timestamp;
        }
        return 0;
    }

    // Get user session history
    public function getUserSessionHistory($email, $limit = 10)
    {
        $query = "SELECT 
                    login_log_id,
                    login_time,
                    logout_time,
                    session_duration,
                    user_ip,
                    user_agent,
                    login_status,
                    failure_reason,
                    CASE 
                        WHEN logout_time IS NOT NULL THEN 'Completed'
                        WHEN logout_time IS NULL AND login_status = 'success' THEN 'Active'
                        ELSE 'Failed'
                    END as session_status,
                    CONCAT(
                        FLOOR(session_duration / 3600), ' hours, ',
                        FLOOR((session_duration % 3600) / 60), ' minutes, ',
                        (session_duration % 60), ' seconds'
                    ) as duration_formatted
                  FROM tbl_login_logs 
                  WHERE user_email = ? 
                  ORDER BY login_time DESC 
                  LIMIT ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("si", $email, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $sessions[] = $row;
        }
        
        return $sessions;
    }

    // Get overall user statistics
    public function getUserLoginStats($email)
    {
        $stats = [];
        
        // Total successful logins
        $query1 = "SELECT COUNT(*) as total_logins 
                   FROM tbl_login_logs 
                   WHERE user_email = ? AND login_status = 'success'";
        $stmt1 = $this->db->prepare($query1);
        $stmt1->bind_param("s", $email);
        $stmt1->execute();
        $result1 = $stmt1->get_result();
        $stats['total_logins'] = $result1->fetch_assoc()['total_logins'];
        
        // Total failed logins
        $query2 = "SELECT COUNT(*) as failed_logins 
                   FROM tbl_login_logs 
                   WHERE user_email = ? AND login_status = 'failed'";
        $stmt2 = $this->db->prepare($query2);
        $stmt2->bind_param("s", $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        $stats['failed_logins'] = $result2->fetch_assoc()['failed_logins'];
        
        // Total session time (sum of all completed sessions)
        $query3 = "SELECT SUM(session_duration) as total_session_time 
                   FROM tbl_login_logs 
                   WHERE user_email = ? AND login_status = 'success' AND logout_time IS NOT NULL";
        $stmt3 = $this->db->prepare($query3);
        $stmt3->bind_param("s", $email);
        $stmt3->execute();
        $result3 = $stmt3->get_result();
        $total_seconds = $result3->fetch_assoc()['total_session_time'] ?? 0;
        
        $stats['total_session_time_seconds'] = $total_seconds;
        $stats['total_session_time_formatted'] = $this->formatDuration($total_seconds);
        
        // Average session duration
        $query4 = "SELECT AVG(session_duration) as avg_session_time 
                   FROM tbl_login_logs 
                   WHERE user_email = ? AND login_status = 'success' AND session_duration IS NOT NULL";
        $stmt4 = $this->db->prepare($query4);
        $stmt4->bind_param("s", $email);
        $stmt4->execute();
        $result4 = $stmt4->get_result();
        $avg_seconds = $result4->fetch_assoc()['avg_session_time'] ?? 0;
        
        $stats['avg_session_time_seconds'] = round($avg_seconds);
        $stats['avg_session_time_formatted'] = $this->formatDuration(round($avg_seconds));
        
        // Last login
        $query5 = "SELECT login_time, logout_time, session_duration 
                   FROM tbl_login_logs 
                   WHERE user_email = ? AND login_status = 'success' 
                   ORDER BY login_time DESC LIMIT 1";
        $stmt5 = $this->db->prepare($query5);
        $stmt5->bind_param("s", $email);
        $stmt5->execute();
        $result5 = $stmt5->get_result();
        $stats['last_login'] = $result5->fetch_assoc();
        
        return $stats;
    }

    private function formatDuration($seconds)
    {
        if (!$seconds) return '0 seconds';
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        $parts = [];
        if ($hours > 0) $parts[] = "$hours hours";
        if ($minutes > 0) $parts[] = "$minutes minutes";
        if ($secs > 0) $parts[] = "$secs seconds";
        
        return implode(', ', $parts);
    }

    // Get all active sessions (users currently logged in)
    public function getActiveSessions()
    {
        $query = "SELECT 
                    l.*,
                    TIMESTAMPDIFF(SECOND, l.login_time, NOW()) as current_duration,
                    CONCAT(
                        FLOOR(TIMESTAMPDIFF(SECOND, l.login_time, NOW()) / 3600), 'h ',
                        FLOOR((TIMESTAMPDIFF(SECOND, l.login_time, NOW()) % 3600) / 60), 'm'
                    ) as current_duration_formatted
                  FROM tbl_login_logs l
                  WHERE l.logout_time IS NULL 
                  AND l.login_status = 'success'
                  AND TIMESTAMPDIFF(HOUR, l.login_time, NOW()) < 24
                  ORDER BY l.login_time DESC";
        
        $result = $this->db->query($query);
        
        $active_sessions = [];
        while ($row = $result->fetch_assoc()) {
            $active_sessions[] = $row;
        }
        
        return $active_sessions;
    }

    // Clean up stale sessions (older than 24 hours with no logout)
    public function cleanupStaleSessions()
    {
        $query = "UPDATE tbl_login_logs 
                  SET logout_time = login_time,
                      session_duration = 0,
                      failure_reason = 'Auto-cleaned: Session timeout'
                  WHERE logout_time IS NULL 
                  AND login_time < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        
        return $this->db->query($query);
    }

    // [Keep your existing methods: getRecordIdentifier, getPrimaryKey, getOldRecordData, 
    // log, archiveDeletedRecord, getChangedFields, logDeleteWithFiles]
    
    // ... (include all your existing methods here)
    // Get record identifier (like project name) for better readability
    private function getRecordIdentifier($table_name, $record_id)
    {
        if (!$record_id) return null;

        // Define identifier fields for different tables
        $identifiers = [
            'tbl_proj' => 'proj_name',
            'tbl_developer' => 'dev_heading',
            'tbl_community' => 'comm_heading',
            'tbl_proj_type' => 'proj_type_name',
            'tbl_proj_gallery' => 'gallery_img',
            'tbl_proj_featfac' => 'featfac_heading',
            'tbl_admin_users' => 'username',
            'tbl_blog' => 'blog_heading'
            
        ];

        if (isset($identifiers[$table_name])) {
            $field = $identifiers[$table_name];
            $query = "SELECT $field FROM $table_name WHERE ";

            // Get primary key field
            $pk = $this->getPrimaryKey($table_name);
            $query .= "$pk = ?";

            $stmt = $this->db->prepare($query);
            $stmt->bind_param("i", $record_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                return $row[$field];
            }
        }
        return null;
    }

    private function getPrimaryKey($table_name)
    {
        $pk_query = "SHOW KEYS FROM $table_name WHERE Key_name = 'PRIMARY'";
        $pk_result = $this->db->query($pk_query);
        if ($pk_row = $pk_result->fetch_assoc()) {
            return $pk_row['Column_name'];
        }
        return 'id';
    }

    // Get old record data
    public function getOldRecordData($table_name, $record_id)
    {
        $pk = $this->getPrimaryKey($table_name);
        $query = "SELECT * FROM $table_name WHERE $pk = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $record_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Log database action
    public function log($action_type, $table_name, $record_id = null, $old_data = null, $new_data = null, $description = null)
    {
        $changed_fields = null;
        $old_json = null;
        $new_json = null;
        $record_identifier = $this->getRecordIdentifier($table_name, $record_id);

        // Calculate changes
        if ($old_data && $new_data) {
            $changes = $this->getChangedFields($old_data, $new_data);
            $changed_fields = !empty($changes) ? json_encode($changes) : null;
            $old_json = json_encode($old_data, JSON_UNESCAPED_UNICODE);
            $new_json = json_encode($new_data, JSON_UNESCAPED_UNICODE);
        } elseif ($old_data && !$new_data && $action_type == 'DELETE') {
            $old_json = json_encode($old_data, JSON_UNESCAPED_UNICODE);
        } elseif (!$old_data && $new_data && $action_type == 'INSERT') {
            $new_json = json_encode($new_data, JSON_UNESCAPED_UNICODE);
        }

        $query = "INSERT INTO tbl_activity_logs 
                  (user_id, username, user_type, user_ip, user_agent, action_type, 
                   table_name, record_id, record_identifier, old_data, new_data, 
                   changed_fields, action_description, request_url, http_method) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($query);
        $request_url = $_SERVER['REQUEST_URI'] ?? '';
        $http_method = $_SERVER['REQUEST_METHOD'] ?? '';

        $stmt->bind_param(
            "issssssisssssss",
            $this->user_id,
            $this->username,
            $this->user_type,
            $this->user_ip,
            $this->user_agent,
            $action_type,
            $table_name,
            $record_id,
            $record_identifier,
            $old_json,
            $new_json,
            $changed_fields,
            $description,
            $request_url,
            $http_method
        );

        return $stmt->execute();
    }


      // Archive deleted record
    public function archiveDeletedRecord($table_name, $record_id, $record_data)
    {
        $query = "INSERT INTO tbl_deleted_records (original_table, record_id, deleted_data, deleted_by, deleted_by_name, ip_address) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $data_json = json_encode($record_data, JSON_UNESCAPED_UNICODE);
        $stmt->bind_param("sissss", $table_name, $record_id, $data_json, $this->user_id, $this->username, $this->user_ip);
        return $stmt->execute();
    }

      private function getChangedFields($old_data, $new_data)
    {
        $changes = [];
        foreach ($new_data as $key => $value) {
            if (isset($old_data[$key]) && $old_data[$key] != $value) {
                $changes[$key] = [
                    'old' => $old_data[$key],
                    'new' => $value
                ];
            }
        }
        return $changes;
    }

     // Helper to log delete with file cleanup tracking
    public function logDeleteWithFiles($table_name, $record_id, $record_data, $files_deleted = [])
    {
        $description = "Deleted from $table_name";
        if (!empty($files_deleted)) {
            $description .= " - Files deleted: " . implode(', ', $files_deleted);
        }

        // Archive the record
        $this->archiveDeletedRecord($table_name, $record_id, $record_data);

        // Log the action
        return $this->log('DELETE', $table_name, $record_id, $record_data, null, $description);
    }
}
?>