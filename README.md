# Gestione Torneo Scolastico (PHP & MySQL)

Questo è un progetto web completo (Full-Stack) che abbiamo sviluppato per gestire e visualizzare i dati di un torneo sportivo scolastico. 
Lo abbiamo realizzato per mettere in pratica l'integrazione tra interfaccia front-end (HTML/CSS) e logica back-end (PHP), imparando a far comunicare un sito web con un database relazionale.

## Come funziona
L'applicazione simula un portale per gli studenti e permette di navigare tra diverse sezioni:

- **Squadre e Rose:** Una pagina per visualizzare tutte le squadre iscritte. Abbiamo implementato un sistema di filtri dinamici in PHP che permette di cercare le squadre per edizione, sport o per classe (es. classi prime, seconde, ecc.).
- **Calendario e Classifiche:** Sezioni dedicate a mostrare lo stato delle partite (da giocare o concluse) e le classifiche aggiornate calcolando i dati direttamente dal database.
- **Albo d'Oro:** Uno storico per visualizzare le squadre vincitrici delle edizioni passate.

## Come testare il codice in locale
Poiché questo progetto usa PHP e un database, per avviarlo sul tuo computer ti servirà un server locale come **XAMPP**.

1. Scarica i file o clona questa repository.
2. Sposta l'intera cartella del progetto all'interno della cartella `htdocs` di XAMPP (es. `C:\xampp\htdocs\torneo`).
3. Apri il pannello di controllo di XAMPP e avvia **Apache** e **MySQL**.
4. Vai all'indirizzo `http://localhost/phpmyadmin/` nel tuo browser.
5. Crea un nuovo database chiamandolo esattamente **`torneo_scolastico`**.
6. **Importa il file `.sql`** che trovi all'interno di questa repository per creare le tabelle e caricare i dati di prova.
7. Apri il browser e vai all'indirizzo `http://localhost/torneo/` (o il nome che hai dato alla cartella) per navigare sul sito.

---

## Autori
Progetto sviluppato in collaborazione:
- **Carmine Ciccarelli** - Il mio profilo LinkedIn https://www.linkedin.com/in/carmine-ciccarelli-bb685a3bb/
- **Filippo Cuccurullo** - Il mio profilo LinkedIn https://www.linkedin.com/in/filippo-cuccurullo-9097603ab
