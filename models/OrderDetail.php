<?php
class OrderDetail {
    public int $id;
    public int $orderId;
    public int $productId;
    public int $quantity;
    public float $price;
    public float $subtotal;
    public string $createdAt;

    public function __construct(
        int $orderId = 0,
        int $productId = 0,
        int $quantity = 0,
        float $price = 0,
        float $subtotal = 0
    ) {
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->subtotal = $subtotal;
    }
}
?>