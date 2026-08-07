document.getElementById("image").addEventListener("change", function () {
    const preview = document.getElementById("preview");
    preview.innerHTML = "";
    const files = this.files;
    for (let i = 0; i < files.length; i++) {
        const img = document.createElement("img");
        img.src = URL.createObjectURL(files[i]);
        img.width = 200;
        img.className = "img-thumbnail";
        preview.appendChild(img);
    }
});

const galleryInput = document.getElementById("images");
if (galleryInput) {
    galleryInput.addEventListener("change", function () {
        const galleryPreview = document.getElementById("gallery_preview");
        if (galleryPreview) {
            galleryPreview.innerHTML = "";
            const files = this.files;
            for (let i = 0; i < files.length; i++) {
                const img = document.createElement("img");
                img.src = URL.createObjectURL(files[i]);
                img.width = 120;
                img.className = "img-thumbnail me-2 mb-2";
                galleryPreview.appendChild(img);
            }
        }
    });
}