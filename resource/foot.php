<!--jQuery JS-->
<script src="<?= RELPATH ?>vendor/jquery-3.4.1.min.js"></script>

<!--Popper JS-->
<script src="<?= RELPATH ?>vendor/popper.min.js"></script>

<!--Bootstrap JS-->
<script src="<?= RELPATH ?>vendor/bootstrap.min.js"></script>

<?php
  if($load_bootstrap_table) { ?>
    <!--Bootstrap Table JS-->
    <script src="<?= RELPATH ?>vendor/bootstrap-table/bootstrap-table.min.js"></script>
    <script src="<?= RELPATH ?>vendor/bootstrap-table/locale/bootstrap-table-de-DE.min.js"></script>
  <?php }
?>