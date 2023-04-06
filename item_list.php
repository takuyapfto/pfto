<?php
session_start();
require_once '../include/model/model.php';
check_session($_SESSION['user_name']);
$user_name = $_SESSION['user_name'];
$messages  = '';

// 商品カート追加時の多重追加防止用トークン生成
$token_add = uniqid(bin2hex(random_bytes(13)), true);
$_SESSION['token_add'] = $token_add;

// カート追加完了・失敗のメッセージを取得
if (isset($_SESSION['messages'])) {
	$messages = $_SESSION['messages'];
}
unset($_SESSION['messages']);

// 表示する商品情報を取得
$db = get_db();
$result_item_id = get_item_list($db);

// 商品一覧ページ右上に表示する購入予定数の合計を取得
$user_id = get_user_id($db, $user_name);
$amount_in_cart = get_amount_in_cart($db, $user_id);
$db = NULL;

if (isset($_POST['logout'])) {
	logout();
}

require_once '../include/view/item_list_view.php';