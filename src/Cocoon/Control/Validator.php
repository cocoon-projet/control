<?php

namespace Cocoon\Control;

use Closure;
use Cocoon\Control\Features\Rules;
use Cocoon\Control\Features\UploadFile;

/**
 * Classe gérant la validation des données
 */
class Validator
{
    /**
     * Liste des messagges par defaut (classé par langue) ex fr.php
     *
     * @var array
     */
    private $langMessages;
    /**
     * Données a valider
     *
     * @var array
     */
    private $data;
    /**
     * Messsages d'erreur par règle
     *
     * @var array
     */
    private $messagesErrors = [];
    /**
     * Personalisation des messages d'erreur par règle
     *
     * @var array
     */
    private $aliasMessageRuleError = [];
    /**
     * Personalisation des messages d'erreur par champs et règle
     *
     * @var array
     */
    public $aliasMessageFieldRuleError = [];
    /**
     * Listes des messages d'erreur retournées.
     *
     * @var array
     */
    private $errors = [];
    /**
     * Liste des alias pour les champs
     *
     * @var array
     */
    private $alias = [];
    /**
     * Liste des règles ajoutées
     *
     * @var array
     */
    private $rules = [];

    use Rules, UploadFile;
    /**
     * Contructeur de la classe
     *
     * @param string $lang la langue des messages
     */
    public function __construct($lang = 'fr')
    {
        $DS = DIRECTORY_SEPARATOR;
        $this->langMessages = require __DIR__ . $DS . 'messages' . $DS . $lang . '.php';
        $this->data = array_merge($_POST, $_FILES);
    }
    /**
     * Les données à valider
     *
     * @param array $data
     * @return self
     */
    public function data($data) :self
    {
        $this->data = $data;
        return $this;
    }
    /**
     * Ajouter une règle.
     *
     * @param string $ruleName
     * @param Closure $callback
     * @param string $message
     * @return self
     */
    public function addRule($ruleName, Closure $callback, $message) : self
    {
        $this->rules[$ruleName] = $callback;
        $this->aliasMessageRuleError[$ruleName] = $message;
        return $this;
    }
    /**
     * Ajouter plusieurs règles
     *
     * @param array $rules
     * @return self
     */
    public function addRules($rules = []) :self
    {
        foreach ($rules as $value) {
            $this->addRule($value['name'], $value['callback'], $value['message']);
        }
        return $this;
    }
    /**
     * Redéfinit tous les messages par défaut
     * (nécessaire pour un autre language.)
     *
     * @param string $langFilePath un fichier retournant un tableau
     * @return self
     */
    public function setLangFile($langFilePath) :self
    {
        if (file_exists($langFilePath)) {
            require $langFilePath;
        }
        return $this;
    }
    /**
     * Validation des données
     *
     * @param array $rules listes des règles pour les données a valider
     * @param array $messages Listes des messages personnalisés.
     * @return self
     */
    public function validate($rules, $messages = []) :self
    {
        if (!empty($messages)) {
            $this->addMessages($messages);
        }

        $this->messagesErrors = $this->langMessages;
        $rules = $this->aliased($rules);
        $validate = [];

        foreach ($rules as $key => $value) {
            $validate[$key] = $this->data[$key];
        }

        foreach ($validate as $key => $value) {
            $fieldsVerify = explode('|', $rules[$key]);
            foreach ($fieldsVerify as $rule) {
                $passes = $this->verify($key, $value, $this->getRule($rule), $this->getArgs($rule));
                // la validation est stoppé quand une règle a échoué.
                if (!$passes) {
                    break;
                }
            }
        }
        return $this;
    }
    /**
     * Vérifie si il y a des messages d'erreur.
     *
     * @return bool
     */
    public function fails() : bool
    {
        return !empty($this->errors);
    }
    /**
     * Retourne les messages d'erreurs.
     *
     * @return object instance de Messages::class
     */
    public function errors()
    {
        return new Messages($this->errors);
    }
    /**
     * Définit des messages personnalisés.
     *
     * @param array $messages
     * @return self
     */
    public function addMessages($messages = []) : self
    {
        foreach ($messages as $key => $value) {
            if (strpos($key, '.')) {
                $this->aliasMessageFieldRuleError[$key] = $value;
            } else {
                $this->aliasMessageRuleError[$key] = $value;
            }
        }
        return $this;
    }
    /**
     * Vérification des règles pour une donnée
     *
     * @param string $field
     * @param string $value
     * @param string $rule
     * @param array $args
     * @return bool
     */
    protected function verify($field, $value, $rule, $args) : bool
    {
        $methodRule = $this->rules[$rule] ?? [$this, 'rule' . $this->camelRule($rule)];
        $control = call_user_func_array($methodRule, [$value, $args, $field]);
        if (!$control) {
            $original = $field;
            $aliasField = $this->alias[$field] ?? $field;
            $this->errors[$field] = $this->formatError($original, $aliasField, $value, $rule, $args);
        }
        return $control;
    }
    /**
     * Résolution du nom d'une règle.
     *
     * @param string $rule
     * @return string
     */
    protected function getRule($rule) : string
    {
        if (strpos($rule, ':')) {
            $result = explode(':', $rule);
            return trim($result[0]);
        }
        return $rule;
    }
    /**
     * Résolution des arguments définient pour une règle
     *
     * @param string $args
     * @return array
     */
    protected function getArgs($args) : array
    {
        $arguments = [];
        if (strpos($args, ':')) {
            $result = explode(':', $args);
            $resolve = end($result);
            if (strpos($resolve, ',')) {
                $arguments = explode(',', $resolve);
            } else {
                $arguments[] = $resolve;
            }
        }
        return $arguments;
    }
    /**
     * Format les messages d'erreur
     *
     * @param string $original
     * @param string $field
     * @param string|array $value
     * @param string $rule
     * @param array $args
     * @return string Message d'erreur
     */
    protected function formatError($original, $field, $value, $rule, $args = []) : string
    {
        $search = ['{field}', '{value}'];
        $value = (is_array($value)) ? $value['name'] : $value;
        $replace = [$field, $value];
        if (!empty($args)) {
            $key = array_keys($args);
            $keys = array_map(function ($key) {
                return '{$arg' . $key . '}';
            }, $key);
            $search = array_merge($search, $keys);
            $replace = array_merge($replace, $args);
        }
        $message = $this->aliasMessageFieldRuleError[$original . '.' . $rule] ??
            $this->aliasMessageRuleError[$rule] ??
            $this->messagesErrors[$rule];
        return str_replace($search, $replace, $message);
    }
    /**
     * Résolution des alias donnée au champs
     *
     * @param array $data
     * @return array
     */
    protected function aliased($data) :array
    {
        foreach ($data as $key => $value) {
            $aliasing = explode(' as ', $key);
            if (isset($aliasing[1])) {
                $original = trim($aliasing[0]);
                $alias = trim($aliasing[1]);
                $this->alias[$original] = $alias;
                $data[$original] = $value;
                unset($data[$key]);
            }
        }
        return $data;
    }
    /**
     * Camelcase pour les règles
     *
     * @param string $rule
     * @return string
     */
    protected function camelRule($rule) :string
    {
        return str_replace('_', '', ucwords($rule, '_'));
    }
}
