<ul class="nav nav-custom px-2 py-2 navbar navbar-expand-md navbar-light">
  <a class="navbar-brand p-1" href="<?= RELPATH ?>">
    <b>Liqui<span class="mdi mdi-water"></span>DB</b>
  </a>
  <button class="navbar-toggler ml-auto" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <li class="nav-item bg-light">
      <a class="nav-link <?= $tab["index"] ?? "" ?>" href="<?= RELPATH ?>index.php">Home</a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $tab["a-infos"] ?? "" ?>" href="<?= RELPATH ?>resource/abzeichen-infos.php">Infos</a>
    </li>
    <!--li class="nav-item">
      <a class="nav-link <?= $tab["vorlage"] ?? "" ?>" href="<?= RELPATH ?>resource/vorlage.php">Vorlage</a>
    </li-->
    <?php
      if(isset($_SESSION["USER"])) {
        $user = get_user($_SESSION["USER"]);
        // Gruppen-Icons generieren
        $icons = "";
        if($user["ist_trainer"]) {
          $icons .= "<span class='mdi mdi-account-star'></span>";
        }
        if($user["ist_admin"]) {
          $icons .= "<span class='mdi mdi-shield-account'></span>";
        }
        if(!empty($icons)) {
          $icons .= " | ";
        }
        ?>
        <li class="nav-item ml-md-1">
          <a class="nav-link <?= $tab["eintritt"] ?? "" ?>" href="<?= RELPATH ?>eintritt/index.php">Eintritt</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tab["abzeichen"] ?? "" ?> <?= ["disabled", ""][$user["ist_trainer"]] ?>" href="<?= RELPATH ?>abzeichen/index.php">Abzeichen</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $tab["admin"] ?? "" ?> <?= ["disabled", ""][$user["ist_admin"]] ?>" href="<?= RELPATH ?>admin/index.php">Admin</a>
        </li>
        <li class="nav-item dropdown ml-auto">
          <a class="nav-link dropdown-toggle <?= $tab["nutzer"] ?? "" ?>" data-toggle="dropdown" href="#" role="button"><?= $icons ?><?= $user["display_name_esc"] ?></a>
          <div class="dropdown-menu dropdown-menu-md-right">
            <a class="dropdown-item mdi mdi-account-edit" href="<?= RELPATH ?>user/change-data.php">Daten ändern</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item mdi mdi-logout" href="<?= RELPATH ?>user/logout.php">Logout</a>
          </div>
        </li>
        </div>
        <?php
      }
    ?>
  </div>
</ul>