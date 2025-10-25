# Codex Development Instructions

Ten plik stanowi brief dla agenta OpenAI Codex pracującego nad repozytorium.

## Cel
Dokończyć implementację panelu admina i zwiększyć bezpieczeństwo CMS.

## Zakres zadań
- Uzupełnić funkcje `handle*Post()` w `src/AdminHandlers.php`.  
- Użyć `PagesRepository`, `PostsRepository`, `MenuRepository`, `Blocks`.  
- Zachować logowanie do `app/logs/diagnostics`.  
- Zintegrować CSRF, safe_html, trim, prepared statements.  
- Przygotować testy unit + runtime.  

## Zasady
- Nie modyfikować testów diagnostycznych bez konsultacji.  
- Nie commitować danych produkcyjnych (.env, tjw.sqlite).  
- Każdą zmianę w osobnej gałęzi `codex/*`.  

---


**Autor:** Roman Berens – OneNetworks (2025)
