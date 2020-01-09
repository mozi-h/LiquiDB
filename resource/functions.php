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
      "SELECT *, pw_hash AS pw_hash FROM user WHERE id = %d",
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
    $user["username_esc"] = escape($user["username"]);
    $user["name_esc"] = escape($user["name"]);
    $user["anzeigename"] = $user["name"] ?? $user["username"];
    $user["anzeigename_esc"] = escape($user["anzeigename"]);
    return $user;
  }

  /** Löst sich zu Tags für <head> auf. */
  function get_head(): void {
    require("head.php");
  }

  /** Löst sich zur Script-Include Liste auf. */
  function get_foot(): void {
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
    if($number < -1 | $number > 1) {
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
   *
   * @param string $target Zielseite.
   * @param string $type Bootstrap Farbe.
   * @param string $content Anzuzigender Text im Alert-Banner.
   * @param bool $allow_html Ob HTML aufgelöst werden soll.
   */
  function send_alert(string $target, string $type, string $content, bool $allow_html = False): void {
    $content = filter_var($content, FILTER_SANITIZE_SPECIAL_CHARS);
    if($allow_html == False) {
      $content = filter_var($content, FILTER_SANITIZE_SPECIAL_CHARS);
    }
    ?>
    <form id='send_alert' action='<?= $target ?>' method='post'>
      <input type='text' name='alert[type]' value='<?= $type ?>' hidden>
      <input type='text' name='alert[content]' value='<?= $content ?>' hidden>
    </form>
    <script>
      document.getElementById('send_alert').submit();
    </script>
    <?php
    die();
  }

  /** Löst sich ggf. in ein einkommendes Alert-Banner auf. */
  function catch_alert(): void {
    if(isset($_POST["alert"])) {
      $alert = $_POST["alert"];
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
    string $keyspace = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!§$%&/()=?{[]}*+~ #-_.:,;<>|"
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
?>