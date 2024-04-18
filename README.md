# PlayOFF

Voici notre projet Transversal de l'élaboration d'une application qui permet à n'importe qui de créer des tournois entre amis dans n'importe quelle discipline (sport, activité, jeux, etc...)

## Requirements

- PHP >= 8.2
- Composer
- Symfony CLI (optionnel mais conseillé)
- NodeJS

## Installation

### Cloner le répertoire

```bash
git clone git@github.com:Sam-rst/PlayOFF.git
cd PlayOFF
```

### Installer les dépendances Composer et NodeJS

```bash
composer install
npm install
```

### Se connecter à la base de données

1. Copier le fichier [.env](.env) dans un nouveau fichier [.env.local](.env.local)
```bash
cp .env .env.local
```

2. Ouvrir le fichier [.env.local](.env.local) puis commenter la ligne 29, et décommentez la ligne 27 et y mettre les identifiants de connection à la database en localhost :
```txt
DATABASE_URL="mysql://user:password@localhost:3306/play_off?serverVersion=8.0.32&charset=utf8mb4"
```

3. Création de la base de données (SI vous ne l'avez pas créée de base):
```bash
symfony console doctrine:database:create
```

4. Création des migrations :
```bash
symfony console doctrine:migrations:migrate
```