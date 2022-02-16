<?php

namespace Cocoon\Control\Features;

/**
 * Les règles de vérification pour les fichiers.
 */
trait UploadFile
{
    /**
     * Verifie si il y a un fichier
     *
     * @param string $value
     * @param array $args
     * @param string $field
     * @return boolean
     */
    protected function ruleRequiredFile($value, $args, $field) :bool
    {
        return !empty($this->file($field)['name']);
    }
    /**
     * Verifit la taille di fichier
     *
     * @param string $value
     * @param array $args
     * @param string $field
     * @return boolean
     */
    protected function ruleSize($value, $args, $field) :bool
    {
        // size par defaut en octets = 2mo
        $size = $args[0] ?? '2000000';
        $filezize = $this->file($field)['size'];
        return $filezize <= $size;
    }
    /**
     * Verifie si le type de fichier est autorisé
     *
     * @param string $value
     * @param array $args
     * @param string $field
     * @return boolean
     */
    protected function ruleType($value, $args, $field) :bool
    {
        $ext = strtolower(explode('.', $this->file($field)['name'])[1]);
        return in_array($ext, $args) !== false;
    }
    /**
     * Pour retourner les infos sur les fichiers
     *
     * @param string $key
     * @return void
     */
    protected function file($key)
    {
        return $_FILES[$key];
    }
}
