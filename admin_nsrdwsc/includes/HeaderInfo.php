<?php

class HeaderInfo
{
    private mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;

        // Get user info from session if logged in
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function getHeaderInfo()
    {
        $query = "SELECT * FROM tbl_header_info LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}
?>