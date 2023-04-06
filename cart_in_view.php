<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>カートページ</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
	<script>
		function del_confirm() {
			return window.confirm('商品を削除します。この処理は取り消せません。本当に削除しますか？');
		}
	</script>
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="../controller/item_list.php" class="title">ECサイト</a>&nbsp;→&nbsp;カートページ
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
<p><strong>カート内商品</strong></p>
<p>※購入予定数を変更する際は1以上・在庫数以下の値を入力してください。</p>
<p class="color_red">※1つでも購入予定数が0の商品がある場合は購入処理されません。</p>
<div class="item_detail_wrapper_cart">
	<div class="detail_width center">商品名</div>
	<div class="detail_width center">画像</div>
	<div class="detail_width center">購入予定数</div>
	<div class="detail_width center">値段</div>
	<div class="detail_width_stock center">在庫数</div>
	<div class="detail_width center">操作</div>
</div>
<br class="br">
<?php foreach ($result_list as $value) { ?>	
	<div class="item_detail_wrapper_cart">
		<div class="detail_width">
			<?= h($value['item_name']); ?><br>
			<p class="messages_red small_sentence"><?php if (!empty($compare_status) || !empty($compare_amount_stock)) {
					if (in_array($value['item_id'], $compare_status, true) || in_array($value['item_id'], $compare_amount_stock, true)) {
						echo  h($require_delete);
				}}?></p>
		</div>
		<div class="detail_width center">
			<img src=<?= '../controller/item_img/' . h($value['img']); ?>>
		</div>
		<div class="detail_width center">
			<?= h($value['amount']); ?>
			<form method="POST">
				<input type="hidden" name="item_id" value="<?= h($value['item_id']); ?>">
				<input type="hidden" name="amount_confirm" value="<?= h($value['amount']); ?>">
				<input type="hidden" name="stock" value="<?= h($value['stock']); ?>">
				<input type="number" name="amount" class="input_margin_bottom" min="1" max="<?= number_format(h(intval($value['stock']))); ?>"><br>
				<input type="submit" name="change_amount" value="変更" <?php if (!empty($compare_status) || !empty($compare_amount_stock)) {
					if (in_array($value['item_id'], $compare_status, true) || in_array($value['item_id'], $compare_amount_stock, true)) {
					echo 'disabled';
				}}?>>
			</form>
		</div>
		<div class="detail_width center">
			<?= number_format(h($value['price'])) . '円'; ?>
		</div>
		<div class="detail_width_stock center">
			<?= number_format(h($value['stock'])); ?>
		</div>
		<div class="detail_width center">
			<form method="POST" onsubmit="return del_confirm()">
				<input type="hidden" name="item_id" value="<?= h($value['item_id']); ?>">
				<input type="submit" name="delete_item" value="商品を削除"><br>
			</form>
		</div>
	</div>
	<br class="br">
<?php } ?>
<div class="show_sum_wrapper">
	<div class="show_sum">
		合計個数：<?= number_format(h($amount_sum)); ?>&emsp;
		合計金額：<?= number_format(h($price_sum)); ?>円
	</div>
	<div class="buy_btn_div">
		<form action="../controller/purchase.php" method="POST">
			<input type="hidden" name ="token_sccs" value ="<?= $token_sccs; ?>">
			<input type="submit" class="buy_btn" value="購入する">
		</form>
	</div>
</div>
<p><a href="../controller/item_list.php">商品一覧ページ</a></p>
<form method="POST">
	<input type="submit" name='logout' class="logout_btn" value="ログアウト">
</form>
</main>
</body>
</html>