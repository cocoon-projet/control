[![Tests](https://github.com/cocoon-projet/control/actions/workflows/ci.yml/badge.svg)](https://github.com/cocoon-projet/control/actions/workflows/ci.yml) [![codecov](https://codecov.io/gh/cocoon-projet/control/graph/badge.svg?token=7693MEUK3C)](https://codecov.io/gh/cocoon-projet/control) ![License](https://img.shields.io/badge/Licence-MIT-green)

# Cocoon Control

Une librairie PHP 8 de validation de données simple et puissante.

## Prérequis

- PHP 8.0 ou supérieur
- Composer

## Installation

```bash
composer require cocoon-projet/control
```

## Utilisation

### Validation de base

```php
use Cocoon\Control\Validator;

$validator = new Validator();

$validator->validate([
    'name' => 'required',
    'email' => 'required|email',
    'age' => 'required|int|num_min:18',
    'password' => 'required|same:password_confirm',
    'password_confirm' => 'required'
]);
```

### Validation avec données personnalisées

```php
$validator->data([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => '25',
    'password' => '123456',
    'password_confirm' => '123456'
])->validate([
    'name' => 'required',
    'email' => 'required|email',
    'age' => 'required|int|num_min:18',
    'password' => 'required|same:password_confirm',
    'password_confirm' => 'required'
]);
```

### Messages d'erreur personnalisés

```php
$validator->validate([
    'name' => 'required',
    'email' => 'required|email'
], [
    'name.required' => 'Le nom est obligatoire',
    'email.email' => 'L\'adresse email n\'est pas valide'
]);
```

### Alias de champs

```php
$validator->validate([
    'user_name as nom d\'utilisateur' => 'required',
    'user_email as email' => 'required|email'
]);
```

### Règles personnalisées

```php
$validator->addRule('custom', function($value) {
    return $value === 'valid';
}, 'La valeur doit être "valid"');

$validator->validate([
    'field' => 'required|custom'
]);
```

### Gestion des erreurs

```php
try {
    $validator->validate([
        'name' => 'required',
        'email' => 'required|email'
    ]);
} catch (\Cocoon\Control\Exceptions\ValidationException $e) {
    $errors = $e->getErrors();
    // Traitement des erreurs
}
```

## Règles disponibles

- `required` : Le champ est obligatoire
- `email` : Le champ doit être une adresse email valide
- `int` : Le champ doit être un nombre entier
- `num_min:value` : Le champ doit être supérieur ou égal à la valeur spécifiée
- `num_max:value` : Le champ doit être inférieur ou égal à la valeur spécifiée
- `same:field` : Le champ doit correspondre au champ spécifié
- `alpha` : Le champ ne doit contenir que des lettres
- `al_num` : Le champ ne doit contenir que des lettres et des chiffres
- `bool` : Le champ doit être un booléen
- `date` : Le champ doit être une date valide
- `ip` : Le champ doit être une adresse IP valide

## Gestion des fichiers

La librairie supporte également la validation des fichiers uploadés :

```php
$validator->validate([
    'avatar' => 'required_file|type:jpg,gif|size:100000'
]);
```

## Tests

Pour exécuter les tests :

```bash
composer test
```

Pour générer un rapport de couverture de code :

```bash
composer test-coverage
```

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :

1. Fork le projet
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/amazing-feature`)
3. Commit vos changements (`git commit -m 'Add some amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.