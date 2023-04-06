<?php
session_start();
require_once '../include/model/model.php';
check_session_admin($_SESSION['user_name']);

$db = get_db();
$result_uc = get_un_cd($db);
$db = NULL;

if (isset($_POST['logout'])) {
	logout();
}

require_once '../include/view/user_mng_view.php';