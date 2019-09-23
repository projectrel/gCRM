<?php
include_once '../funcs.php';
if (!isAuthorized()) header("Location: ./login.php");
include_once '../components/templates/template.php';

echo template(display_tree_table());

