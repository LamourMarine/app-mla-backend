# 🌾 Cantine Verte - Backend

API REST pour la plateforme de vente de produits locaux aux cantines scolaires.

## 📋 Prérequis

- PHP 8.2+
- Composer
- PostgreSQL

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/LamourMarine/app-mla-backend.git
cd app-mla-backend
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

Créer un fichier `.env.local` :

```env
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/cantine_verte?serverVersion=15&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=
```

### 4. Générer les clés JWT

```bash
php bin/console lexik:jwt:generate-keypair
```

### 5. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Charger les données de test

```bash
php bin/console doctrine:fixtures:load --no-interaction
```

### 7. Lancer le serveur

```bash
symfony server:start
```

L'API est accessible sur `http://localhost:8000`

## 🔑 Endpoints principaux

### Authentification

- `POST /api/register` - Inscription
- `POST /api/login` - Connexion (retourne un JWT)

### Produits

- `GET /api/products` - Liste des produits
- `GET /api/products/{id}` - Détail d'un produit
- `POST /api/products` - Créer un produit (producteur uniquement)

### Producteurs

- `GET /api/producers` - Liste des producteurs

### Commandes

- `POST /api/orders` - Créer une commande
- `GET /api/orders` - Mes commandes

## 🛠️ Technologies

- **Framework** : Symfony 7
- **Base de données** : PostgreSQL
- **Authentification** : LexikJWTAuthenticationBundle
- **ORM** : Doctrine
- **Fixtures** : DoctrineFixturesBundle avec Faker

## 👥 Rôles utilisateurs

- `ROLE_USER` : Client (par défaut)
- `ROLE_PRODUCTEUR` : Producteur (peut créer des produits)
- `ROLE_STRUCTURE` : Structure/cantine (commandes)

## 📦 Structure du projet

```
src/
├── Controller/       # Contrôleurs API
├── Entity/          # Entités Doctrine
├── Repository/      # Repositories
└── DataFixtures/    # Données de test
```

## 🧪 Comptes de test (après fixtures)

Utilisez les emails générés par Faker ou créez-en via `/api/register`

## 📝 Notes

- CORS est configuré pour accepter les requêtes depuis `localhost:5173` (Vite)
- Les groupes de sérialisation sont définis dans les entités
- Les images produits sont stockées dans `public/images/`, avec uns structure par type de produits:
- `public/images/fruits/`
- `public/images/legumes/`
- `public/images/produits_laitiers/`
