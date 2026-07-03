<?php

require_once("../includes/auth.php");
require_once("../includes/header.php");
require_once("../includes/navbar.php");

?>

<div class="container">

<?php require_once("../includes/sidebar.php"); ?>

<div class="content">

<h1>Music Library</h1>

<input
type="text"
id="search"
placeholder="Search songs..."
class="search-box">

<div id="songs"></div>

</div>

</div>

<script src="../assets/library.js"></script>

<?php require_once("../includes/footer.php"); ?>