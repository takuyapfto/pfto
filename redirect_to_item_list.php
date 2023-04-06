<?php
session_start();
require_once '../include/model/model.php';
check_session($_SESSION['user_name']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ログイン済みリダイレクトページ</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
	<script>
		setTimeout(function () {
			window.location.href = './item_list.php';
		}, 10000);
	</script>
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="./item_list.php" class="title">ECサイト</a>&nbsp;→&nbsp;商品一覧リダイレクトページ
	</div>
</header>
<main>
<p>ログイン済みです。10秒後、自動的に商品一覧ページに移動します。</p>
<p>移動しない場合は<a href="./item_list.php">こちら</a>をクリックしてください。</p>
</main>
</body>
</html>