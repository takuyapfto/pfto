<?php
session_start();
require_once '../include/model/model.php';
check_session($_SESSION['user_name']);
$user_name = $_SESSION['user_name'];

// 多重追加チェック
if (!isset($_POST['token_add']) || $_POST['token_add'] !== $_SESSION['token_add']) {
	die('不正なアクセスです');
} elseif ($_POST['token_add'] === $_SESSION['token_add']) {
	$messages = '';
	if ($_POST['amount'] === '') { // フォームが空白で「カートに入れる」ボタンが押下された場合は処理しない
		$messages = '<p class="messages_yellow">値が入力されていません。処理されませんでした。</p>';
	} else {
	// 表示する商品情報を取得
	$db = get_db();
	$result_item_id = get_item_list($db);
	// カートに入れる商品に関係する情報を取得
	$item_id = $_POST['item_id'];
	$amount  = $_POST['amount'];
	$user_id = get_user_id($db, $user_name);
		// カートに入れようとしている商品が既にカートにあれば、そのidを取得
		$check_cart = get_item_already_in_cart($db, $item_id, $user_id);
		// カートに同一商品がなければ商品を追加
		if (is_null($check_cart)) {
			add_into_cart($db, $user_id, $item_id, $amount);
			$messages = '<p class="messages_green">商品がカートに正常に追加されました。</p>';
		} else {
			// カートに同一商品があればその個数を1増やすだけにする。ただし、増やすと在庫数を超過するなら増やさない
			$result_a = check_amount_in_cart($db, $item_id, $user_id);
			$result_s = check_stock($db, $item_id);
			if ($result_a + 1 <= $result_s) {
				increase_amount_one($db, $item_id, $user_id);
				$messages = '<p class="messages_yellow">同一商品が既にカートにありましたので、購入数を1増やしました。</p>';
			} else {
				$messages = '<p class="messages_red">同一商品が既にカートにあり、1増やすと在庫数を超えるため、購入数の変更・追加ができませんでした。</p>';
			}
		}
	}
	$_SESSION['messages'] = $messages;
	$db = NULL;
	unset($_SESSION['token_add']);
	header('Location:./item_list.php');
	exit();
} else {
	die('二重投稿です');
}