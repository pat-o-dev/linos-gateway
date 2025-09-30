# linos-gateway

Petit projet de gateway api e-commerce pour fonctionner avec [linos-front](https://github.com/pat-o-dev/linos-front)) et remplacer [fakestoreapi](https://fakestoreapi.com/docs)

---

## Stack

- **Symfony** – framework php


---

## Installation

1. **Cloner le projet**
```
git clone <url-du-repo>
cd sf-small-shop
```

2. **Installer les dépendances et Symfony**
```
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

3. **Générer les fausses données avec les factories**
```
php bin/console foundry:load-fixtures 
```

## Documentation

- [Symfony](https://symfony.com/doc/)
- [Bundle Factory](https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#installation)

