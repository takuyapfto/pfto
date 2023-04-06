<?php
session_start();
require_once '../include/model/model.php';
if (isset($_SESSION['user_name'])) {
	header('Location:./redirect_to_item_list.php');
	exit();
}

$messages = '';

if(isset($_POST['sign_up_btn'])) {
	$user_name = $_POST['user_name'];
	$password  = $_POST['password'];
	if (!(preg_match('/^[a-zA-Z0-9]{6,}$/i', $user_name) && preg_match('/^[a-zA-Z0-9]{6,}$/i', $password))) {
		$messages = '<p class="messages_red">ユーザ名及びパスワードは、半角英数字6字以上で入力してください。</p>';
	} else {
		$password_hashed = password_hash($password, PASSWORD_DEFAULT);
		$db = get_db();
		$result_un = check_same_user_name($db, $user_name);
		if (!empty($result_un)) {
			$messages = '<p class="messages_red">入力されたユーザ名は登録済みです。異なるユーザ名で登録してください。</p>';
		} else {
			insert_into_user_table($db, $user_name, $password_hashed);
			$messages = '<p class="messages_green">新規登録が正常に完了しました。</p>';
		}
		$db = NULL;
	}
}

require_once '../include/view/sign_up_view.php';