<?php
class Product {
    public int $id;
    public int $categoryId;
    public int $brandId;
    public string $proName;
    public string $slug;
    public float $price;
    public float $discountPrice;
    public int $quantity;
    public ?string $image;
    public ?string $description;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        int $categoryId = 0,
        int $brandId = 0,
        string $proName = "",
        string $slug = "",
        float $price = 0,
        float $discountPrice = 0,
        int $quantity = 0,
        ?string $image = null,
        ?string $description = null,
        int $status = 1
    ) {
        $this->categoryId = $categoryId;
        $this->brandId = $brandId;
        $this->proName = $proName;
        $this->slug = $slug;
        $this->price = $price;
        $this->discountPrice = $discountPrice;
        $this->quantity = $quantity;
        $this->image = $image;
        $this->description = $description;
        $this->status = $status;
    }
}
?>