<?php
  // ** Einstellungen ** //
  /** MySQL Datenbank Hostname */
  define("DB_HOST", "localhost");

  /** MySQL Datenbank Nutzername */
  define("DB_USERNAME", "root");

  /** MySQL Datenbank Passwort */
  define("DB_PASSWORD", "");

  /** MySQL Datenbank Datenbanken-Name */
  define("DB_NAME", "liquidb");

  /** MySQL Datenbank Datenbanken-Name */
  define("DB_PORT", "3306");

  /** Zeitzone */
  define("TIMEZONE", "Europe/Berlin");

  // ** Ender der Einstellungen - Hierunter nicht bearbeiten ** //

  /**
   * Absoluter Pfad zum LiquiDB Root-Verzeichnis (für Dateisystem).
   * 
   * Für URLs, set_relpath() nutzen und RELPATH verwenden.
   * **/
  define("ABSPATH", dirname(__FILE__) . "/");
  
  /** Datenbank-Verbindung */
  $db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
  if(!$db) {
    echo "FEHLER MIT DER DATENBANKENVERBINDUNG:<br>";
    echo mysqli_connect_error();
    die();
  }
  mysqli_set_charset($db, "utf8mb4_bin");

  session_start();

  require("resource/functions.php");
?>