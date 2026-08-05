<?php
class ProductImage {
    public int $id;
    public int $productId;
    public string $image;
    public int $sortOrder;
    public string $createdAt;

    public function __construct(
        int $productId = 0,
        string $image = "",
        int $sortOrder = 0
    ) {
        $this->productId = $productId;
        $this->image = $image;
        $this->sortOrder = $sortOrder;
    }
}
?>