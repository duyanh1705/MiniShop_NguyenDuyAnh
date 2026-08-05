<?php
class User {
    public int $id;
    public string $fullName;
    public string $username;
    public string $password;
    public string $email;
    public ?string $phone;
    public ?string $address;
    public int $role;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        string $fullName = "",
        string $username = "",
        string $password = "",
        string $email = "",
        ?string $phone = null,
        ?string $address = null,
        int $role = 0,
        int $status = 1
    ) {
        $this->fullName = $fullName;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->role = $role;
        $this->status = $status;
    }
}
?>