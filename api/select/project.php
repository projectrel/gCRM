<?php
include_once("../../db.php");
include_once("../../funcs.php");
if (isset($_POST['project_id'])) {
    $project_id = clean($_POST['project_id']);
    $project = mysqli_fetch_assoc($connection->query("
    SELECT * FROM projects WHERE project_id = '$project_id'
    "));
}
if ($project) {
    echo json_encode($project);
    return false;
} else {
    return error("failed");
}



