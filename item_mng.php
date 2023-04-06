<?php
session_start();
require_once '../include/model/model.php';
check_session_admin($_SESSION['user_name']);

// 商品追加時の多重投稿防止用トークン生成
$token_mng = uniqid(bin2hex(random_bytes(13)), true);
$_SESSION['token_mng'] = $token_mng;

// item_mng_execute.phpからエラーメッセージを一度だけ引き継ぐ
$errmsg_item_name = '';
$errmsg_price     = '';
$errmsg_stock     = '';
$errmsg_img       = '';
$messages_add     = '';

if (isset($_SESSION['errmsg_item_name'])) {
	$errmsg_item_name = $_SESSION['errmsg_item_name'];
}
if (isset($_SESSION['errmsg_price'])) {
	$errmsg_price     = $_SESSION['errmsg_price'];
}
if (isset($_SESSION['errmsg_stock'])) {
	$errmsg_stock     = $_SESSION['errmsg_stock'];
}
if (isset($_SESSION['errmsg_img'])) {
	$errmsg_img       = $_SESSION['errmsg_img'];
}
if (isset($_SESSION['messages_add'])) {
	$messages_add     = $_SESSION['messages_add'];
}
unset($_SESSION['errmsg_item_name']);
unset($_SESSION['errmsg_price']);
unset($_SESSION['errmsg_stock']);
unset($_SESSION['errmsg_img']);
unset($_SESSION['messages_add']);

$messages = '';
$db = get_db();

// 以下、商品追加以外（在庫数変更・公開ステータス変更・商品削除）は多重投稿対策なし
// 在庫数変更
if (isset($_POST['update_stock'])) {
	$item_id = $_POST['item_id'];
	$stock   = $_POST['stock'];
	if (isset($_POST['changing_stock'])) {
		$changing_stock = $_POST['changing_stock'];
	}
	if ($stock === $changing_stock) {
		$messages = '<p class="messages_yellow">変更前と変更後の在庫数が同じです。処理されませんでした。</p>';
	} elseif($changing_stock === '') {
		$messages = '<p class="messages_yellow">値が入力されていません。処理されませんでした。</p>';
	} else {
		update_stock_func($db, $changing_stock, $item_id);
		$messages = '<p class="messages_green">在庫数は正常に変更されました。</p>';
	}
}

// 公開ステータス変更
if (isset($_POST['update_status'])) {
	$item_id = $_POST['item_id'];
	$status = $_POST['status'];
	if (isset($_POST['status_confirm'])){
		$status_confirm = $_POST['status_confirm'];
	}
	if ($status === $status_confirm) {
		$messages = '<p class="messages_yellow">変更前と変更後の公開ステータスが同じです。処理されませんでした。</p>';
	} else {
		if ($status === '公開') {
			$status = 1;
		} elseif ($status === '非公開') {
			$status = 0;
		}
		update_status_func($db, $status, $item_id);
		$messages = '<p class="messages_green">公開ステータスは正常に変更されました。</p>';
	}
}

// ec_item_tableとec_stock_tableから商品削除
if (isset($_POST['delete_item'])) {
	$item_id = $_POST['item_id'];
	try {
		$db -> beginTransaction();
			delete_from_item_table($db, $item_id);
			delete_from_stock_table($db, $item_id);
		$db -> commit();
		$messages = '<p class="messages_green">商品は正常に削除されました。</p>';
	} catch(PDOException $e) {
		$db -> rollback();
		die("エラーコード{echo $e -> getCode()}");
		die("エラーコード{echo $e -> getCode()}");
	}	
}

// 表示する商品情報を取得
$result = get_item_list_mng($db);
$db = NULL;

if (isset($_POST['logout'])) {
	logout();
}

require_once '../include/view/item_mng_view.php';