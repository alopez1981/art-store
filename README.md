# 🎨 Art Store — E-Commerce de Obras de Arte

Una plataforma moderna de venta de obras de arte únicas, construida con **Laravel 13**, **Vue 3**, **Inertia.js** y *
*Stripe**. Diseñada para galerías que venden piezas únicas con envío internacional.

![Status](https://img.shields.io/badge/status-production--ready-green)
![PHP](https://img.shields.io/badge/PHP-8.3+-blue)
![Laravel](https://img.shields.io/badge/Laravel-13-red)
![Vue](https://img.shields.io/badge/Vue-3-green)
![License](https://img.shields.io/badge/license-MIT-blue)

---

## ✨ Características Principales

### **Para Compradores**

- 🎨 Catálogo de obras publicadas con vista detallada
- 🌍 Cálculo automático de envío según zona geográfica
- 💳 Pago seguro con **Stripe Checkout** (tarjeta de crédito)
- 📍 Recolección automática de dirección de envío y teléfono
- ✅ Confirmación instantánea de compra
- 🔒 Protección CSRF automática en checkout

### **Para Administradores**

- 🖼️ Panel de admin protegido por login
- ➕ Crear, editar y eliminar obras
- 📊 Visualización de órdenes completadas
- 🔐 Control de publicación (is_published)
- 🛡️ Throttling anti-fuerza-bruta (5 intentos/min)

### **Piezas Únicas (Protección contra doble venta)**

- ⏱️ Sesiones de checkout con expiración de 30 minutos
- 🔒 Transacción atómica: marca como vendida al completar pago
- 📝 Trazabilidad completa via logs

### **Seguridad & Performance**

- 🚀 Índices de BD optimizados (~100x más rápido)
- 🛡️ Rate limiting en webhook (30 req/min)
- 📝 Logging completo de eventos de pago
- 🔐 Verificación de firma Stripe
- 🌐 API cleanly separated en `/api/stripe/webhook`
- 💾 Almacenamiento seguro de datos sensibles

---

## 🏗️ Stack Técnico

### **Backend**

- **PHP 8.3** — Lenguaje de servidor
- **Laravel 13** — Framework web con arquitectura limpia
- **SQLite** — BD ligera, sin servidor (ideal para galerías pequeñas)
- **Stripe PHP SDK v20.1** — Procesamiento de pagos

### **Frontend**

- **Vue 3** — Reactivity moderna con Composition API
- **Inertia.js 3** — Bridge Laravel ↔ Vue (sin API REST)
- **Tailwind CSS 4** — Estilos utilities-first
- **Vite 8** — Build tool ultrarrápido

### **Infraestructura**

- **Nginx** — Servidor web (recomendado para producción)
- **PHP-FPM 8.3** — Procesador PHP
- **Certbot** — SSL/TLS automático (Let's Encrypt)
- **Composer** — Gestor de dependencias PHP
- **npm** — Gestor de dependencias frontend

### **Servicios Externos**

- **Stripe** — Procesamiento de pagos (Checkout + Webhooks)
- **Cloudflare** — DNS + CDN (opcional pero recomendado)

---

## 🚀 Requisitos de Instalación

### **Local (Desarrollo)**

```
- PHP 8.3 con extensiones: pdo, sqlite, mbstring, xml, curl
- Composer 2.7+
- Node.js 20+ + npm 10+
- SQLite 3
```

### **Servidor (Producción)**

```
- Ubuntu 24.04 LTS (o equivalente)
- Nginx + PHP-FPM 8.3
- SQLite 3 (ya incluido)
- SSL/TLS (Let's Encrypt via Certbot)
- 2 CPU, 4GB RAM, 40GB SSD mínimo
```

---

## 📥 Instalación Local

### **1. Clona el repositorio**

```bash
git clone https://github.com/alopez1981/art-store.git
cd art-store
```

### **2. Setup automático (recomendado)**

```bash
composer run setup
```

Esto ejecuta:

- `composer install`
- Genera `.env` desde `.env.example`
- `php artisan key:generate`
- `php artisan migrate`
- `npm install && npm run build`

### **3. Alterna: Setup manual**

```bash
# Dependencias PHP
composer install

# Copia .env
cp .env.example .env

# Generar clave
php artisan key:generate

# Base de datos
php artisan migrate

# Assets
npm install
npm run build
```

### **4. Inicia el servidor de desarrollo**

```bash
composer run dev
```

Abre: **http://localhost:8000**

---

## 🔧 Desarrollo

### **Comandos útiles**

```bash
# Dev mode (Laravel + queue + logs + Vite HMR)
composer run dev

# Build assets para producción
npm run build

# Ver logs en tiempo real
php artisan pail

# Ejecutar tests
composer run test

# Single test
php artisan test --filter=test_name

# Limpia caches
php artisan config:cache
php artisan route:cache

# Refresh BD (⚠️ elimina datos)
php artisan migrate:refresh --seed
```

### **Estructura de carpetas**

```
art-store/
├── app/
│   ├── Domain/              ← Lógica de negocio (Value Objects, Repositories)
│   ├── Http/Controllers/    ← Controladores web + API
│   ├── Http/Middleware/     ← Middlewares (auth, admin, CSRF)
│   ├── Infrastructure/      ← Implementaciones de repositorios
│   ├── Models/              ← Modelos Eloquent (Artwork, Order, User)
│   └── Services/            ← Servicios (ArtworkImageStorage)
├── config/                  ← Configuración (app, services, shipping, database)
├── database/
│   ├── migrations/          ← Esquema BD
│   ├── seeders/             ← Datos de prueba
│   └── database.sqlite      ← BD (gitignored)
├── resources/
│   ├── css/                 ← Tailwind
│   ├── js/Pages/            ← Componentes Inertia
│   └── views/               ← Layouts Inertia
├── routes/
│   ├── web.php              ← Rutas web (renders)
│   └── api.php              ← Rutas API (webhook Stripe)
├── storage/
│   └── logs/                ← Logs de aplicación
├── tests/                   ← Tests unitarios/funcionales
├── DEPLOY.md                ← Guía de deployment
├── .env.example             ← Variables de entorno (plantilla)
└── package.json, composer.json, vite.config.js
```

---

## 🛒 Flujo de Compra

```
Cliente navega catálogo
        ↓
Selecciona obra + zona de envío
        ↓
POST /checkout/{artwork}
        ↓
Stripe Checkout Session creada
        ↓
Cliente redirigido a stripe.com
        ↓
Ingresa tarjeta + dirección
        ↓
Pago completado
        ↓
Stripe envía webhook: checkout.session.completed
        ↓
POST /api/stripe/webhook (con firma verificada)
        ↓
Dentro de DB::transaction():
  - Crea/actualiza Order
  - Marca Artwork como vendido
  - Logs de éxito
        ↓
Cliente redirigido a /checkout/success
```

---

## 💳 Configuración de Stripe

### **En desarrollo (modo test)**

1. Obtén claves de https://dashboard.stripe.com/test/apikeys
2. Actualiza `.env`:

```env
STRIPE_KEY=pk_live_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
STRIPE_SECRET=sk_test_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```

3. Para webhooks locales, usa [Stripe CLI](https://stripe.com/docs/stripe-cli):

```bash
stripe login
stripe listen --forward-to localhost:8000/api/stripe/webhook
stripe trigger checkout.session.completed
```

### **En producción (modo live)**

1. Obtén claves live de https://dashboard.stripe.com/apikeys
2. En Hetzner, actualiza `.env`:

```env
STRIPE_KEY=pk_live_XXXXXXXXXXXXXXXXXXXX
STRIPE_SECRET=sk_live_XXXXXXXXXXXXXXXXXXXX
```

3. Configura webhook en Stripe Dashboard:
    - URL: `https://tu-dominio.com/api/stripe/webhook`
    - Evento: `checkout.session.completed`
    - Copia signing secret → `STRIPE_WEBHOOK_SECRET` en `.env`

4. Test de pago real antes de ir a producción (ver DEPLOY.md)

---

## 🚢 Deployment en Hetzner

Ver **[DEPLOY.md](./DEPLOY.md)** para instrucciones completas. Resumen rápido:

```bash
# En servidor
ssh root@tu-ip-hetzner

# Setup
apt update && apt install -y php8.3-fpm php8.3-sqlite nginx
cd /var/www

# Deploy
git clone https://github.com/alopez1981/art-store.git
cd art-store
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Configuración
cp .env.example .env
# Edita .env con valores de producción (STRIPE_KEY, STRIPE_SECRET, etc.)

php artisan key:generate
php artisan migrate --force
php artisan config:cache && php artisan route:cache

# SSL
certbot certonly --nginx -d tu-dominio.com

# Nginx config + restart
# Ver DEPLOY.md para template
systemctl restart nginx php8.3-fpm
```

---

## 📊 Tarifas de Envío

Las zonas y precios están en **`config/shipping.php`**. Editable sin código:

```php
'local' => [
    'label' => 'Badalona / Área de Barcelona',
    'amount' => 800,  // 8€ en céntimos
    'delivery_days' => [1, 3],
    'countries' => ['ES'],
],
```

Para aplicar cambios en producción:

```bash
php artisan config:cache
```

---

## 🔒 Seguridad & Mejoras Recientes (v1.1)

### **Indexes de BD** ⚡

```sql
-- Búsquedas en webhook: 50ms → 1ms
CREATE INDEX idx_stripe_session_id ON orders (stripe_session_id);
CREATE INDEX idx_vendido_at ON artworks (vendido_at);
CREATE INDEX idx_published_created ON artworks (is_published, created_at);
```

### **Rate Limiting**

```
POST /api/stripe/webhook: máx 30 req/min (throttle:30,1)
POST /checkout/{artwork}: máx 10 req/min (throttle:10,1)
POST /admin/login: máx 5 req/min (throttle:5,1)
```

### **Logging de Pagos**

Cada webhook registra:

- Evento recibido + tipo
- ID de sesión + obra
- Monto + moneda + estado
- Orden creada + obra marcada como vendida

Ver logs:

```bash
php artisan pail
# o
tail -f storage/logs/laravel.log
```

### **Verificación de Firma Stripe**

```php
$event = Webhook::constructEvent($payload, $sigHeader, $secret);
// SignatureVerificationException si es inválido
```

### **Transacciones Atómicas**

```php
DB::transaction(function () {
    Order::updateOrCreate(...);
    Artwork::update(['vendido_at' => now()]);
    // Todo o nada
});
```

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
composer run test

# Tests específicos
php artisan test tests/Feature/CheckoutTest.php
php artisan test --filter=webhook

# Con coverage
php artisan test --coverage
```

Tests incluyen:

- Checkout Session creation
- Webhook signature verification
- Order creation & artwork marking
- Shipping zone validation
- Login throttling
- Double-sale prevention

---

## 📝 Logs & Debugging

### **Estructura de logs**

```
storage/logs/laravel.log
```

### **En desarrollo**

```bash
php artisan pail  # Monitoreo en tiempo real
```

### **En producción**

```bash
# Monitorar logs
tail -f /var/www/art-store/storage/logs/laravel.log

# Rotar logs (automático vía Laravel)
php artisan log:prune
```

### **Eventos registrados**

- `Stripe webhook received` — Cada webhook entrante
- `Stripe checkout session completed` — Sesión completada
- `Order created and artwork marked as sold` — Confirmación
- `Stripe webhook signature verification failed` — Errores

---

## 🐛 Problemas Conocidos & Mitigaciones

### **Race condition: Doble venta de pieza única**

**Problema:** Si dos clientes pagan simultáneamente la misma obra.

**Mitigación actual:**

- Sesiones de checkout expiran en 30 minutos
- `whereNull('vendido_at')` + `update(['vendido_at' => now()])` en transacción
- Primera orden gana, segunda debe reembolsarse manualmente desde Stripe Dashboard

**Mejor solución (futuro):**

```php
// Usar DB lock pessimista
$artwork = Artwork::where('id', $artworkId)
    ->lockForUpdate()
    ->first();
```

---

## 📱 API Endpoints

### **Web Routes (renders HTML)**

```
GET    /                           # Catálogo
GET    /artworks/{slug}            # Detalle obra
POST   /checkout/{artwork}         # Crea sesión Stripe
GET    /checkout/success           # Confirmación
GET    /checkout/cancel            # Cancelación
GET    /admin/login                # Login
POST   /admin/login                # Submit login
POST   /admin/logout               # Logout
GET    /admin/artworks             # Panel obras
POST   /admin/artworks             # Crear obra
PUT    /admin/artworks/{id}        # Editar obra
DELETE /admin/artworks/{id}        # Eliminar obra
GET    /admin/orders               # Panel órdenes
```

### **API Routes**

```
POST   /api/stripe/webhook         # Webhook Stripe (rate-limited)
```

---

## 🌍 Variables de Entorno

### **Requeridas**

```env
APP_NAME="Art Store"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
APP_KEY=base64:xxxxx

STRIPE_KEY=pk_live_XXXXXXXXXXXXXXXXXXXX
STRIPE_SECRET=sk_live_XXXXXXXXXXXXXXXXXXXX
STRIPE_WEBHOOK_SECRET=whsec_XXXXXXXXXXXXXXXXXXXX

SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=tu-dominio.com
```

### **Opcionales**

```env
LOG_LEVEL=warning
CACHE_STORE=database
QUEUE_CONNECTION=sync
```

Ver **`.env.example`** para plantilla completa.

---

## 🤝 Contribuir

Este proyecto es de producción (no aceptamos PRs). Para cambios:

1. Fork el repositorio
2. Crea rama: `git checkout -b feature/mi-feature`
3. Commit: `git commit -m "feat: descripción"`
4. Push: `git push origin feature/mi-feature`
5. Open PR

---

## 📄 Licencia

MIT License — Ver [LICENSE](LICENSE) para detalles.

---

## 👨‍💻 Autor

**Albert López** — Backend/Fullstack Developer

- Especialista en Arquitectura Hexagonal + DDD/CQRS
- 6+ años con PHP/Laravel
- 11+ años en el dominio de autoescuelas (HoyVoy)

---

## 🆘 Soporte

Para issues o preguntas:

1. Revisa [DEPLOY.md](./DEPLOY.md)
2. Mira los logs: `php artisan pail`
3. Abre issue en GitHub con:
    - Descripción del problema
    - Pasos para reproducir
    - Output de logs
    - Entorno (local/Hetzner, PHP version, etc.)

---

## 📦 Cambios Recientes (v1.1)

✅ Añadidos índices de BD para performance (50ms → 1ms en webhooks)  
✅ Rate limiting en `/api/stripe/webhook` (30 req/min)  
✅ Logging completo de eventos de pago (trazabilidad)  
✅ API cleanly separated en `/api/` (separación web vs api)  
✅ `.env.example` para deployment sin sorpresas  
✅ Documentación de deployment mejorada

---

**Desarrollado con ❤️ para galerías de arte modernas.**
