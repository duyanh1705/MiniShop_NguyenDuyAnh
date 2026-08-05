<?php
class Database
{
    protected string $host = "localhost";
    protected string $database = "nguyenduyanh_database";
    protected string $username = "root";
    protected string $password = "";
    protected mysqli $conn;
    public function __construct()
    {
        $this->connect();
    }
    // Kết nối cơ sở dữ liệu
    protected function connect(): void
    {
        try {
            $this->conn = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );
            if ($this->conn->connect_errno) {
                throw new Exception("Kết nối DB thất bại: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
    // Trả về đối tượng kết nối MySQLi
    public function getConnection(): mysqli
    {
        return $this->conn;
    }
    /*Đóng kết nối
    public function close(): void
    {
    if(isset($this->conn)){
    $this->conn->close();
    }
    }
*/
}