# 🌾 Cantine Verte - Backend

API REST pour la plateforme de vente de produits locaux aux cantines scolaires.

## Prérequis

- Docker & Docker Compose
- PHP 8.2+ (si exécution locale sans Docker)
- Composer (si exécution locale sans Docker)

## Installation

### 1. Cloner le projet
```bash
git clone https://github.com/LamourMarine/app-mla-backend.git
cd app-mla-backend
```

### 2. Démarrer avec Docker
```bash
docker compose up -d
```

Les services suivants seront lancés :
- **API** : http://localhost:8000
- **PostgreSQL** : localhost:5432
- **pgAdmin** : http://localhost:5050

### 3. Configurer l'environnement (si exécution locale sans Docker)

Créer un fichier `.env.local` :
```env
DATABASE_URL="postgresql://app:password@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase
CORS_ALLOW_ORIGIN=http://localhost:5173
```

### 4. Générer les clés JWT
```bash
# Avec Docker
docker compose exec app php bin/console lexik:jwt:generate-keypair

# Sans Docker
php bin/console lexik:jwt:generate-keypair
```

### 5. Créer la base de données et exécuter les migrations
```bash
# Avec Docker
docker compose exec app php bin/console doctrine:database:create
docker compose exec app php bin/console doctrine:migrations:migrate

# Sans Docker
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Charger les données de test (optionnel, local uniquement)
```bash
# Avec Docker
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

# Sans Docker
php bin/console doctrine:fixtures:load --no-interaction
```

## Endpoints principaux

### Authentification
- `POST /api/register` - Inscription (producteur ou structure)
- `POST /api/login_check` - Connexion (retourne un JWT)

### Produits
- `GET /api/products` - Liste des produits
- `GET /api/products/{id}` - Détail d'un produit
- `POST /api/products` - Créer un produit (producteur uniquement)
- `PUT /api/products/{id}` - Modifier un produit (producteur uniquement)
- `DELETE /api/products/{id}` - Supprimer un produit (producteur uniquement)

### Producteurs
- `GET /api/producers` - Liste des producteurs actifs
- `GET /api/producers/deactivated` - Liste des producteurs désactivés (admin)
- `PATCH /api/producers/{id}/deactivate` - Désactiver un producteur (admin)
- `PATCH /api/producers/{id}/activate` - Réactiver un producteur (admin)

### Validation des producteurs (Admin)
- `GET /api/admin/producers/pending` - Liste des producteurs en attente
- `PATCH /api/admin/producers/{id}/approve` - Approuver un producteur
- `PATCH /api/admin/producers/{id}/reject` - Rejeter un producteur

### Commandes
- `POST /api/orders` - Créer une commande
- `GET /api/orders` - Mes commandes
- `GET /api/orders/{id}` - Détail d'une commande

### Catégories & Unités
- `GET /api/categories` - Liste des catégories
- `GET /api/units` - Liste des unités

## 🛠️ Technologies

- **Framework** : Symfony 7
- **Serveur** : FrankenPHP (Docker)
- **Base de données** : PostgreSQL 15
- **Authentification** : LexikJWTAuthenticationBundle
- **ORM** : Doctrine
- **Fixtures** : DoctrineFixturesBundle avec Faker

## Rôles utilisateurs

- `ROLE_USER` : Utilisateur de base (par défaut)
- `ROLE_PRODUCTEUR` : Producteur (peut gérer ses produits, nécessite validation admin)
- `ROLE_STRUCTURE` : Structure/cantine (peut passer des commandes)
- `ROLE_ADMIN` : Administrateur (validation des producteurs, gestion globale)

## Statuts des producteurs

Les producteurs passent par un système de validation :
- `pending` : En attente de validation par un admin
- `approved` : Approuvé, peut se connecter et vendre
- `rejected` : Refusé par l'admin

## Structure du projet
```
src/
├── Controller/       # Contrôleurs API
├── Entity/          # Entités Doctrine
├── Repository/      # Repositories
├── DataFixtures/    # Données de test (local uniquement)
└── EventListener/   # Listeners (validation JWT, etc.)
```

## Comptes de test (après fixtures - local uniquement)

Les fixtures génèrent automatiquement :
- Un compte **admin** avec email et mot de passe aléatoires
- Plusieurs **producteurs** de test
- Plusieurs **structures** de test
- Des produits dans différentes catégories

Consultez les logs lors du chargement des fixtures pour voir les identifiants générés.

## Notes techniques

### CORS
- En développement : accepte les requêtes depuis `http://localhost:5173` (Vite)
- En production : configuré pour `https://cantineverte.netlify.app`

### Images produits
Les images sont stockées dans `public/images/` avec la structure suivante :
```
public/images/
├── fruits/
├── legumes/
└── produits_laitiers/
```

### Sérialisation
Les groupes de sérialisation sont définis dans les entités pour contrôler les données exposées par l'API.

## Déploiement

### Production
- **API** : https://app-mla-backend.onrender.com
- **Base de données** : Supabase (PostgreSQL)
- **Frontend** : https://cantineverte.netlify.app

### Variables d'environnement en production
Configurez les variables suivantes sur Render :
- `DATABASE_URL` : URL de connexion Supabase
- `JWT_SECRET_KEY` : Clé privée JWT
- `JWT_PUBLIC_KEY` : Clé publique JWT
- `JWT_PASSPHRASE` : Passphrase JWT
- `CORS_ALLOW_ORIGIN` : https://cantineverte.netlify.app

## Debugging

### Accéder à pgAdmin (local)
- URL : http://localhost:5050
- Email : admin@admin.com
- Password : admin

### Logs Docker
```bash
docker compose logs -f app
```

### Redémarrer les services
```bash
docker compose restart
```