<?php
require_once __DIR__ . "/BaseDAO.php";
require_once __DIR__ . "/../models/User.php";

class UserDAO extends BaseDAO {
    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $list = [];
        try {
            $sql = "SELECT * FROM users ORDER BY id DESC";
            $result = $this->executeQuery($sql);
            while ($row = $result->fetch_assoc()) {
                $user = new User(
                    $row["fullname"],
                    $row["username"],
                    $row["password"],
                    $row["email"],
                    $row["phone"],
                    $row["address"],
                    (int)$row["role"],
                    (int)$row["status"]
                );
                $user->id = (int)$row["id"];
                $user->createdAt = $row["created_at"];
                $user->updatedAt = $row["updated_at"];
                $list[] = $user;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $list;
    }

    public function findById(int $id): ?User {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $user = new User(
                    $row["fullname"],
                    $row["username"],
                    $row["password"],
                    $row["email"],
                    $row["phone"],
                    $row["address"],
                    (int)$row["role"],
                    (int)$row["status"]
                );
                $user->id = (int)$row["id"];
                $user->createdAt = $row["created_at"];
                $user->updatedAt = $row["updated_at"];
                return $user;
            }
        } catch (Exception $e) {
            throw $e;
        }
        return null;
    }

    public function insert(User $user): bool {
        try {
            $sql = "INSERT INTO users (fullname, username, password, email, phone, address, role, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ssssssii", $user->fullName, $user->username, $user->password, $user->email, $user->phone, $user->address, $user->role, $user->status);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function update(User $user): bool {
        try {
            $sql = "UPDATE users SET fullname = ?, username = ?, password = ?, email = ?, phone = ?, address = ?, role = ?, status = ? WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("ssssssiii", $user->fullName, $user->username, $user->password, $user->email, $user->phone, $user->address, $user->role, $user->status, $user->id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool {
        try {
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>