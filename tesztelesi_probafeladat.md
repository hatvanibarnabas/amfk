## 1. E2E (UI) tesztelés – saucedemo.com

**Mit tartok kritikusnak egy vásárlási folyamatnál:**
Nem csak azt ellenőrzöm, hogy "végig lehet-e menni" a folyamaton, hanem hogy a rendszer helyesen kezeli-e a hibás bemeneteket (validáció), és hogy az adatok (kosár tartalma, összegek) konzisztensek maradnak-e a lépések között.

```javascript
// e2e/checkout.spec.js
import { test, expect } from '@playwright/test';

const BASE_URL = 'https://www.saucedemo.com';

test.describe('Bejelentkezés', () => {
  test('sikeres bejelentkezés helyes adatokkal', async ({ page }) => {
    await page.goto(BASE_URL);
    await page.fill('#user-name', 'standard_user');
    await page.fill('#password', 'secret_sauce');
    await page.click('#login-button');
    await expect(page).toHaveURL(/inventory.html/);
    await expect(page.locator('.title')).toHaveText('Products');
  });

  test('zárolt felhasználó nem tud bejelentkezni', async ({ page }) => {
    await page.goto(BASE_URL);
    await page.fill('#user-name', 'locked_out_user');
    await page.fill('#password', 'secret_sauce');
    await page.click('#login-button');
    await expect(page.locator('[data-test="error"]')).toContainText('locked out');
  });

  test('hibás jelszóval sikertelen bejelentkezés, hibaüzenettel', async ({ page }) => {
    await page.goto(BASE_URL);
    await page.fill('#user-name', 'standard_user');
    await page.fill('#password', 'rossz_jelszo');
    await page.click('#login-button');
    await expect(page.locator('[data-test="error"]')).toContainText('do not match');
  });
});

test.describe('Kosár kezelése', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(BASE_URL);
    await page.fill('#user-name', 'standard_user');
    await page.fill('#password', 'secret_sauce');
    await page.click('#login-button');
  });

  test('termék hozzáadása frissíti a kosár badge számlálót', async ({ page }) => {
    await page.click('#add-to-cart-sauce-labs-backpack');
    await expect(page.locator('.shopping_cart_badge')).toHaveText('1');
  });

  test('több termék hozzáadása helyesen összegződik', async ({ page }) => {
    await page.click('#add-to-cart-sauce-labs-backpack');
    await page.click('#add-to-cart-sauce-labs-bike-light');
    await expect(page.locator('.shopping_cart_badge')).toHaveText('2');
  });

  test('termék eltávolítása csökkenti a badge számot / eltünteti azt', async ({ page }) => {
    await page.click('#add-to-cart-sauce-labs-backpack');
    await page.click('#remove-sauce-labs-backpack');
    await expect(page.locator('.shopping_cart_badge')).toHaveCount(0);
  });
});

test.describe('Teljes checkout folyamat', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto(BASE_URL);
    await page.fill('#user-name', 'standard_user');
    await page.fill('#password', 'secret_sauce');
    await page.click('#login-button');
    await page.click('#add-to-cart-sauce-labs-backpack');
    await page.click('.shopping_cart_link');
  });

  test('a vásárlás a bejelentkezéstől a sikeres megrendelésig végigmegy', async ({ page }) => {
    await page.click('#checkout');
    await page.fill('#first-name', 'Teszt');
    await page.fill('#last-name', 'Elek');
    await page.fill('#postal-code', '1000');
    await page.click('#continue');
    await expect(page).toHaveURL(/checkout-step-two.html/);
    await page.click('#finish');
    await expect(page.locator('.complete-header')).toHaveText('Thank you for your order!');
  });

  test('kötelező mező (First Name) hiányában hibaüzenet jelenik meg', async ({ page }) => {
    await page.click('#checkout');
    await page.click('#continue');
    await expect(page.locator('[data-test="error"]')).toContainText('First Name is required');
  });

  test('a végösszeg megegyezik a tételösszeg és az adó összegével', async ({ page }) => {
    await page.click('#checkout');
    await page.fill('#first-name', 'Teszt');
    await page.fill('#last-name', 'Elek');
    await page.fill('#postal-code', '1000');
    await page.click('#continue');

    const subtotalText = await page.locator('.summary_subtotal_label').innerText();
    const taxText = await page.locator('.summary_tax_label').innerText();
    const totalText = await page.locator('.summary_total_label').innerText();

    const subtotal = parseFloat(subtotalText.replace(/[^0-9.]/g, ''));
    const tax = parseFloat(taxText.replace(/[^0-9.]/g, ''));
    const total = parseFloat(totalText.replace(/[^0-9.]/g, ''));

    expect(total).toBeCloseTo(subtotal + tax, 2);
  });
});

test.describe('Adatok megőrzése navigáció közben', () => {
  test('a kosár tartalma megmarad, ha a user visszalép', async ({ page }) => {
    await page.goto(BASE_URL);
    await page.fill('#user-name', 'standard_user');
    await page.fill('#password', 'secret_sauce');
    await page.click('#login-button');
    await page.click('#add-to-cart-sauce-labs-backpack');
    await page.click('.shopping_cart_link');
    await page.goBack();
    await expect(page.locator('.shopping_cart_badge')).toHaveText('1');
  });
});
```

