# 🌾 Cantine Verte - Backend

REST API for connecting local producers with school canteens. This platform enables producers to list their local products and allows canteens to place orders directly.

## Prerequisites

- Docker & Docker Compose
- PHP 8.2+ (if running locally without Docker)
- Composer (if running locally without Docker)

## Installation

### 1. Clone the project
```bash
git clone https://github.com/LamourMarine/app-mla-backend.git
cd app-mla-backend
```

### 2. Start with Docker
```bash
docker compose up -d
```

The following services will be started:
- **API**: http://localhost:8000
- **PostgreSQL**: localhost:5432
- **pgAdmin**: http://localhost:5050

### 3. Configure environment (if running locally without Docker)

Create a `.env.local` file:
```env
DATABASE_URL="postgresql://app:password@127.0.0.1:5432/app?serverVersion=15&charset=utf8"
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase
CORS_ALLOW_ORIGIN=http://localhost:5173
```

### 4. Generate JWT keys
```bash
# With Docker
docker compose exec app php bin/console lexik:jwt:generate-keypair

# Without Docker
php bin/console lexik:jwt:generate-keypair
```

### 5. Create database and run migrations
```bash
# With Docker
docker compose exec app php bin/console doctrine:database:create
docker compose exec app php bin/console doctrine:migrations:migrate

# Without Docker
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 6. Load test data (optional, local only)
```bash
# With Docker
docker compose exec app php bin/console doctrine:fixtures:load --no-interaction

# Without Docker
php bin/console doctrine:fixtures:load --no-interaction
```

## Main Endpoints

### Authentication
- `POST /api/register` - Register (producer or structure)
- `POST /api/login_check` - Login (returns a JWT)

### Products
- `GET /api/products` - List products
- `GET /api/products/{id}` - Get product details
- `POST /api/products` - Create a product (producer only)
- `PUT /api/products/{id}` - Update a product (producer only)
- `DELETE /api/products/{id}` - Delete a product (producer only)

### Producers
- `GET /api/producers` - List active producers
- `GET /api/producers/deactivated` - List deactivated producers (admin)
- `PATCH /api/producers/{id}/deactivate` - Deactivate a producer (admin)
- `PATCH /api/producers/{id}/activate` - Reactivate a producer (admin)

### Producer Validation (Admin)
- `GET /api/admin/producers/pending` - List pending producers
- `PATCH /api/admin/producers/{id}/approve` - Approve a producer
- `PATCH /api/admin/producers/{id}/reject` - Reject a producer

### Orders
- `POST /api/orders` - Create an order
- `GET /api/orders` - Get my orders
- `GET /api/orders/{id}` - Get order details

### Categories & Units
- `GET /api/categories` - List categories
- `GET /api/units` - List units

## Technologies

- **Framework**: Symfony 7
- **Server**: FrankenPHP (Docker)
- **Database**: PostgreSQL 15
- **Authentication**: LexikJWTAuthenticationBundle
- **ORM**: Doctrine
- **Fixtures**: DoctrineFixturesBundle with Faker

## Related Projects

- [Frontend Application](https://github.com/LamourMarine/app-mla-frontend) - React TypeScript client

## User Roles

- `ROLE_USER`: Base user (default)
- `ROLE_PRODUCTEUR`: Producer (can manage their products, requires admin validation)
- `ROLE_STRUCTURE`: Structure/canteen (can place orders)
- `ROLE_ADMIN`: Administrator (producer validation, global management)

## Producer Status

Producers go through a validation system:
- `pending`: Awaiting admin validation
- `approved`: Approved, can login and sell
- `rejected`: Rejected by admin

## Project Structure
```
src/
├── Controller/       # API Controllers
├── Entity/          # Doctrine Entities
├── Repository/      # Repositories
├── DataFixtures/    # Test data (local only)
└── EventListener/   # Listeners (JWT validation, etc.)
```

## Test Accounts (after fixtures - local only)

Fixtures automatically generate:
- An **admin** account with random email and password
- Multiple test **producers**
- Multiple test **structures**
- Products in various categories

Check the logs when loading fixtures to see the generated credentials.

## Technical Notes

### CORS
- Development: accepts requests from `http://localhost:5173` (Vite)
- Production: configured for `https://cantineverte.netlify.app`

### Product Images
Images are stored in `public/images/` with the following structure:
```
public/images/
├── fruits/
├── legumes/
└── produits_laitiers/
```

### Serialization
Serialization groups are defined in entities to control data exposed by the API.

## Deployment

### Production
- **API**: https://app-mla-backend.onrender.com
- **Database**: Supabase (PostgreSQL)
- **Frontend**: [https://cantineverte.netlify.app](https://cantineverte.netlify.app)

### Production Environment Variables
Configure the following variables on Render:
- `DATABASE_URL`: Supabase connection URL
- `JWT_SECRET_KEY`: JWT private key
- `JWT_PUBLIC_KEY`: JWT public key
- `JWT_PASSPHRASE`: JWT passphrase
- `CORS_ALLOW_ORIGIN`: https://cantineverte.netlify.app

## Debugging

### Access pgAdmin (local)
- URL: http://localhost:5050
- Email: admin@admin.com
- Password: admin

### Docker Logs
```bash
docker compose logs -f app
```

### Restart Services
```bash
docker compose restart
```

## Author

**Marine Lamour** - Backend Developer   
[Portfolio](https://ml-dev.netlify.app)

## License

This project is open source and available under the MIT License.
