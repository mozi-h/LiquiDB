<!--Meta tags-->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<!--Favicon-->
<link rel="shortcut icon" href="<?= RELPATH ?>img/favicon.png" type="image/png">

<!--Bootstrap CSS-->
<link rel="stylesheet" href="<?= RELPATH ?>vendor/bootstrap.min.css">
<!--Bootstrap Datepicker CSS-->
<link rel="stylesheet" href="<?= RELPATH ?>vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css">
<!--Bootstrap Select CSS-->
<link rel="stylesheet" href="<?= RELPATH ?>vendor/bootstrap-select/css/bootstrap-select.min.css">
<!--Material Icons CSS-->
<link rel="stylesheet" href="<?= RELPATH ?>vendor/materialdesignicons/materialdesignicons.min.css">

<?php
  if($load_bootstrap_table) { ?>
    <!--Fontawesome CSS-->
    <link rel="stylesheet" href="<?= RELPATH ?>vendor/fontawesome/css/all.min.css">

    <!--Bootstrap Table CSS-->
    <link rel="stylesheet" href="<?= RELPATH ?>vendor/bootstrap-table/bootstrap-table.min.css">
  <?php }
?>

<!--Custom CSS-->
<link rel="stylesheet" href="<?= RELPATH ?>css/style.css">