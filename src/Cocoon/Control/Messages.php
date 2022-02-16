<?php

namespace Cocoon\Control;

/**
 * Gestion des messages d'erreur
 */
class Messages
{
    /**
     * Liste des messages
     *
     * @var array
     */
    private $messages = [];
    /**
     * Constructeur de la classe
     *
     * @param array $messages
     */
    public function __construct($messages)
    {
        $this->messages = $messages;
    }
    /**
     * Retourne la liste des messages
     *
     * @return array
     */
    public function all() :array
    {
        return array_values($this->messages);
    }
    /**
     * Retourne le message pour un champ donné
     *
     * @param string $key
     * @return string
     */
    public function get($key) :string
    {
        if ($this->has($key)) {
            return $this->messages[$key];
        }
    }
    /**
     * Vérifit si le champ indiqué existe
     *
     * @param string $key
     * @return boolean
     */
    public function has($key) :bool
    {
        return isset($this->messages[$key]);
    }
}