---

## 2. API tesztelés – reqres.in

**Gondolatmenet:** a puszta 200-as státuszkódon túl azt validálom, hogy (1) a válasz sémája stabil-e — mert egy frontend erre épít, (2) a lapozás valóban különböző adatot ad-e vissza, és (3) az egyes mezők formátuma helyes-e (pl. email, avatar URL) — mert ezek gyakran csendben törnek el, státuszkód-hiba nélkül.

```javascript
// api/users.spec.js
import { test, expect } from '@playwright/test';

const BASE = 'https://reqres.in';

test.describe('GET /api/users', () => {
  test('200 státuszkód és helyes válasz-séma', async ({ request }) => {
    const response = await request.get(`${BASE}/api/users?page=2`);
    expect(response.status()).toBe(200);

    const body = await response.json();
    expect(body).toHaveProperty('page', 2);
    expect(body).toHaveProperty('total_pages');
    expect(Array.isArray(body.data)).toBeTruthy();
    expect(body.data.length).toBeGreaterThan(0);
  });

  test('a lapozás (pagination) ténylegesen eltérő adatot ad vissza oldalanként', async ({ request }) => {
    const page1 = await (await request.get(`${BASE}/api/users?page=1`)).json();
    const page2 = await (await request.get(`${BASE}/api/users?page=2`)).json();

    const page1Ids = page1.data.map(u => u.id);
    const page2Ids = page2.data.map(u => u.id);

    expect(page1.page).toBe(1);
    expect(page2.page).toBe(2);
    expect(page1Ids).not.toEqual(page2Ids);
  });

  test('minden felhasználó mezője helyes formátumú', async ({ request }) => {
    const response = await request.get(`${BASE}/api/users?page=1`);
    const body = await response.json();

    for (const user of body.data) {
      expect(user).toHaveProperty('id');
      expect(user.email).toMatch(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
      expect(user.avatar).toMatch(/^https:\/\//);
      expect(typeof user.first_name).toBe('string');
      expect(typeof user.last_name).toBe('string');
    }
  });
});
```
---

## 3. Dokumentáció – miért ezeket validáltam (E2E feladathoz)

- **Bejelentkezés — pozitív és negatív eset is:** nemcsak az kell, hogy a jó adatokkal működjön, hanem hogy a rendszer explicit, érthető hibát adjon vissza helytelen/zárolt fióknál — ez gyakori éles hiba forrás (pl. néma hibák, a user nem tudja miért nem megy be).
- **Kosár számláló és tartalom konzisztenciája:** ez egy state-kezelési pont, ahol könnyen csúszhat a szám (pl. duplikált hozzáadás, eltávolítás után rossz érték).
- **Kötelező mezők validációja checkoutnál:** ez egy vásárlást blokkoló pont, ahol egy hiányzó validáció (pl. üres mezővel is tovább lehet lépni) közvetlen bevételkiesést okozhat.
- **Összegek matematikai helyessége (subtotal + tax = total):** csendes hiba típus — UI-lag minden működik, de a felhasználó rossz végösszeget lát, ami bizalmi és jogi kockázat is.
- **Adatmegőrzés navigáció után:** valós felhasználói viselkedés (vissza-előre lépkedés), és pont ez a fajta eset szokott elveszni fejlesztés közben, mert a "happy path" teszt lefedi, de a navigációs edge case nem.

Az API tesztnél hasonlóan gondolkodtam: a fejlesztőnek nem a státuszkód a fontos elsősorban, hanem hogy a válasz szerkezete és a lapozás logikája ne törjön el észrevétlenül — ezek azok a hibák, amik CI-ban simán átcsúsznak, ha csak `status === 200`-at nézünk.
