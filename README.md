# Сокращатель ссылок

Laravel-приложение для создания коротких ссылок, отслеживания переходов и управления ими через личный кабинет **Filament v3**.

**Репозиторий:** https://github.com/scalevillain13/laravel-url-shortener

## Стек

| Компонент | Версия |
|---|---|
| PHP | 8.3+ |
| Laravel | 13 |
| Filament | v3.3 |
| БД | SQLite (по умолчанию) |
| API | Laravel Sanctum |
| QR | Simple QrCode |
| CI | GitHub Actions |

## Возможности

### По ТЗ
- Регистрация и вход (`/admin/register`, `/admin/login`)
- Создание коротких ссылок с автогенерацией кода
- Редирект `GET /{code}` + запись статистики (IP, дата/время)
- Личный кабинет: список ссылок, удаление, статистика по каждой ссылке
- Filament v3 как admin panel

### Дополнительно (1–16 улучшений)

| # | Улучшение | Реализация |
|---|---|---|
| 1 | Docker Compose | `Dockerfile`, `docker-compose.yml` (app + queue worker) |
| 2 | GitHub Actions | `.github/workflows/ci.yml` — тесты + Pint |
| 3–4 | README + `.env.example` | Подробная документация и комментарии к переменным |
| 5 | Публичный лендинг | `/` — форма создания ссылки + последние ссылки |
| 6 | Срок жизни ссылки | `expires_at`, `is_active`, HTTP 410 при недоступности |
| 7 | UTM-метки | `utm_source`, `utm_medium`, `utm_campaign` → добавляются при редиректе |
| 8 | Фильтры в кабинете | По активности, истечению, мин. переходам |
| 9 | Rate limit | `throttle:redirect` — лимит редиректов с IP |
| 10 | Фильтр ботов | `BotDetector` — crawler'ы не попадают в статистику |
| 11 | Безопасные URL | `SafeRedirectUrl` — блок `javascript:`, локальных IP |
| 12 | HTTPS-only | `SHORTENER_REQUIRE_HTTPS=true` по умолчанию |
| 13 | Policy | `LinkPolicy` + явная регистрация в `AppServiceProvider` |
| 14 | REST API | Sanctum: CRUD ссылок + токен `/api/tokens` |
| 15 | Геолокация | Страна/город в `clicks` (через ip-api.com в job) |
| 16 | Пагинация статистики | Таблица переходов: 10/25/50/100 на страницу |

## Быстрый старт (локально)

```bash
git clone https://github.com/scalevillain13/laravel-url-shortener
cd laravel-url-shortener

composer install
cp .env.example .env
php artisan key:generate

# SQLite
type nul > database\database.sqlite

php artisan migrate
php artisan serve
```

Откройте:
- http://127.0.0.1:8000 — публичный лендинг
- http://127.0.0.1:8000/admin — личный кабинет

## Docker

```bash
docker compose up --build
```

- **app** — http://localhost:8000
- **queue** — воркер очереди (`QUEUE_CONNECTION=database`)

## Очереди

```env
QUEUE_CONNECTION=database   # production / docker
php artisan queue:work
```

Для разработки можно оставить `QUEUE_CONNECTION=sync` — клики пишутся сразу.

## REST API (Sanctum)

### Получить токен

```http
POST /api/tokens
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password",
  "device_name": "my-app"
}
```

### Создать ссылку

```http
POST /api/links
Authorization: Bearer {token}
Content-Type: application/json

{
  "original_url": "https://example.com/page",
  "utm_source": "newsletter",
  "expires_at": "2026-12-31T23:59:59"
}
```

### Другие эндпоинты

| Метод | URL | Описание |
|---|---|---|
| GET | `/api/links` | Список ссылок (paginate) |
| GET | `/api/links/{id}` | Ссылка + статистика + последние 50 кликов |
| DELETE | `/api/links/{id}` | Удалить ссылку |

## Архитектурные решения

```
app/
├── Actions/          # Бизнес-логика (Create, RecordClick, Export, GeoIP, Resolve)
├── Http/
│   ├── Controllers/  # Home, Redirect, QR, API
│   └── Requests/     # StoreLinkRequest + SafeRedirectUrl
├── Jobs/             # RecordClickJob (асинхронная запись)
├── Policies/         # LinkPolicy
├── Rules/            # SafeRedirectUrl
└── Support/          # BotDetector
```

- **Редирект** не ждёт запись в БД — клик уходит в `RecordClickJob`
- **Кэш** хранит ID ссылки (не сериализованную модель) — `ResolveLinkForRedirectAction`
- **Policy** централизует доступ; Filament + API используют одни правила
- **Боты** отфильтровываются до записи; **геолокация** — в job, не блокирует редирект

## Тесты

```bash
php artisan test
```

**34 теста:** редирект, очередь, кэш, Policy, API, UTM, expiry, боты, HTTPS-валидация, Filament.

## Скриншоты

> Добавьте в `docs/screenshots/`:
> - `dashboard.png` — инфопанель Filament
> - `links.png` — список ссылок
> - `stats.png` — статистика переходов
> - `landing.png` — публичный лендинг

## Переменные окружения

| Переменная | По умолчанию | Описание |
|---|---|---|
| `SHORTENER_REQUIRE_HTTPS` | `true` | Только HTTPS в оригинальных URL |
| `SHORTENER_REDIRECT_RATE_LIMIT` | `60` | Редиректов в минуту с одного IP |
| `SHORTENER_IGNORE_BOTS` | `true` | Не считать ботов |
| `SHORTENER_GEOIP_ENABLED` | `true` | Геолокация по IP |

## Лицензия

MIT
