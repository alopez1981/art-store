# Checklist de despliegue a producción

## 1. Entorno (.env de producción)

```dotenv
APP_ENV=production
APP_DEBUG=false                  # CRÍTICO: nunca true en producción
APP_URL=https://tudominio.com
APP_KEY=                         # php artisan key:generate si es instalación nueva

SESSION_SECURE_COOKIE=true       # cookies solo por HTTPS
SESSION_DOMAIN=tudominio.com

STRIPE_KEY=pk_live_...           # claves LIVE, no test
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...  # del endpoint live (paso 3)
```

## 2. Servidor

- HTTPS obligatorio (Let's Encrypt / Cloudflare). Stripe lo exige para webhooks live.
- Document root apuntando a `public/` (nunca a la raíz del proyecto).
- SQLite: `database/database.sqlite` fuera del document root (ya lo está) y con
  permisos de escritura para el usuario de PHP-FPM (también el directorio `storage/`).
- Queue worker activo si se usan colas: `php artisan queue:work` (supervisor/systemd).
- La carpeta `Estela/` NO se sube al servidor (ya está en .gitignore).

## 3. Stripe (dashboard, modo live)

1. Developers → Webhooks → Add endpoint: `https://tudominio.com/api/stripe/webhook`
2. Evento a escuchar: `checkout.session.completed`
3. Copiar el signing secret (`whsec_...`) al `.env` de producción.
4. Probar una compra real con tarjeta de test ANTES de activar live
   (con claves test + `stripe listen --forward-to` en local ya validado).

## 4. Comandos post-deploy

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=ArtworkSeeder --force   # solo el primer deploy
npm ci && npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 5. Verificación

```bash
php artisan test       # debe pasar todo (incluye tests de envío)
```

- Comprar una pieza de prueba: comprobar que Stripe pide dirección + teléfono,
  que el envío correcto aparece en el checkout y que la obra queda "vendida".
- Comprobar que `/admin` exige login y que el login se bloquea tras 5 intentos/min.
- Comprobar que una obra `is_published=false` devuelve 404 en la web pública.

## 6. Tarifas de envío

Las zonas y precios viven en `config/shipping.php` (origen: Badalona).
Cambiar importes ahí y ejecutar `php artisan config:cache` tras cada cambio.

## 7. Riesgo conocido (aceptado en v1)

Pieza única + checkout asíncrono: si dos personas inician el pago de la misma
obra a la vez, ambas podrían completarlo. Mitigación actual: las sesiones de
checkout caducan a los 30 min y la obra se marca vendida con el primer webhook.
Si ocurre, reembolsar el segundo pago desde el dashboard de Stripe.
