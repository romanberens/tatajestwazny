# Tata Jest Ważny – CMS

**Status:** test & refactor  
**Cel:** przygotowanie aplikacji PHP do stabilnej wersji produkcyjnej z audytowanym bezpieczeństwem i kompletnym systemem CMS.

---

## 🧩 Aktualna architektura
- Frontend: HTML + CSS (bootstrap-like)  
- Backend: PHP 8.3 + SQLite + PDO  
- Serwer: Traefik → Nginx → PHP-FPM w Dockerze  
- Repozytoria: `PagesRepository`, `PostsRepository`, `MenuRepository`, `Blocks`  
- Testy: `test_structure_map.php`, `test_runtime_trace.php`, `test_security_audit.sh`, `test_security_extended.sh`, `test_db_integrity.php`, `test_api_post.sh`

---

## ⚙️ Stan funkcji (2025-10-25)

| Moduł / Funkcja | Status | Uwagi |
|------------------|---------|--------|
| `handlePagesPost()` | 🚧 Partial | CSRF OK, brak zapisów do DB (`save()`) |
| `handleBlogPost()` | 🚧 Pending | brak implementacji |
| `handleMenuPost()` | 🚧 Pending | brak implementacji |
| `handleBlocksPost()` | 🚧 Pending | brak implementacji |
| `safe_html()` | ✅ OK | sanityzacja działa |
| `render()` | ✅ OK | używa `htmlspecialchars()` |
| `require_post_csrf()` | ✅ OK | token generowany poprawnie |
| `auth()` | ✅ OK | działa z `AUTH_PASSWORD_HASH` z `.env` |
| `test_api_post.sh` | ⚠️ Częściowo | nie pobiera CSRF (bez logowania) |

---

## 🧠 Znane problemy do refaktoryzacji
- Bezpośrednie użycie `$_POST` bez filtracji (`AdminHandlers`, `helpers`, `auth.php`).  
- Brak placeholderów w części zapytań PDO (do weryfikacji).  
- Brak CSRF w `admin_dashboard.php`, `admin_layout.php`.  
- Formularz API POST nie działa bez sesji logowania.  
- `handle*Post()` funkcje nie przesyłają danych do repozytoriów.  

---

## 🧪 Testy i audyt
Wszystkie testy dokumentujące strukturę i bezpieczeństwo znajdują się w `/app/tests/`.

Przykładowe uruchomienia:
```bash
docker exec -it tjw_php php /app/tests/test_structure_map.php
docker exec -it tjw_php php /app/tests/test_runtime_trace.php
docker exec -it tjw_php sh /app/tests/test_security_audit.sh
docker exec -it tjw_php sh /app/tests/test_security_extended.sh
