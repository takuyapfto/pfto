<?php
session_start();

/**
 * ログイン済み（セッションの有無）確認
 *
 * @param mixed $str
 * @return void
 */
function check_session($str) {
	if (!isset($str)) {
		header('Location:../controller/redirect_to_login.php');
		exit();
	}	
}

/**
 * 管理アカウントログイン済み（セッションの有無）確認
 *
 * @param string $str
 * @return void
 */
function check_session_admin($str) {
	if ($str !== 'admin') {
		header('Location:../controller/redirect_to_login.php');
		exit();
	}	
}

/**
 * ログアウト
 *
 * @return void
 */
function logout() {
	$_SESSION = [];
	session_unset();
	header('Location:../controller/logout.php');
	exit();
}

/**
 * XSS対策
 * 
 * @param string $str
 * @return string $str
 */
function h($str) {
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * データベース接続
 *
 * @return object $db
 */
function get_db():PDO {
	try {
		$db_host = 'SecretForGitHub';
		$db_user = 'SecretForGitHub';
		$db_pw   = 'SecretForGitHub';
		$db_name = 'SecretForGitHub';
		$dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8";
		$db  = new PDO($dsn, $db_user, $db_pw);
		$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $db;
}

/**
 * ログイン時のパスワード照合のためのユーザ名取得
 *
 * @param object $db
 * @param string $user_name
 * @return string $result_un['password']
 */
function get_un_pw($db, $user_name) {
	try {
		$sql   = 'SELECT user_name, password FROM ec_user_table WHERE user_name = :user_name';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':user_name', $user_name, PDO::PARAM_STR);
		$stmt -> execute();
		$result_un = $stmt -> fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	if (!empty($result_un['password'])) {
		return $result_un['password'];
	} else {
		return;
	}
}

/**
 * ユーザ登録に際し既存のユーザ名と重複していれば取得
 *
 * @param object $db
 * @param string $user_name
 * @return string $result['user_name']
 */
function check_same_user_name($db, $user_name) {
	try {
		$sql   = 'SELECT user_name FROM ec_user_table WHERE user_name = :user_name';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':user_name', $user_name, PDO::PARAM_STR);
		$stmt -> execute();
		$result = $stmt -> fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	if (!empty($result['user_name'])) {
		return $result['user_name'];
	} else {
		return;
	}
}

/**
 * 管理する商品を新規追加
 *
 * @param object $db
 * @param string $item_name
 * @param int $price
 * @param string $img_name
 * @param int $status
 * @return void
 */
function insert_into_item_table($db, $item_name, $price, $img_name, $status) {
	try{
		$sql   = 'INSERT INTO ec_item_table(item_name, price, img, status) VALUES(:item_name, :price, :img, :status)';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_name', $item_name, PDO::PARAM_STR);
		$stmt -> bindValue(':price', $price, PDO::PARAM_INT);
		$stmt -> bindValue(':img', $img_name, PDO::PARAM_STR);
		$stmt -> bindValue(':status', $status, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * 新規追加した商品の在庫数を新規追加
 *
 * @param object $db
 * @param int $item_id
 * @param int $stock
 * @return void
 */
function insert_into_stock_table($db, $item_id, $stock) {
	try {
		$sql   = 'INSERT INTO ec_stock_table(item_id, stock) VALUES(:item_id, :stock)';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> bindValue(':stock', $stock, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * 商品の在庫数を変更
 *
 * @param object $db
 * @param int $changing_stock
 * @param int $item_id
 * @return void
 */
function update_stock_func($db, $changing_stock, $item_id) {
	try {
		$sql   = 'UPDATE ec_stock_table SET stock = :changing_stock WHERE item_id = :item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':changing_stock', $changing_stock, PDO::PARAM_INT);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * 公開ステータスを変更
 *
 * @param object $db
 * @param int $status
 * @param int $item_id
 * @return void
 */
function update_status_func($db, $status, $item_id) {
	try {
		$sql   = 'UPDATE ec_item_table SET status = :status WHERE item_id = :item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':status', $status, PDO::PARAM_INT);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * ec_item_tableから商品を削除
 *
 * @param object $db
 * @param int $item_id
 * @return void
 */
function delete_from_item_table($db, $item_id) {
	try {
		$sql   = 'DELETE FROM ec_item_table WHERE item_id = :item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * ec_stock_tableから商品を削除
 *
 * @param object $db
 * @param int $item_id
 * @return void
 */
function delete_from_stock_table($db, $item_id) {
	try {
		$sql   = 'DELETE FROM ec_stock_table WHERE item_id = :item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * 商品管理ページで表示する商品一覧情報を取得
 *
 * @param object $db
 * @return array $result
 */
function get_item_list_mng($db) {
	try {
		$sql = 'SELECT ec_item_table.item_name, ec_item_table.img, ec_item_table.price, ec_item_table.status,
				ec_stock_table.stock, ec_item_table.item_id FROM ec_item_table
				INNER JOIN ec_stock_table WHERE ec_item_table.item_id = ec_stock_table.item_id';
		$stmt = $db -> prepare($sql);
		$stmt -> execute();
		$result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result;
}

/**
 * 新規ユーザ登録
 *
 * @param object $db
 * @param string $user_name
 * @param string $password_hashed
 * @return void
 */
function insert_into_user_table($db, $user_name, $password_hashed) {
	try {
		$sql   = 'INSERT INTO ec_user_table(user_name, password) VALUES (:user_name, :password)';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':user_name', $user_name, PDO::PARAM_STR);
		$stmt -> bindValue(':password', $password_hashed, PDO::PARAM_STR);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * ユーザ名と新規登録日時を取得
 *
 * @param object $db
 * @return array $result_uc
 */
function get_un_cd($db) {
	try {
		$sql    = 'SELECT user_name, created_date FROM ec_user_table';
		$stmt   = $db->prepare($sql);
		$stmt  -> execute();
		$result_uc = $stmt -> fetchAll(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_uc;
}

/**
 * ec_user_tableを使いuser_nameからuser_idを取得
 *
 * @param object $db
 * @param string $user_name
 * @return int $result_id['user_id']
 */
function get_user_id($db, $user_name) {
	try {
		$sql   = 'SELECT user_id FROM ec_user_table WHERE user_name = :user_name';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':user_name', $user_name, PDO::PARAM_STR);
		$stmt -> execute();
		$result_uid = $stmt -> fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_uid['user_id'];
}

/**
 * 商品一覧ページで表示する商品情報を取得
 *
 * @param object $db
 * @return array $result_item_id
 */
function get_item_list($db) {
	try {
		$sql   = 'SELECT ec_item_table.item_id, ec_item_table.item_name, ec_item_table.price, ec_item_table.img,
				ec_item_table.status, ec_stock_table.stock FROM ec_item_table
				INNER JOIN ec_stock_table ON ec_item_table.item_id = ec_stock_table.item_id
				WHERE ec_item_table.status = 1';
		$stmt  = $db -> prepare($sql);
		$stmt -> execute();
		$result_item_id = $stmt -> fetchAll(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_item_id;
}

/**
 * カート内に既にある商品のidを取得
 *
 * @param object $db
 * @param int $item_id
 * @param int $user_id
 * @return int $result_c['item_id']
 */
function get_item_already_in_cart($db, $item_id, $user_id) {
	try {
		$sql   = 'SELECT item_id FROM ec_cart_table WHERE item_id = :item_id AND user_id = :user_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt -> execute();
		$result_c = $stmt -> fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_c['item_id'];
}

/**
 * カートに商品を追加
 *
 * @param object $db
 * @param int $user_id
 * @param int $item_id
 * @param int $amount
 * @return void
 */
function add_into_cart($db, $user_id, $item_id, $amount) {
	try {
		$sql   = 'INSERT INTO ec_cart_table(user_id, item_id, amount) VALUES(:user_id, :item_id, :amount)';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> bindValue(':amount', $amount, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * カート内に既にある1つの商品の購入予定数を取得
 *
 * @param object $db
 * @param int $item_id
 * @param int $user_id
 * @return int $result_a['amount']
 */
function check_amount_in_cart($db, $item_id, $user_id) {
	try {
		$sql   = 'SELECT amount FROM ec_cart_table WHERE item_id = :item_id AND user_id = :user_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt -> execute();
		$result_a = $stmt -> fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_a['amount'];
}

/**
 * カート内に既にある1つの商品の在庫数を取得
 *
 * @param object $db
 * @param int $item_id
 * @return int $result_s['stock']
 */
function check_stock($db, $item_id) {
	try {
		$sql   = 'SELECT stock FROM ec_stock_table WHERE item_id = :item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> execute();
		$result_s = $stmt -> fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_s['stock'];
}

/**
 * 購入予定数を1だけ増加
 *
 * @param object $db
 * @param int $item_id
 * @param int $user_id
 * @return void
 */
function increase_amount_one($db, $item_id, $user_id) {
	try {
		$sql   = 'UPDATE ec_cart_table SET amount = amount + 1 WHERE item_id = :item_id AND user_id = :user_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * カートページで表示する購入予定数の合計を取得
 *
 * @param object $db
 * @param int $user_id
 * @return int $result_am['SUM(amount)']
 */
function get_amount_in_cart($db, $user_id) {
	try {
		$sql   = 'SELECT SUM(amount) FROM ec_cart_table WHERE user_id = :user_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt -> execute();
		$result_am = $stmt -> fetch(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_am['SUM(amount)'];
}

/**
 * 購入予定数・購入数の合計の計算
 * 
 * @param array $result_list_over0
 * @return int $amount_sum_pre
 */
function calc_amount_sum($result_list_over0) {
	$amount_sum_pre = 0;
	foreach ($result_list_over0 as $value_sum) {
		$amount_sum_pre = $amount_sum_pre + intval($value_sum['amount']);
	}
	return $amount_sum_pre;
}

/**
 * カートページで表示する購入予定商品一覧の情報を取得
 *
 * @param object $db
 * @return array $result_list;
 */
function get_item_list_cart($db) {
	try {
		$sql   = 'SELECT ec_item_table.item_id, ec_item_table.item_name, ec_item_table.price, ec_item_table.img,
				ec_cart_table.amount, ec_stock_table.stock FROM ec_item_table
				INNER JOIN ec_cart_table ON ec_item_table.item_id = ec_cart_table.item_id
				INNER JOIN ec_stock_table ON ec_item_table.item_id = ec_stock_table.item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> execute();
		$result_list = $stmt -> fetchAll(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_list;
}

/**
 * 合計金額の計算
 * 
 * @param array $result_list_over0
 * @return int $price_sum_pre
 */
function calc_price_sum($result_list_over0) {
	$price_sum_pre = 0;
	foreach ($result_list_over0 as $value_sum) {
		$price_sum_pre = $price_sum_pre + (intval($value_sum['price']) * intval($value_sum['amount']));
	}
	return $price_sum_pre;
}

/**
 * カート内の商品を削除
 *
 * @param object $db
 * @param int $item_id
 * @return void
 */
function delete_from_cart($db, $item_id) {
	try {
		$sql   = 'DELETE FROM ec_cart_table WHERE item_id = :item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * カート内の購入予定商品の個数を変更
 *
 * @param object $db
 * @param int $amount
 * @param int $item_id
 * @return void
 */
function update_on_purchase($db, $amount, $item_id) {
	try {
		$sql   = 'UPDATE ec_cart_table SET amount = :amount WHERE item_id = :item_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':amount', $amount, PDO::PARAM_INT);
		$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
		$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}

/**
 * 購入処理時の購入予定数と在庫数の比較
 *
 * @param array $result_list
 * @return array $stock_alert_array
 */
function compare_amount_stock($result_list) {
	$item_id_array = array();
	$stock_array = array();
	$stock_alert_array = array();
	$db = get_db();
	foreach ($result_list as $value) {
		$item_id = $value['item_id'];
		try {
			$sql   = 'SELECT stock FROM ec_stock_table WHERE item_id = :item_id';
			$stmt  = $db -> prepare($sql);
			$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
			$stmt -> execute();
			$stock = $stmt -> fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			die("エラーコード{echo $e -> getCode()}");
			exit();
		}
		array_push($stock_array, $stock['stock']);
		array_push($item_id_array, $item_id);
	}
	for ($i = 0; $i < count($stock_array); $i++) {
		if ($stock_array[$i] === 0) { // $stock_arrayが0となるときと同じ配列順にあるitem_idを追加する
			array_push($stock_alert_array, $item_id_array[$i]);
		}
	}
	$db = NULL;
	return $stock_alert_array;
}

/**
* 購入処理時の公開ステータス確認	
 *
 * @param object $result_list
 * @return array $status_alert_array
 */
function compare_status($result_list) {
	$status_array  = array();
	$item_id_array = array();
	$status_alert_array = array();
	$db = get_db();
	foreach ($result_list as $value) {
		$item_id = $value['item_id'];
		try {
			$sql   = 'SELECT item_id, status FROM ec_item_table WHERE item_id = :item_id';
			$stmt  = $db -> prepare($sql);
			$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
			$stmt -> execute();
			$status = $stmt -> fetch(PDO::FETCH_ASSOC);
		} catch(PDOException $e) {
			die("エラーコード{echo $e -> getCode()}");
			exit();
		}
		array_push($status_array, $status['status']);
		array_push($item_id_array, $item_id);
	}
	for ($i = 0; $i < count($status_array); $i++) {
		if ($status_array[$i] === 0) {// $status_arrayが0となるときと同じ配列順にあるitem_idを追加する
			array_push($status_alert_array, $item_id_array[$i]);
		}
	}
	$db = NULL;
	return $status_alert_array;
}


/**
 * カートに商品を入れた後に公開ステータスが非公開になっていた商品は、取り扱いがないのに在庫があるとおかしいので在庫を0にする
 *
 * @param array $compare_status
 * @return void
 */
function set_stock_zero($compare_status) {
	$db = get_db();
	for ($i = 0; $i < count($compare_status); $i++) {
		$item_id = $compare_status[$i];
		try {
			$sql   = 'UPDATE ec_stock_table SET stock = 0 WHERE item_id = :item_id';
			$stmt  = $db -> prepare($sql);
			$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
			$stmt -> execute();
		} catch(PDOException $e) {
			die("エラーコード{echo $e -> getCode()}");
			exit();
		}
	}
	$db = NULL;
}

/**
 * 購入完了ページの商品一覧情報を取得
 *
 * @param object $db
 * @param int $user_id
 * @return array $result_list
 */
function purchased_item_list($db, $user_id) {
	try {
		$sql = 'SELECT ec_item_table.item_id, ec_item_table.item_name, ec_item_table.price, ec_item_table.img,
				ec_cart_table.amount, ec_stock_table.stock FROM ec_item_table
				INNER JOIN ec_cart_table ON ec_item_table.item_id = ec_cart_table.item_id
				INNER JOIN ec_stock_table ON ec_item_table.item_id = ec_stock_table.item_id
				WHERE ec_cart_table.amount >= 1 AND ec_cart_table.user_id = :user_id';
		$stmt  = $db -> prepare($sql);
		$stmt -> bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$stmt -> execute();
		$result_list = $stmt -> fetchAll(PDO::FETCH_ASSOC);
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
	return $result_list;
}

/**
 * 購入された商品の在庫数を減らす
 *
 * @param object $db
 * @param int $item_id
 * @param int $amount
 * @param int $stock
 * @return void
 */
function update_stock_on_purchase($db, $item_id, $amount, $stock) {
	try {
	$sql   = 'UPDATE ec_stock_table SET stock = :stock - :amount WHERE item_id = :item_id';
	$stmt  = $db -> prepare($sql);
	$stmt -> bindValue(':item_id', $item_id, PDO::PARAM_INT);
	$stmt -> bindValue(':amount', $amount, PDO::PARAM_INT);
	$stmt -> bindValue(':stock', $stock, PDO::PARAM_INT);
	$stmt -> execute();
	} catch(PDOException $e) {
		die("エラーコード{echo $e -> getCode()}");
		exit();
	}
}