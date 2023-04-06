<?php
session_start();
require_once '../include/model/model.php';
check_session_admin($_SESSION['user_name']);

// 多重投稿チェック
if (!isset($_POST['token_mng']) || $_POST['token_mng'] !== $_SESSION['token_mng']) {
	die('不正なアクセスです');
} elseif ($_POST['token_mng'] === $_SESSION['token_mng']) {
	$messages_add = '';
	$db = get_db();
	// 商品追加
	if ($_POST['add_item']) {
		$item_name = $_POST['item_name'];
		$price     = $_POST['price'];
		$stock     = $_POST['stock'];
		$status    = $_POST['status'];
		if ($status === '公開') {
			$status = 1;
		} else {
			$status = 0;
		}
		$img_name  = $_FILES['upload_img']['name'];
		$errmsg_item_name = '';
		$errmsg_price     = '';
		$errmsg_stock     = '';
		$errmsg_img       = '';
		$ext_pre = pathinfo($img_name);
		$ext = $ext_pre['extension'];
		$judge = TRUE;
		if ($item_name === '0') { // empty()は0が入力されていてもTRUEを返してしまうので0の入力をエラーにしないようにする
			$errmsg_item_name = '';
		} elseif (empty($item_name) || $item_name === '') {
			$errmsg_item_name = '※エラー：商品名を入力してください。';
			$_SESSION['errmsg_item_name'] = $errmsg_item_name;
		}
		if ($price === '0') {
			$errmsg_price = '';
		} elseif (empty($price) || (intval($price) < 0) || preg_match('/[0-9]+/u', $price) === 0 || $price === '') {
			$errmsg_price = '※エラー：値段は0以上の整数で入力してください。';
			$_SESSION['errmsg_price'] = $errmsg_price;
		}
		if ($stock === '0') {
			$errmsg_stock = '';
		} elseif (empty($stock) || (intval($stock) < 0) || $stock === '') {
			$errmsg_stock = '※エラー：在庫数は0以上の整数で入力してください。';
			$_SESSION['errmsg_stock'] = $errmsg_stock;
		}
		if (empty($img_name)) {
			$errmsg_img = '※エラー：画像が選択されていません。';
			$_SESSION['errmsg_img'] = $errmsg_img;
		} elseif (!($ext === 'jpeg' || $ext === 'png')) {
			$errmsg_img = '※エラー：画像ファイルの拡張子を確認してください。';
			$_SESSION['errmsg_img'] = $errmsg_img;
		} else {
			$errmsg_img = '';
		}
		// 全ての入力チェックでエラーメッセージが出るか確認。1つもエラーメッセージがなければ追加処理開始
		$judge_materials = [$errmsg_item_name === '',$errmsg_price === '',
							$errmsg_stock === '', $errmsg_img === '',];
		foreach ($judge_materials as $target) {
			$judge = ($judge && $target);
		}
		if ($judge) {
			try {
				$db -> beginTransaction();
					insert_into_item_table($db, $item_name, $price, $img_name, $status);
					// ec_item_tableに追加された商品と同じitem_idをもつ商品をec_stock_tableに追加
					$item_id = $db -> lastInsertId();
					insert_into_stock_table($db, $item_id, $stock);
					// 画像をサーバにアップロード
					$upload_dir = 'item_img/';
					$upload = $upload_dir . basename($_FILES['upload_img']['name']);
					move_uploaded_file($_FILES['upload_img']['tmp_name'], $upload);
				$db -> commit();
			} catch(PDOException $e) {
				$db -> rollback();
				die("エラーコード{echo $e -> getCode()}");
			}
			$messages_add = '<p class="messages_green">商品は正常に追加されました。</p>';
			$_SESSION['messages_add'] = $messages_add;
		} else {
			$messages_add = '<p class="messages_red">不正な入力項目があります。再入力してください。</p>';
			$_SESSION['messages_add'] = $messages_add;
		}
		$db = NULL;
		unset($_SESSION['token_mng']);
		header('Location:./item_mng.php');
		exit();
	}
} else {
	die('二重投稿です');
}