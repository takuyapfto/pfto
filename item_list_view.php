<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>商品一覧ページ</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="../controller/item_list.php" class="title">ECサイト</a>&nbsp;→&nbsp;商品一覧ページ
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
<p>カートに入れた商品が既にカートの中にあった場合は、カートに入れた個数に関わらず、購入数を１増やします。</p>
<div class="item_wrapper">
	<?php foreach ($result_item_id as $value) { ?>
		<div class="item_detail_wrapper">
			<div class="item_detail">
				<img src=<?= '../controller/item_img/' . h($value['img']); ?>><br>
				<?= h($value['item_name']); ?><br>
				<?= number_format(h($value['price'])); ?>円<br>
				在庫数：<?= number_format(h(intval($value['stock']))); ?><br>
				<?php if (intval($value['stock']) >= 1) { ?>
				<form action="../controller/item_list_execute.php" method="POST">
					<input type="hidden" name="item_id" value="<?= h($value['item_id']); ?>">
					<input type="hidden" name ="token_add" value ="<?= $token_add; ?>">
					<input type="number" name="amount" class="input_margin_bottom" min="0" max="<?= number_format(h(intval($value['stock']))); ?>"><br>
					<input type="submit" name="put_into_cart" value="カートに入れる">
				</form>
				<?php } else { echo '売り切れ'; }?>
			</div>
		</div>
	<?php }; ?>
</div>
<p><a href="../controller/cart.php">カートページ</a></p>
<form method="POST">
	<input type="submit" name='logout' class="logout_btn" value="ログアウト">
</form>
</main>
</body>
</html>