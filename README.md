# Mini ticketkezelő rendszer

Laravel / PHP feladat: publikus hibajegy-beküldés és admin felület a ticketek kezelésére.

- Publikus form (bejelentkezés nélkül): http://localhost:8000
- Admin felület: http://localhost:8000/admin

## Előfeltétel

A projekt **Dockerrel** indul. Telepítsd és indítsd el a [Docker Desktop](https://www.docker.com/products/docker-desktop/)-ot, és várd meg, amíg teljesen elindul.

Node.js is kell a frontend buildhez.

## Első indítás

PowerShellben, a projekt mappájából:

```powershell
cd C:\sajat-mappa\amfk
```

### 1. Környezeti fájl

Ha még nincs `.env` fájl:

```powershell
copy .env.example .env
```

### 2. PHP függőségek

```powershell
docker run --rm -v "${PWD}:/app" -w /app composer:2 composer install
```

### 3. Alkalmazáskulcs és adatbázis

```powershell
if (-not (Test-Path "database\database.sqlite")) { New-Item -ItemType File -Path "database\database.sqlite" | Out-Null }

docker run --rm -v "${PWD}:/app" -w /app composer:2 php artisan key:generate
docker run --rm -v "${PWD}:/app" -w /app composer:2 php artisan migrate --seed --force
```

A `--seed` létrehozza az admin felhasználót.

### 4. Frontend

```powershell
npm install
npm run build
```

### 5. Szerver indítása

```powershell
docker compose up -d
```

Nyisd meg a böngészőben: **http://localhost:8000**

## Admin belépés

| E-mail | `admin@example.com` |
| Jelszó | `password` |

Belépés: http://localhost:8000/login

## Leállítás

```powershell
docker compose down
```

## Későbbi indítás

Ha az első telepítés már lefutott, elég:

```powershell
cd C:\sajat-mappa\amfk
docker compose up -d
```

Ha a Docker Desktop nem fut, előbb indítsd el, és csak utána a `docker compose up -d` parancsot.

## Hasznos parancsok

Artisan parancsok Dockeren keresztül (PHP nincs a gépre telepítve):

```powershell
docker run --rm -v "${PWD}:/app" -w /app composer:2 php artisan migrate:fresh --seed --force
docker run --rm -v "${PWD}:/app" -w /app composer:2 php artisan test
```

`migrate:fresh --seed` **törli** az adatbázist, és újra létrehozza az admin fiókot.

## Mit csinál az app

1. Bárki beküldhet ticketet név, e-mail, cím és leírás megadásával. Az új ticket státusza `pending`.
2. Az admin felület csak bejelentkezett adminnak érhető el.
3. Az admin látja a ticketeket (beküldő, e-mail, leírás, státusz, időpont), és módosíthatja a státuszt: `pending`, `in progress`, `done`.
