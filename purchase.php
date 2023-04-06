<?php
session_start();
require_once '../include/model/model.php';
check_session($_SESSION['user_name']);
$user_name      = $_SESSION['user_name'];
$amount_in_cart = $_SESSION['amount_in_cart'];
$result_list    = $_SESSION['result_list'];
if (isset($_POST['token_sccs'])) {
	$token_sccs_p = $_POST['token_sccs'];
}
if (isset($_SESSION['token_sccs'])) {
	$token_sccs_s = $_SESSION['token_sccs'];
} else {
	$token_sccs_s = '';
}
// $result_listの中身[0]~[N]
// Array ([N] => Array ( [item_id] => A [item_name] => B [price] => C [img] => D [amount] => E [stock] => F ))

if (isset($_POST['logout'])) {
	logout();
}

// 何らかの理由で購入予定数が在庫数を上回った場合に処理しないための比較
$compare_amount_stock = compare_amount_stock($result_list);
$_SESSION['compare_amount_stock'] = $compare_amount_stock;
// 何らかの理由で公開ステータスが非公開になっていた場合に処理しないための比較
$compare_status = compare_status($result_list);
$_SESSION['compare_status'] = $compare_status;
// 在庫数か公開ステータスに異常があった場合に表示するメッセージ
if (!empty($compare_amount_stock) || !empty($compare_status)) {
	$require_delete = '現在お取り扱いできません。お手数ですが、削除してください。';
	$_SESSION['require_delete'] = $require_delete;
} 

if ($compare_status) { //公開ステータスが非公開になっていた商品があった場合は、在庫を0にして購入完了ページ（情報異常）を表示
	set_stock_zero($compare_status);
	require_once '../include/view/purchase_error_view.php';
} elseif ($compare_amount_stock) { //  購入数が在庫数を超過する商品があった場合は購入完了ページ（情報異常）を表示
	require_once '../include/view/purchase_error_view.php';
} elseif (in_array(0, array_column($result_list, 'amount'), true)) {// 1つでも購入予定数が0の商品がある場合は購入完了ページ(0有)を表示
	require_once '../include/view/purchase_empty_view.php';
} else {// 購入数・在庫数・公開ステータスのすべてに問題がなければ購入処理

	$db = get_db();
	// 購入した商品情報を取得
	$user_id = get_user_id($db, $user_name);
	$result_list_over0 = purchased_item_list($db, $user_id);
	// 商品の合計個数と合計金額を計算
	$amount_sum = calc_amount_sum($result_list_over0);
	$price_sum  = calc_price_sum($result_list_over0);

	// ec_stock_tableの在庫数変更・ec_cart_tableからの商品削除
	foreach ($result_list as $value) {
		$item_id = $value['item_id'];
		$amount  = $value['amount'];
		$stock   = $value['stock'];
		try {
			$db -> beginTransaction();
				update_stock_on_purchase($db, $item_id, $amount, $stock);
				delete_from_cart($db, $item_id);
			$db -> commit();
		} catch(PDOException $e) {
			$db -> rollback();
			die("エラーコード{echo $e -> getCode()}");
			exit();	
		}
	}
	
	$messages = '<p class="messages_green space_bellow">正常に処理されました。ご購入ありがとうございました。</p>';

	// 商品一覧ページ右上に表示する購入予定数の合計を取得
	$user_id = get_user_id($db, $user_name);
	$amount_in_cart = get_amount_in_cart($db, $user_id);

	// ブラウザの戻る機能で遷移してきた場合（購入ボタンを押さずに遷移してきた場合）に、viewのヘッダ下部に表示するメッセージを表示
	if (!isset($token_sccs_p) || $token_sccs_p !== $token_sccs_s) {
		$messages = '<p class="space_bellow">購入された商品はまだありません。</span>';
	} elseif ($token_sccs_p === $token_sccs_s) {
		unset($_SESSION['token_sccs']);
	}

	$db = NULL;
	require_once '../include/view/purchase_sccs_view.php';	
}