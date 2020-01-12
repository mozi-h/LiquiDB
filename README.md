# LiquiDB
Daten-Manager für Schwimmkurse. Teilnehmer, Abzeichen(-fortschritt) und Benutzerrechte. Entwickelt für die DLRG.
> LiquiDB ist derzeit noch in der Entwicklung. Erste Beta-Versionen voraussichtlich Mitte 2020.

Hier der Plan:


## Die Idee
LiquiDB ist eine **Webanwendung**, auf die man mit eingerichteten (DLRG) Tablets zugreifen kann.  

Personen am Eingang können eintreffende Schwimmer als **anwesend** markieren und **Eintritt** für das Schwimmbad kassieren.  
Trainer im Bad können einsehen, wer welche **Abzeichen** hat, an welchen gearbeitet wird und wie der Fortschritt ist. Er kann auch Disziplinen als erledigt eintragen und sieht beim Ausstellen des Abzeichens alle benötigten Daten, sowie **Hinweise** z. B. zu abgelaufenen Erste-Hilfe Bescheinigungen (z.B. beim DRSA Silber) oder weit in der Vergangenheit liegende Disziplinen (2 Monate).

Es können Statistiken, wie z. B. ausgestelle Abzeichen im Kalenderjahr oder Anzahl der aktiven Personen mit DRSA Silber / Gold angezeigt werden.

Helfer haben jeder einen eigenen Account. Optional können sie als **Trainer** (kann Abzeichen(-fortschritt) bearbeiten) und / oder **Admin** (Benutzer erstellen und managen) markiert werden.


## Technische Umsetzung
Der Webserver und die Datenbank läuft auf einem Raspberry Pi (voraussichtlich Zero W). Der Pi stellt sein eigenes drahtloses Netzwerk und ist selber nicht mit dem Internet verbunden (Datensicherheit). Der Pi wird zu Beginn des Trainings eingeschaltet.

Eingerichtete Tablets können LiquiDB aufrufen und man kann sich anmelden. Tablets können in wasserdichte Hüllen getan werden, um ein Arbeiten am Wasser zu ermöglichen.
