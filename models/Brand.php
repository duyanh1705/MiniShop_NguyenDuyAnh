<?php
class Brand
{
    public int $id;
    public string $brandName;
    public string $slug;
    public ?string $image;
    public ?string $description;
    public int $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(
        string $brandName = "",
        string $slug = "",
        ?string $image = null,
        ?string $description = null,
        int $status = 1
    ) {
        $this->brandName = $brandName;
        $this->slug = $slug;
        $this->image = $image;
        $this->description = $description;
        $this->status = $status;
    }
}