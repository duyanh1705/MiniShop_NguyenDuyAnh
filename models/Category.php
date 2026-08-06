<?php
class Category
{
    public int $id;
    public string $catename;
    public string $slug;
    public ?string $image;
    public ?string $description;
    public int $status;
    public string $createdAt;
    public string $updatedAt;
    public function __construct(
        string $catename = "",
        string $slug = "",
        ?string $image = null,
        ?string $description = null,
        int $status = 1
    ) {
        $this->catename = $catename;
        $this->slug = $slug;
        $this->image = $image;
        $this->description = $description;
        $this->status = $status;
    }
}
?>
