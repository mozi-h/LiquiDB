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

  // ** Ender der Einstellungen - Hierunter nicht bearbeiten ** //

  if(!defined("ABSPATH")) {
    /** Absoluter Pfad zum LiquiDB Root-Verzeichnis */
    define("ABSPATH", dirname(__FILE__) . "/");
  }

  /** Datenbank-Verbindung */
  $db = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
  if(!$db) {
    echo "FEHLER MIT DER DATENBANKENVERBINDUNG:<br>";
    echo mysqli_connect_error();
    die();
  }
  mysqli_set_charset($db, "utf8mb4_bin");

  session_start();

  require("resource/functions.php");
?>