<?php
declare(strict_types=1);

namespace Cocoon\Control\Features;

use DateTimeInterface;

/**
 * Les règles de vérification pour les chaines de caractère.
 */
trait Rules
{
    /**
     * Cache des expressions régulières compilées
     */
    private array $compiledRegex = [];

    /**
     * la valeur est un entier
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleInt(mixed $value, array $args): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return is_int((int) $value);
    }

    /**
     * La valeur est une adresse email valide
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleEmail(mixed $value, array $args): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * la valeur est requise
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleRequired(mixed $value, array $args): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }
        return !empty($value);
    }

    /**
     * La valeur est un url valide
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleUrl(mixed $value, array $args): bool
    {
        return is_string($value) && filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * la valeur est chaîne est alphabétique
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleAlpha(mixed $value, array $args): bool
    {
        return is_string($value) && ctype_alpha($value);
    }

    /**
     * La valeur est une chaîne est alphanumérique
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleAlNum(mixed $value, array $args): bool
    {
        return is_string($value) && ctype_alnum($value);
    }

    /**
     * Vérifie si la valeur ne contient que des lettres, chiffres, tirets et underscores
     */
    private function ruleAlNumDash(mixed $value, array $params, string $field): bool
    {
        if (!is_string($value)) {
            return false;
        }

        if (!isset($this->compiledRegex['al_num_dash'])) {
            $this->compiledRegex['al_num_dash'] = '/^[a-zA-Z0-9\-_]+$/';
        }

        return (bool)preg_match($this->compiledRegex['al_num_dash'], $value);
    }

    /**
     * La valeur est inferieur ou egal au nombre définit
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleNumMin(mixed $value, array $args): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return (float) $value >= (float) $args[0];
    }

    /**
     * La valeur est superieur ou egal au nombre définit
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleNumMax(mixed $value, array $args): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return (float) $value <= (float) $args[0];
    }

    /**
     * La valeur a une longueur inférieur ou égal à celle définit
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleMax(mixed $value, array $args): bool
    {
        if (!is_string($value)) {
            return false;
        }
        return mb_strlen($value) <= (int) $args[0];
    }

    /**
     * La valeur a une longueur supérieur ou égal à celle définit
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleMin(mixed $value, array $args): bool
    {
        if (!is_string($value)) {
            return false;
        }
        return mb_strlen($value) >= (int) $args[0];
    }

    /**
     * la valeur est identique
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleSame(mixed $value, array $args): bool
    {
        if (!isset($args[0])) {
            return false;
        }
        return $value === ($this->data[$args[0]] ?? null);
    }

    /**
     * La valeur est un tableau
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleArray(mixed $value, array $args): bool
    {
        return is_array($value);
    }

    /**
     * La valeur corespond à l'expression régulière définit
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleRegex(mixed $value, array $args): bool
    {
        if (!isset($args[0]) || !is_string($value)) {
            return false;
        }
        $pattern = $args[0];
        if (!isset($this->compiledRegex[$pattern])) {
            $this->compiledRegex[$pattern] = $pattern;
        }
        return (bool)preg_match($this->compiledRegex[$pattern], $value);
    }

    /**
     * La valeur à une longueur comprise entre n et n caractères
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleBetween(mixed $value, array $args): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return (float) $value >= (float) $args[0] && (float) $value <= (float) $args[1];
    }

    /**
     * La valeur(un nombre) est comprise entre n et n nombre
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleBetweenNum(mixed $value, array $args): bool
    {
        if (!is_numeric($value)) {
            return false;
        }
        return (float) $value >= (float) $args[0] && (float) $value <= (float) $args[1];
    }

    /**
     * La valeur est vrai
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleTrue(mixed $value, array $args): bool
    {
        $possibility = ['yes', 'on', '1', 1, true, 'true'];
        return in_array($value, $possibility, true);
    }

    /**
     * La valeur est un boolean
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleBool(mixed $value, array $args): bool
    {
        return is_bool($value);
    }

    /**
     * La valeur est une adresse ip valide
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleIp(mixed $value, array $args): bool
    {
        if (!is_string($value)) {
            return false;
        }
        if (isset($args[0]) && $args[0] === 'v4') {
            return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
        }
        if (isset($args[0]) && $args[0] === 'v6') {
            return filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
        }
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * la valeur est un nombre
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleNum(mixed $value, array $args): bool
    {
        return is_numeric($value);
    }

    /**
     * La valeur est une date valide
     *
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    protected function ruleDate(mixed $value, array $args): bool
    {
        if ($value instanceof DateTimeInterface) {
            return true;
        }
        if (!is_string($value) || strtotime($value) === false) {
            return false;
        }
        $date = date_parse($value);
        return checkdate($date['month'], $date['day'], $date['year']);
    }
}
