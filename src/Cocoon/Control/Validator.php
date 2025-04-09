<?php
declare(strict_types=1);

namespace Cocoon\Control;

use Closure;
use Attribute;
use Cocoon\Control\Features\Rules;
use Cocoon\Control\Features\UploadFile;
use Cocoon\Control\Exceptions\ValidationException;
use Cocoon\Control\Exceptions\RuleNotFoundException;
use Cocoon\Control\Exceptions\LanguageFileNotFoundException;

/**
 * Classe gérant la validation des données
 * 
 * Cette classe permet de valider des données selon des règles prédéfinies
 * ou personnalisées, avec support multilingue des messages d'erreur.
 */
#[Attribute(Attribute::TARGET_CLASS)]
class Validator
{
    use Rules, UploadFile;

    /**
     * Cache des règles compilées
     */
    private array $compiledRules = [];

    /**
     * Cache des messages d'erreur formatés
     */
    private array $errorCache = [];

    /**
     * Liste des messages par défaut (classé par langue) ex fr.php
     */
    private array $langMessages = [];

    /**
     * Données à valider
     */
    private array $data = [];

    /**
     * Messages d'erreur par règle
     */
    private array $messagesErrors = [];

    /**
     * Personnalisation des messages d'erreur par règle
     */
    private array $aliasMessageRuleError = [];

    /**
     * Personnalisation des messages d'erreur par champs et règle
     */
    public array $aliasMessageFieldRuleError = [];

    /**
     * Listes des messages d'erreur retournées
     */
    private array $errors = [];

    /**
     * Liste des alias pour les champs
     */
    private array $alias = [];

    /**
     * Liste des règles ajoutées
     */
    private array $rules = [];

    /**
     * Langue des messages
     */
    private string $lang;

    /**
     * Constructeur de la classe
     *
     * @param string $lang La langue des messages (par défaut 'fr')
     * @throws LanguageFileNotFoundException Si le fichier de langue n'existe pas
     */
    public function __construct(string $lang = 'fr')
    {
        $this->lang = $lang;
        $DS = DIRECTORY_SEPARATOR;
        $langFile = __DIR__ . $DS . 'messages' . $DS . $lang . '.php';
        
        if (!file_exists($langFile)) {
            throw new LanguageFileNotFoundException($langFile);
        }
        
        $this->langMessages = require $langFile;
        $this->data = [];
    }

    /**
     * Définit les données à valider
     *
     * @param array $data Les données à valider
     * @return self
     */
    public function data(array $data): self
    {
        $this->data = $data;
        $this->errorCache = []; // Réinitialise le cache des erreurs
        return $this;
    }

    /**
     * Ajoute une règle personnalisée
     *
     * @param string $ruleName Nom de la règle
     * @param Closure $callback Fonction de validation
     * @param string $message Message d'erreur personnalisé
     * @return self
     */
    public function addRule(string $ruleName, Closure $callback, string $message): self
    {
        $this->rules[$ruleName] = $callback;
        $this->aliasMessageRuleError[$ruleName] = $message;
        return $this;
    }

    /**
     * Ajoute plusieurs règles
     *
     * @param array<array{name: string, callback: Closure, message: string}> $rules Liste des règles à ajouter
     * @return self
     */
    public function addRules(array $rules = []): self
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
     * @param string $langFilePath Chemin vers le fichier de langue
     * @return self
     * @throws LanguageFileNotFoundException Si le fichier de langue n'existe pas
     */
    public function setLangFile(string $langFilePath): self
    {
        if (!file_exists($langFilePath)) {
            throw new LanguageFileNotFoundException($langFilePath);
        }
        
        $this->langMessages = require $langFilePath;
        return $this;
    }

    /**
     * Validation des données
     *
     * @param array<string, string> $rules Listes des règles pour les données à valider
     * @param array<string, string> $messages Listes des messages personnalisés
     * @return self
     * @throws ValidationException Si la validation échoue
     */
    public function validate(array $rules, array $messages = []): self
    {
        if (!empty($messages)) {
            $this->addMessages($messages);
        }

        $this->messagesErrors = $this->langMessages;
        $rules = $this->aliased($rules);
        $validate = [];

        // Compile les règles si nécessaire
        foreach ($rules as $key => $value) {
            if (!isset($this->compiledRules[$key])) {
                $this->compiledRules[$key] = array_map(
                    fn($rule) => ['rule' => $this->getRule($rule), 'args' => $this->getArgs($rule)],
                    explode('|', $value)
                );
            }
            $validate[$key] = $this->data[$key] ?? null;
        }

        foreach ($validate as $key => $value) {
            foreach ($this->compiledRules[$key] as $rule) {
                $passes = $this->verify($key, $value, $rule['rule'], $rule['args']);
                if (!$passes) {
                    break;
                }
            }
        }

        if ($this->fails()) {
            throw new ValidationException($this->errors);
        }

        return $this;
    }

