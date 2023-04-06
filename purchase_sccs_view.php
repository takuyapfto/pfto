<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>購入完了ページ</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="../controller/item_list.php" class="title">ECサイト</a>&nbsp;→&nbsp;購入完了ページ
		<div class="header_right clear">
			<strong><a href="../controller/cart.php" class="link_cart">カート(<?php
				if (empty($amount_in_cart)) {
					echo '0';
				} else {
					echo $amount_in_cart;
				} ?>)</a></strong>
		</div>
	</div>
</header>
<main>
<?= $messages; ?>
<div class="item_detail_wrapper_sccs">
	<div class="detail_width_sccs center">商品名</div>
	<div class="detail_width_sccs center">画像</div>
	<div class="detail_width_sccs center">購入数</div>
	<div class="detail_width_sccs center">値段</div>
</div>
<br class="br">
<?php foreach ($result_list_over0 as $value) { ?>	
	<div class="item_detail_wrapper_sccs center">
		<div class="detail_width_sccs">
			<?= h($value['item_name']); ?>
		</div>
		<div class="detail_width_sccs center">
			<img src=<?='../controller/item_img/' . h($value['img']); ?>>
		</div>
		<div class="detail_width_sccs center">
			<?= h($value['amount']); ?>
		</div>
		<div class="detail_width_sccs center">
			<?= number_format(h($value['price'])); ?>円
		</div>
	</div>
	<br class="br">
<?php } ?>
<div class="show_sum_wrapper_sccs">
	<div class="show_sum">
		合計個数：<?= number_format(h($amount_sum)); ?>&emsp;
		合計金額：<?= number_format(h($price_sum)); ?>円
	</div>
</div>
<p><a href="../controller/item_list.php">商品一覧ページ</a></p>
<form method="POST">
	<input type="submit" name='logout' class="logout_btn" value="ログアウト">
</form>
</main>
</body>
</html>