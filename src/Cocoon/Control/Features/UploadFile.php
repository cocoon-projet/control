<?php
declare(strict_types=1);

namespace Cocoon\Control\Features;

/**
 * Les règles de vérification pour les fichiers.
 */
trait UploadFile
{
    /**
     * Types MIME autorisés par extension
     */
    private array $allowedMimeTypes = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'zip' => ['application/zip'],
        'rar' => ['application/x-rar-compressed']
    ];

    /**
     * Vérifie si un fichier a été uploadé
     *
     * @param mixed $value
     * @param array $args
     * @param string $field
     * @return bool
     */
    protected function ruleRequiredFile(mixed $value, array $args, string $field): bool
    {
        if (!isset($_FILES[$field])) {
            return false;
        }

        return !empty($_FILES[$field]['name']) && $_FILES[$field]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Vérifie la taille du fichier
     *
     * @param mixed $value
     * @param array $args
     * @param string $field
     * @return bool
     */
    protected function ruleSize(mixed $value, array $args, string $field): bool
    {
        if (!isset($_FILES[$field])) {
            return false;
        }

        // Conversion de la taille en octets (ex: 2M, 500K, 1G)
        $size = $this->convertSizeToBytes($args[0] ?? '2M');
        return $_FILES[$field]['size'] <= $size;
    }

    /**
     * Vérifie le type MIME du fichier
     *
     * @param mixed $value
     * @param array $args
     * @param string $field
     * @return bool
     */
    protected function ruleMimes(mixed $value, array $args, string $field): bool
    {
        if (!isset($_FILES[$field])) {
            return false;
        }

        $file = $_FILES[$field];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // Vérification de l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!isset($this->allowedMimeTypes[$extension])) {
            return false;
        }

        // Vérification du type MIME
        return in_array($mimeType, $this->allowedMimeTypes[$extension], true);
    }

    /**
     * Vérifie les dimensions d'une image
     *
     * @param mixed $value
     * @param array $args
     * @param string $field
     * @return bool
     */
    protected function ruleDimensions(mixed $value, array $args, string $field): bool
    {
        if (!isset($_FILES[$field])) {
            return false;
        }

        $file = $_FILES[$field];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        // Vérifie si c'est une image
        if (!str_starts_with($mimeType, 'image/')) {
            return false;
        }

        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return false;
        }

        [$width, $height] = $imageInfo;

        // Vérifie les dimensions minimales
        if (isset($args[0]) && $width < $args[0]) {
            return false;
        }
        if (isset($args[1]) && $height < $args[1]) {
            return false;
        }

        // Vérifie les dimensions maximales
        if (isset($args[2]) && $width > $args[2]) {
            return false;
        }
        if (isset($args[3]) && $height > $args[3]) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si le fichier est une image
     *
     * @param mixed $value
     * @param array $args
     * @param string $field
     * @return bool
     */
    protected function ruleImage(mixed $value, array $args, string $field): bool
    {
        if (!isset($_FILES[$field])) {
            return false;
        }

        $file = $_FILES[$field];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        return str_starts_with($mimeType, 'image/');
    }

    /**
     * Convertit une taille en octets
     *
     * @param string $size
     * @return int
     */
    private function convertSizeToBytes(string $size): int
    {
        $unit = strtolower(substr($size, -1));
        $size = (int)substr($size, 0, -1);

        return match ($unit) {
            'g' => $size * 1024 * 1024 * 1024,
            'm' => $size * 1024 * 1024,
            'k' => $size * 1024,
            default => $size
        };
    }

    /**
     * Pour retourner les infos sur les fichiers
     *
     * @param string $key
     * @return array|null
     */
    protected function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }
}