    /**
     * Vérifie s'il y a des messages d'erreur
     *
     * @return bool
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Retourne les messages d'erreurs
     *
     * @return Messages Instance de Messages::class
     */
    public function errors(): Messages
    {
        return new Messages($this->errors);
    }

    /**
     * Définit des messages personnalisés
     *
     * @param array<string, string> $messages Messages personnalisés
     * @return self
     */
    public function addMessages(array $messages = []): self
    {
        foreach ($messages as $key => $value) {
            $this->aliasMessageFieldRuleError[$key] = $value;
        }
        return $this;
    }

    /**
     * Vérifie une règle de validation
     *
     * @param string $field Nom du champ
     * @param mixed $value Valeur à valider
     * @param string $rule Nom de la règle
     * @param array $args Arguments de la règle
     * @return bool
     * @throws RuleNotFoundException Si la règle n'existe pas
     */
    protected function verify(string $field, mixed $value, string $rule, array $args): bool
    {
        $methodRule = $this->rules[$rule] ?? [$this, 'rule' . $this->camelRule($rule)];
        
        if (!is_callable($methodRule)) {
            throw new RuleNotFoundException($rule);
        }
        
        $control = call_user_func_array($methodRule, [$value, $args, $field]);
        
        if (!$control) {
            $original = $field;
            $aliasField = $this->alias[$field] ?? $field;
            $this->errors[$field] = $this->formatError($original, $aliasField, $value, $rule, $args);
        }
        
        return $control;
    }

    /**
     * Résout le nom d'une règle
     *
     * @param string $rule Règle à résoudre
     * @return string
     */
    protected function getRule(string $rule): string
    {
        if (str_contains($rule, ':')) {
            $result = explode(':', $rule);
            return trim($result[0]);
        }
        return $rule;
    }

    /**
     * Résout les arguments définis pour une règle
     *
     * @param string $args Arguments à résoudre
     * @return array
     */
    protected function getArgs(string $args): array
    {
        $arguments = [];
        if (str_contains($args, ':')) {
            $result = explode(':', $args);
            $resolve = end($result);
            if (str_contains($resolve, ',')) {
                $arguments = explode(',', $resolve);
            } else {
                $arguments[] = $resolve;
            }
        }
        return $arguments;
    }

    /**
     * Formate les messages d'erreur
     *
     * @param string $original Nom original du champ
     * @param string $field Nom du champ (avec alias)
     * @param mixed $value Valeur du champ
     * @param string $rule Nom de la règle
     * @param array $args Arguments de la règle
     * @return string Message d'erreur formaté
     */
    protected function formatError(string $original, string $field, mixed $value, string $rule, array $args = []): string
    {
        $cacheKey = "{$original}.{$rule}." . implode(',', $args);
        
        if (isset($this->errorCache[$cacheKey])) {
            return $this->errorCache[$cacheKey];
        }

        $search = ['{field}', '{value}'];
        $value = (is_array($value)) ? $value['name'] : $value;
        $replace = [$field, $value];
        
        if (!empty($args)) {
            $key = array_keys($args);
            $keys = array_map(fn($key) => '{$arg' . $key . '}', $key);
            $search = array_merge($search, $keys);
            $replace = array_merge($replace, $args);
        }
        
        $message = $this->aliasMessageFieldRuleError[$original . '.' . $rule] ??
            $this->aliasMessageRuleError[$rule] ??
            $this->messagesErrors[$rule];
            
        $formatted = str_replace($search, $replace, $message);
        $this->errorCache[$cacheKey] = $formatted;
        
        return $formatted;
    }

    /**
     * Résout les alias donnés aux champs
     *
     * @param array<string, string> $data Données avec alias
     * @return array<string, string>
     */
    protected function aliased(array $data): array
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
     * Convertit une règle en camelCase
     *
     * @param string $rule Règle à convertir
     * @return string
     */
    protected function camelRule(string $rule): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $rule)));
    }
}
