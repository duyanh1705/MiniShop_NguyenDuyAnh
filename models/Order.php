<?php
class Order {
    public int $id;
    public int $customerId;
    public ?int $userId;
    public string $orderCode;
    public float $totalAmount;
    public ?string $note;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public ?string $customerName = null;
    public ?string $customerPhone = null;
    public ?string $customerAddress = null;
    public ?string $userName = null;

    public function __construct(
        int $customerId = 0,
        ?int $userId = null,
        string $orderCode = "",
        float $totalAmount = 0,
        ?string $note = null,
        int $status = 0
    ) {
        $this->customerId = $customerId;
        $this->userId = $userId;
        $this->orderCode = $orderCode;
        $this->totalAmount = $totalAmount;
        $this->note = $note;
        $this->status = $status;
    }
}
?>