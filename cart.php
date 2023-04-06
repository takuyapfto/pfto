<?php
session_start();
require_once '../include/model/model.php';
check_session($_SESSION['user_name']);
$user_name = $_SESSION['user_name'];

if (isset($_SESSION['compare_amount_stock'])) {
	$compare_amount_stock = $_SESSION['compare_amount_stock'];
}
if (isset($_SESSION['compare_status'])) {
	$compare_status = $_SESSION['compare_status'];
}
if (isset($_SESSION['require_delete'])) {
	$require_delete = $_SESSION['require_delete'];
}

// 購入完了後、ブラウザの戻る機能で戻った時に商品情報を表示させないためにトークン生成
$token_sccs = uniqid(bin2hex(random_bytes(13)), true);
$_SESSION['token_sccs'] = $token_sccs;

$messages = '';
$db = get_db();

// カート内商品の削除
if (isset($_POST['delete_item'])) {
	$item_id  = $_POST['item_id'];
	delete_from_cart($db, $item_id);
	$messages = '<p class="messages_green">商品は正常に削除されました。</p>';
	// $require_deleteに文章が代入されている状態で商品の削除ボタンが押下されたとき、
	// $_SESSION['compare_amount_stock']か$_SESSION['$compare_status']から当該商品のitem_idを削除する
	if (!empty($compare_amount_stock)) {
		$as_ready_am = array_search($item_id, $_SESSION['compare_amount_stock']);
		if ($as_ready_am !== FALSE) {
			unset($_SESSION['compare_amount_stock'][$as_ready_am]);
		}
	}
	if (!empty($compare_status)) {
		$as_ready_st = array_search($item_id, $_SESSION['compare_status']);
		if ($as_ready_st !== FALSE) {
			unset($_SESSION['compare_status'][$as_ready_st]);
		}
	}
}

// カート内商品の購入予定数の変更の制御
if (isset($_POST[('change_amount')])) {
	$item_id = $_POST['item_id'];
	$amount  = $_POST['amount'];
	$stock   = $_POST['stock'];
	$amount_confirm = $_POST['amount_confirm'];
	if ($amount > $stock) {
		$messages = '<p class="messages_yellow">変更後の個数が在庫数を超えています。処理されませんでした。</p>';		
	} elseif ($amount <= 0) {
		$messages = '<p class="messages_yellow">変更後の個数が0以下です。処理されませんでした。</p>';		
	} elseif ($amount === $amount_confirm) {
		$messages = '<p class="messages_yellow">変更前と変更後の個数が同じです。処理されませんでした。</p>';
	} else {
		update_on_purchase($db, $amount, $item_id);
		$messages = '<p class="messages_green">購入予定数は正常に変更されました。</p>';
	}
}

// 表示する商品情報を取得
$result_list = get_item_list_cart($db);
$_SESSION['result_list'] = $result_list;
// 商品の合計個数と合計金額を計算
$amount_sum  = calc_amount_sum($result_list);
$price_sum   = calc_price_sum($result_list);

// 商品一覧ページ右上に表示する購入予定数の合計を取得
$user_id = get_user_id($db, $user_name);
$amount_in_cart = get_amount_in_cart($db, $user_id);
$_SESSION['amount_in_cart'] = $amount_in_cart;
$db = NULL;

if (isset($_POST['logout'])) {
	logout();
}

// カートの中身の有無でviewを切り替え
if (!empty($result_list)) {
	require_once '../include/view/cart_in_view.php';
} else {
	require_once '../include/view/cart_empty_view.php';
}