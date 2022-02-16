<?php

namespace Cocoon\Control\Features;

use DateTimeInterface;

/**
 * Les règles de vérification pour les chaines de caractère.
 */
trait Rules
{
    /**
     * la valeur est un entier
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleInt($value, $args) :bool
    {
        if (is_numeric($value)) {
            return false;
        }
        return is_int($value);
    }
    /**
     * La valeur est une adresse email valide
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleEmail($value, $args) :bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }
    /**
     * la valeur est requise
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleRequired($value, $args) :bool
    {
        return !empty($value);
    }
    /**
     * La valeur est un url valide
     *
     * @param string $value
     * @param array $args
     * @return boolean
     */
    protected function ruleUrl($value, $args) :bool
    {
        return filter_var($value, FILTER_VALIDATE_URL);
    }
    /**
     * la valeur est chaîne est alphabétique
     *
     * @param string $value
     * @param array $args
     * @return boolean
     */
    protected function ruleAlpha($value, $args) :bool
    {
        if (!is_string($value)) {
            return false;
        }
        // TODO mettre un preg_match si ctype existe pas
        return ctype_alpha($value);
    }
    /**
     * La valeur est une chaîne est alphanumérique
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleAlNum($value, $args) :bool
    {
        if (!is_string($value)) {
            return false;
        }
        // TODO mettre un preg_match si ctype existe pas
        return ctype_alnum($value);
    }
    /**
     * La valeur est une chaîne est alphanumérique plus underscore ou tiret
     *
     * @param string $value
     * @param array $args
     * @return void
     */
    protected function ruleAlNumDash($value, $args) :bool
    {
        if (!is_string($value)) {
            return false;
        }
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }
    /**
     * La valeur est inferieur ou egal au nombre définit
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleNumMin($value, $args) :bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return $value >= $args[0];
    }
    /**
     * La valeur est superieur ou egal au nombre définit
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleNumMax($value, $args) :bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return $value <= $args[0];
    }

     /**
     * La valeur a une longueur inférieur ou égal à celle définit
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleMax($value, $args) :bool
    {
        if (!is_string($value)) {
            return false;
        }
        return strlen($value) <= $args[0];
    }
    /**
     * La valeur a une longueur supérieur ou égal à celle définit
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleMin($value, $args) :bool
    {
        if (!is_string($value)) {
            return false;
        }
        return strlen($value) >= $args[0];
    }
    /**
     * la valeur est identique
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleSame($value, $args) :bool
    {
        if (!isset($args[0])) {
            return false;
        }
        return $value === $this->data[$args[0]];
    }
    /**
     * La valeur est un tableau
     *
     * @param string $value
     * @param array $args
     * @return bool
     */
    protected function ruleArray($value, $args) :bool
    {
        return is_array($value);
    }
    /**
     * La valeur corespond à l'expression régulière définit
     *
     * @param string $value
     * @param array $args
     * @return boolean
     */
    protected function ruleRegex($value, $args) :bool
    {
        if (!isset($args[0])) {
            return false;
        }
        return preg_match($args[0], $value);
    }
    /**
     * La valeur à une longueur comprise entre n et n caractères
     *
     * @param string $value
     * @param array $args
     * @return boolean
     */
    protected function ruleBetween($value, $args) :bool
    {
        return $value >= $args[0] && $value <= $args[1];
    }
    /**
     * La valeur(un nombre) est comprise entre n et n nombre
     *
     * @param string $value
     * @param array $args
     * @return boolean
     */
    protected function ruleBetweenNum($value, $args) :bool
    {
        return $value >= $args[0] && $value <= $args[1];
    }
    /**
     * La valeur est vrai
     *
     * @param string array
     * @param array $args
     * @return boolean
     */
    protected function ruleTrue($value, $args) :bool
    {
        $possibility = ['yes', 'on', '1', 1, true, 'true'];
        return in_array($value, $possibility, true);
    }
    /**
     * La valeur est un boolean
     *
     * @param string $value
     * @param array $args
     * @return boolean
     */
    protected function ruleBool($value, $args) :bool
    {
        return is_bool($value);
    }
    /**
     * La valeur est une adresse ip valide
     *
     * @param string $value
     * @param array $args v4 ou v6
     * @return boolean
     */
    protected function ruleIp($value, $args) :bool
    {
        if (isset($args[0]) && $args[0] == 'v4') {
            return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        if (isset($args[0]) && $args[0] == 'v6') {
            return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        }
        return filter_var($value, FILTER_VALIDATE_IP);
    }
    /**
     * la valeur est un nombre
     *
     * @param string $value
     * @param array $args
     * @return boolean
     */
    protected function ruleNum($value, $args) :bool
    {
        return is_numeric($value);
    }

    protected function ruleDate($value, $args) :bool
    {
        if ($value instanceof DateTimeInterface) {
            return true;
        }
        if (strtotime($value) === false) {
            return false;
        }
        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }
}
