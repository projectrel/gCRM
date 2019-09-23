<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ../login.php");
include_once '../components/templates/template.php';

echo template('<div class="container" id="container">
</div><button id="mode-switch"></button>');

