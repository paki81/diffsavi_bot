# Diffsavi - Per la differenziata di Savignano Irpino ♻️
Bot Telegram per la gestione della raccolta differenziata nel comune di Savignano Irpino.

Funzioni
---------
* Calendario raccolta rifiuti
* Dettagli sui singoli materiali riciclabili e non
* Notifica push per conoscere il materiale da esporre in un determinato giorno
* Informazioni utili fornite dal comune.
* ~~Supporto ai grandi comuni con più zone~~ (sarà reintrodotta in futuro)

Materiali supportati
---------
🍉 Organico
📦 Carta e Cartone
💡 Indifferenziato
🎈 Plastica
🍷 Vetro e Lattine
💻 Ingombranti

BOT già attivi
---------
[@Diffsavi_bot](https://t.me/diffsavi_bot) - Raccolta differenziata Savignano Irpino(AV)



Requisiti
---------
* PHP >= 5.3
* Estensione curl di PHP5 attiva
* DB MySql
* Server con supporto HTTPS (in rete ne esistono anche di gratuiti)

Per iniziare
---------
⚠️ La presente sezione non è ancora completa ⚠️

1) Dai un'occhiata alla documentazione ufficiale sul sito [Telegram.org](https://core.telegram.org/bots)
2) Segui le istruzioni su come attivare un bot con [BotFather](https://core.telegram.org/bots#6-botfather) e generare un TOKEN
3) Scarica l'[ultima release]di Diffsavi_bot
4) Apporta le modifiche per inserire i contenuti del tuo comune
5) Imposta correttamente il file push.php
6) Esegui l'upload del file database.sql sul tuo sistema di DB MySql
7) Esegui l'upload di tutti i file sul tuo server (N.B. Si consiglia di creare una sottocartella così da poter supportare più bot sullo stesso server)
8) Apri il browser e visita https://api.telegram.org/bot(BOT_TOKEN)/setWebhook?url=https://iltuoserver.it/cartellabot/index.php

N.B. Assicurati di sostituire (BOT TOKEN), togliendo pure le parentesi, con il token generato da BotFather e di inserire correttamente l'url, compreso di sottocartella, al file index.php del BOT

Notifiche push
---------
* Per attivare le notifiche push, configura il file push.php in modo corretto
* Importa il file database.sql nel tuo database
* Successivamente attiva sul tuo Server la funzione Cron Job e fai eseguire il file push.php all'orario in cui è previsto l'inizio dell'esposizione dei rifiuti in strada

Crediti
---------
Il Diffsavi_bot si basa sul framework [TelegramBotPHP](https://github.com/Eleirbag89/TelegramBotPHP) realizzato da Eleirbag89.
