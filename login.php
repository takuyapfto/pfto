<?php
session_start();
require_once '../include/model/model.php';
if (isset($_SESSION['user_name'])) {
	header('Location:./redirect_to_item_list.php');
	exit();
}

$messages = '';

if (isset($_POST['login_btn'])) {
	$password  = $_POST['password'];
	$user_name = $_POST['user_name'];
	$db = get_db();
	$result_pw = get_un_pw($db, $user_name);
	// 管理者か一般ユーザのログインが成功してはじめて$_SESSION['user_name']に$user_nameを代入
	// ユーザadminに対するパスワードはデータベースに登録済み
	if ($user_name === 'admin' && password_verify($password, $result_pw)) {
		session_regenerate_id(TRUE);
		$_SESSION['user_name'] = $user_name;
		header('Location:./item_mng.php');
		exit();
	} else {
		if (password_verify($password, $result_pw)) {
			$_SESSION['user_name'] = $user_name;
			header('Location:./item_list.php');
			exit();
		} else {
			$messages = 'ログインに失敗しました。ユーザ名及びパスワードを正しく入力してください。';
		}
	}
	$db = NULL;
}

require_once '../include/view/login_view.php';