<?php
  /**
   * HTML-Putzt eine Einage
   * 
   * @param mixed $to_escape
   * 
   * @return string Gesäuberte Ausgabe.
   */
  function escape($to_escape): ?string {
    if(!$to_escape) {
      return NULL;
    }
    return filter_var($to_escape, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  }

  /**
   * Setzt die RELPATH-Konstante anhand der Verzeichnistiefe.
   *
   * @param int $dir_depth Anzahl der Verzeichnisse, die die derzeitige Datei vom LiquiDB-Root "entfernt" ist.
   */
  function set_relpath(int $dir_depth): void {
    /** Relativer Pfad zum LiquiDB Root-Verzeichnis (für URL). */
    define("RELPATH", str_repeat("../", $dir_depth));
  }

  /**
   * Löst Teilnehmer-$id zu einem Array mit dessen Daten auf.
   *
   * @param int $id Teilnehmer-ID
   *
   * @return array Teilnehmerdaten aus der Datenbank plus einige Zusatzdaten
   */
  function get_participant(int $id): array {
    global $db;
    $query = sprintf(
      "SELECT * FROM participant WHERE id = %d",
      $id
    );
    $result = mysqli_query($db, $query);
    if(!$result) {
      die("Fehler beim auflösen der ParticipantID.");
    }
    $participant = mysqli_fetch_array($result);
    if(!$participant) {
      die("Fehler beim auflösen der ParticipantID.");
    }
    // Zusatzdaten
    $participant["display_gender"] = [
      "m" => "Männlich",
      "w" => "Weiblich",
      "d" => "Divers"
    ][$participant["gender"]] ?? NULL;
    if($participant["birthday"]) { // 20.06.2001  2001-06-20
      $participant["birthday_formatted"] = substr($participant["birthday"], 8, 2) . "." . substr($participant["birthday"], 5, 2) . "." . substr($participant["birthday"], 0, 4);
    }
    else {
      $participant["birthday_formatted"] = NULL;
    }
    $participant["display_gender"] = escape($participant["display_gender"]);
    $participant["name_esc"] = escape($participant["name"]);
    $participant["birthplace_esc"] = escape($participant["birthplace"]);
    $participant["address_esc"] = escape($participant["address"]);
    $participant["city_esc"] = escape($participant["city"]);
    $participant["note_esc"] = escape($participant["note"]);
    return $participant;
  }

  /**
   * Löst Nutzer-$id zu einem Array mit dessen Daten auf.
   *
   * Überprüft außerdem, ob die Sitzung abgelaufen ist.
   *
   * @param int $id Nutzer-ID
   *
   * @return array Nutzerdaten aus der Datenbank plus einige Zusatzdaten
   */
  function get_user(int $id): array {
    global $db;
    $query = sprintf(
      "SELECT * FROM user WHERE id = %d",
      $id
    );
    $result = mysqli_query($db, $query);
    if(!$result) {
      die("Fehler beim auflösen der UserID. Bitte Abmelden.");
    }
    $user = mysqli_fetch_array($result);
    if(!$user) {
      die("Fehler beim auflösen der UserID. Bitte Abmelden.");
    }
    // Ausloggen, wenn Login-Zeitstempel vor letzter Passwortänderung (Sitzung abgelaufen)
    if(isset($user["pw_changed"])) {
      if(date_timestamp_get(date_create_from_format("Y-m-d H:i:s", $user["pw_changed"])) >= $_SESSION["USER_LOGINTIME"]) {
        // Sitzung abgelaufen
        unset($_SESSION["USER"]);
        unset($_SESSION["USER_LOGINTIME"]);
        send_alert(RELPATH . "index.php", "info", "Ihre Sitzung ist abgelaufen, da sich ihr Passwort verändert hat.");
      }
    }
    // Zusatzdaten
    $user["username_esc"] = escape($user["username"]);
    $user["name_esc"] = escape($user["name"]);
    $user["display_name_esc"] = escape($user["display_name"]);
    return $user;
  }

  /**
   * Löst sich zu Tags für <head> auf.
   *
   * @param bool $load_bootstrap_table Ob Bootstrap Table für die Seite bereitstehen soll.
  */
  function get_head(bool $load_bootstrap_table = False): void {
    require("head.php");
  }

  /**
   * Löst sich zur Script-Include Liste auf.
   *
   * @param bool $load_bootstrap_table Ob Bootstrap Table für die Seite bereitstehen soll.
   * */
  function get_foot(bool $load_bootstrap_table = False): void {
    require("foot.php");
  }

  /**
   * Löst sich zur Navigationsleiste auf.
   * 
   * @param string $aktiver_tab Tab, der den active-Tag bekommt.
   * */
  function get_nav(string $aktiver_tab): void {
    $tab[$aktiver_tab] = "active";
    require("nav.php");
  }

  /**
   * Gibt passend den Singular bzw. Plural aus.
   *
   * @param mixed $number Nummer, anhand der der Numerus festgestellt wird.
   * 
   * @param string $singular singulärer Ausdruck.
   * 
   * @param string $plural Optional. Pluraler Ausdruck.
   * Standart: $singular . "s"
   */
  function get_quantity($number, string $singular, string $plural = NULL): string {
    if($number == -1 | $number == 1) {
      return $singular;
    }
    if(!$plural) {
      $plural = $singular . "s";
    }
    return $plural;
  }

  /**
   * Sendet Alert-Banner zur $target Seite.
   *
   * Bricht Ausführung ab und leitet direkt zur Zielseite weiter.
   * Zielseite muss ein catch_alert() haben.
   * $post_forward dient dazu, (gültige) Eingaben eines Formulars bei einem Fehler an die Zielseite zurückzusenden. Wenn z.B. ein Passwort beim Anmelden falsch ist, kann der Nutzername zurückgesendet werden, um dem Benutzer ein doppeltes Eingeben zu ersparen.
   *
   * @param string $target Zielseite.
   * @param string $type Bootstrap Farbe.
   * @param string $content Anzuzigender Text im Alert-Banner.
   * @param bool $allow_html Ob HTML aufgelöst werden soll.
   * @param array $fill_form Zum erneuten Ausfüllen von Formularen bei Fehlern
   */
  function send_alert(string $target, string $type, string $content, bool $allow_html = False, array $fill_form = NULL): void {
    if($fill_form) {
      $_SESSION["fill_form"] = $fill_form;
    }
    $_SESSION["alert"] = [
      "type" => $type,
      "content" => $content,
      "allow_html" => $allow_html
    ];
    header("Status: 200");
    header("Location: $target");
    die();
  }

  /** Löst sich ggf. in ein einkommendes Alert-Banner auf. */
  function catch_alert(): void {
    if(isset($_SESSION["alert"])) {
      $alert = $_SESSION["alert"];
      unset($_SESSION["alert"]);

      if(!$alert["allow_html"]) {
        $alert["content"] = filter_var($alert["content"] ?? "", FILTER_SANITIZE_SPECIAL_CHARS);
      }
      ?>
      <div class="alert alert-<?= $alert["type"] ?? "info" ?> alert-dismissible fade show" role="alert">
        <?= $alert["content"] ?? "Kein Inhalt im Alert." ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php
    }
  }

  /**
   * Gibt den gespeicherten Wert des Formular-Inputs aus.
   * 
   * z.B. bei Fehlern nützlich. Wird von send_alert() ausgelöst.
   *
   * @param string $input_name Name des Input-Tags
   * @param bool $is_textarea Überspringt "value=''". Nützlich für Textfelder
   *
   * @return string (Falls vorhander) der gespeicherte Wert
   */
  function get_fill_form(string $input_name, bool $is_textarea = False): string {
    $value = "";
    if(isset($_SESSION["fill_form"])) {
      $value = $_SESSION["fill_form"][$input_name] ?? "";
  
      unset($_SESSION["fill_form"][$input_name]);
      if(count($_SESSION["fill_form"]) == 0) {
          unset($_SESSION["fill_form"]);
      }
    }

    if($is_textarea) {
      return $value;
    }
    return "value='$value'";
  }

  /**
   * Prüft, ob Nutzer angemeldet ist und entsprechende Berechtigung besitzt
   *
   * @param string $rolle Rolle, die für Zugriff erforderlich ist. Entsprechend großschreiben.
   * NULL für Helfer (angemeldet).
   */
  function restricted(string $rolle = NULL): void {
    // Prüft, ob Nutzer angemeldet ist und entsprechende Berechtigung besitzt
    // $rolle mit entsprechender Großschreibung. NULL für Helfer (angemeldet)
    if(!isset($_SESSION["USER"])) {
      send_alert(RELPATH . "index.php", "info", "Sie müssen sich zuerst anmelden");
    }
    $user = get_user($_SESSION["USER"]);
    if($user["pw_must_change"]) {
      send_alert(RELPATH . "index.php", "warning", "Sie müssen zuerst Ihr <a href='user/change-data.php'>Passwort ändern</a>", True);
    }
    if($rolle !== NULL) {
      if(!$user["ist_" . strtolower($rolle)]) {
        send_alert(RELPATH . "index.php", "warning", "Sie sind kein $rolle");
      }
    }
  }

  /**
   * Generiert einen kryptographisch sicheren, zufälligen String.
   * 
   * @param int $length Länge der Ausgabe.
   * @param string $keyspace Mögliche Zeichen der Ausgabe.
   * 
   * @return string
   * 
   * @link https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
   */
  function random_str(
    int $length = 64,
    string $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!$%&/()=?{[]}*+~ #-_.:,;<>|"
  ): string {
    if ($length < 1) {
      throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, "8bit") - 1;
    for ($i = 0; $i < $length; ++$i) {
      $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode("", $pieces);
  }

  /**
   * Gibt den Hash des Passwortes (mit Salt) zurück.
   *
   * @param string $salt Zuzufügendes Salt.
   * @param string $password Das zu hashende Passwort.
   * @param string $algo Der zu verwendende Hash-Algorithmus.
   *
   * @return string Hash.
   */
  function hash_password(string $salt, string $password, string $algo = "sha256"): string {
    $mixed_password = $salt . $password;
    return strtoupper(hash($algo, $mixed_password));
  }

  /**
   * Gibt das Alter einer Person wieder, die am gegebenen Tag Geburtstag hat.
   *
   * @param string $birthday Format des Geburtsdatums: YYYY-MM-DD
   *
   * @return int
   */
  function get_age(string $birthday): int {
    $timezone = new DateTimeZone(TIMEZONE);
    $birthday_date = new DateTime($birthday, $timezone);
    $current_date = new DateTime("now", $timezone);
    return $birthday_date->diff($current_date)->y;
  }

  /**
   * "NULL", wenn $field == NULL ist, ansonsten "'$field'", wobei $field mysql-escaped wurde.
   * 
   * Für optionale Felder gedacht, die als "NULL" (Konstante) oder "'Entsprech\'ender Wert'" (Sicherer String) sein können.
   *
   * @param mixed $field
   *
   * @return string
   */
  function mysql_escape_or_null($field): string {
    global $db;
    if(!$field) {
      return "NULL";
    }
    return "'" . mysqli_real_escape_string($db, $field) . "'";
  }
?>