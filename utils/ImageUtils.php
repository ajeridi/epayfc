<?php
// Classse utilitaire pour la gestion d'images

class ImageUtils
{

    public static function validate_image(array $image): array
    {
        $max_size = 5 * 1024 * 1024; // 5MB
        $errors = [];
        if ($image["size"] > $max_size) {
            $errors[] = "File size cannot exceed 5MB";
        }
        if (!in_array($image["type"], ["image/png", "image/gif", "image/jpeg", ".jpg", "image/webp"])) {
            $errors[] = "File type must be .png, .gif, .jpg, .webp";
        }
        return $errors;
    }


    public static function scale_image(array $image, float $width): GdImage
    { // methode qui change la resolution d'une image
        $converted_image = self::convert_to_GDImage($image);
        return imagescale($converted_image, $width);
    }

    private static function convert_to_GDImage(array $image): GdImage | false
    { // methode qui converti un fichier $_FILES["image] en GDImage
        $image_type = $image["type"];
        $image_name = $image["tmp_name"];
        switch ($image_type) {
            case "image/png":
                return imagecreatefrompng($image_name);
            case "image/gif":
                return imagecreatefromgif($image_name);
            case "image/jpeg":
                return imagecreatefromjpeg($image_name);
            case "image/webp":
                return imagecreatefromwebp($image_name);
        }
        return false;
    }

    public static function get_thumbnail_path(string $path): string
    { // prends le path de la photo principale et renvoie le path du thumbnail
        $split_path = pathinfo($path);
        return $split_path['dirname'] . '/' . $split_path['filename'] . '_thumbnail' . "." . $split_path['extension'];
    }

    public static function delete_image_files(string $image_path): void
    {
        unlink($image_path);
        unlink(self::get_thumbnail_path($image_path));
    }

    public static function save_image_file(GdImage $image, string $path){
        imagejpeg($image, $path);
    }
}
