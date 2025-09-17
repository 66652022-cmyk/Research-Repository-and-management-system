<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'research_management_system';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect() {
        if ($this->conn === null) {
            $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->db_name);

            if (!$this->conn) {
                die("Database connection failed: " . mysqli_connect_error());
            }

            // Set charset to utf8mb4
            if (!mysqli_set_charset($this->conn, 'utf8mb4')) {
                die("Error setting charset: " . mysqli_error($this->conn));
            }
        }
        return $this->conn;
    }

    public function close() {
        if ($this->conn !== null) {
            mysqli_close($this->conn);
            $this->conn = null;
        }
    }
}
?>
