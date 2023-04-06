<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>購入完了ページ（情報異常）</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="../controller/item_list.php" class="title">ECサイト</a>&nbsp;→&nbsp;購入完了ページ（情報異常）
		<div class="header_right clear">
			<strong><a href="../controller/art.php" class="link_cart">カート(<?php
				if (empty($amount_in_cart)) {
					echo '0';
				} else {
					echo $amount_in_cart;
				} ?>)</a></strong>
		</div>
	</div>
</header>
<main>
	<p class="empty_msg">購入数が在庫数を超えている商品がある・取り扱いのなくなった商品が含まれているなどの理由により、処理されませんでした。<br>カートをご確認ください。</p>
	<p><a href="../controller/item_list.php">商品一覧ページ</a></p>
	<p><a href="../controller/cart.php">カートページ</a></p>
<form method="POST">
	<input type="submit" name='logout' class="logout_btn" value="ログアウト">
</form>
</main>
</body>
</html>