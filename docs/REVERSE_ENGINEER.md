# Reverse-engineer des entités depuis la base de données

Utilisez la commande Symfony fournie pour générer des entités basiques (avec attributs Doctrine) à partir des tables existantes :

php bin/console app:doctrine:reverse-engineer [--force]

Notes et limitations :
- La commande se connecte à la base indiquée par `DATABASE_URL` (.env).
- Les types ENUM PostgreSQL personnalisés sont automatiquement mappés en `string` (le mapping est effectif uniquement pour la génération automatique).
- Les relations (FK) ne sont pas créées automatiquement — vous devez les ajouter manuellement (annotations/attributs `#[ORM\ManyToOne]`, etc.).
- Si des fichiers d'entité existent déjà, utilisez `--force` pour les écraser.

Si vous souhaitez améliorer la génération (ex. mappage des enums vers des classes PHP, création automatique des relations), je peux étendre la commande pour votre schéma spécifique.
