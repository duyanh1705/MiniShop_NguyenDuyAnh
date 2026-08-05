<?php
class Customer {
    public int $id;
    public string $fullName;
    public string $phone;
    public ?string $email;
    public ?string $address;
    public ?string $note;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        string $fullName = "",
        string $phone = "",
        ?string $email = null,
        ?string $address = null,
        ?string $note = null,
        int $status = 1
    ) {
        $this->fullName = $fullName;
        $this->phone = $phone;
        $this->email = $email;
        $this->address = $address;
        $this->note = $note;
        $this->status = $status;
    }
}
?>