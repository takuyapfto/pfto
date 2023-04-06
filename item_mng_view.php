<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>商品管理ページ</title>
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
		<a href="../controller/item_mng.php" class="title">ECサイト</a>&nbsp;→&nbsp;商品管理ページ
	</div>
</header>
<main>
<?= $messages; ?><?= $messages_add; ?>
<p class="top_link"><a href="../controller/user_mng.php">ユーザ管理ページ</a></p>
<form enctype="multipart/form-data" action="../controller/item_mng_execute.php" method="POST">
	<table class="adding_item_table">
		<caption><strong>追加商品情報入力</strong></caption>
		<tr>
			<td class="listing">商品名</td>
			<td><input type="text" name="item_name"><?= '<span class="messages_red">' . $errmsg_item_name . '</span>'; ?></td>
		</tr>
		<tr>
			<td class="listing">値段</td>
			<td><input type="text" name="price"><?= '<span class="messages_red">' . $errmsg_price . '</span>'; ?><br>0以上の整数で入力してください。</td>
		</tr>
		<tr>
			<td class="listing">在庫数</td>
			<td><input type="number" name="stock" class="listing_stock" min="0" step="1"><?= '<span class="messages_red">' . $errmsg_stock . '</span>'; ?><br>0以上の整数で入力してください。</td>
		</tr>
		<tr>
			<td class="listing">公開ステータス</td>
			<td><select name="status"><option value="公開">公開</option><option value="非公開">非公開</option></select></td>
		</tr>
		<tr>
			<td class="listing">画像ファイル</td>
			<td><input type="file" name="upload_img" accept=".jpeg, .png">
				<?= '<span class="messages_red">' . $errmsg_img . '</span>'; ?><br>
				アップロードできる画像ファイルの拡張子はjpegまたはpngのみです。</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" name="add_item" value="商品を追加する"></td>
		</tr>
	</table>
	<input type="hidden" name ="token_mng" value ="<?= $token_mng; ?>">
</form>
<table class="item_mng_list_table">
	<th>商品名</th>
	<th>商品画像</th>
	<th>値段</th>
	<th>在庫数</th>
	<th>公開ステータス</th>
	<th>削除</th>
	<?php foreach($result as $value) {
		if ($value['status'] === 1) {
			$value['status'] = '公開';
		} else {
			$value['status'] = '非公開';
		}
	?>
	<tr>
		<td>
			<?= h($value['item_name']); ?>
		</td>
		<td class="center">
			<img src=<?= '../controller/item_img/' . h($value['img']); ?>>
		</td>
		<td class="right">
			<?= number_format(h($value['price'])) . '円'; ?>
		</td>
		<td class="center">
			<?= number_format(h($value['stock'])); ?><br><br>
			<form method="POST">
				<input type="hidden" name="stock" value="<?= number_format(h($value['stock'])); ?>">
				<input type="hidden" name="item_id" value="<?= h($value['item_id']); ?>">
				<input type="number" name="changing_stock" class="input_margin_bottom" min="0" step="1"><br>
				<span class="right"><input type="submit" name="update_stock" value="変更"></span>
			</form>
		</td>
		<td>
			現在：「<?= h($value['status']); ?>」<br><br>
			<form method="POST">
				<input type="hidden" name="item_id" value="<?= h($value['item_id']); ?>">
				<input type="hidden" name="status_confirm" value="<?= h($value['status']); ?>">
				<select name="status"><option value="公開">公開</option><option value="非公開">非公開</option></select><br>
				<input type="submit" name="update_status" value="公開ステータスを変更">
			</form>
		</td>
		<td class="center">
			<form method="POST" onsubmit="return del_confirm()">
				<input type="hidden" name="item_id" value="<?= h($value['item_id']) ; ?>">
				<input type="submit" name="delete_item" value="商品を削除"><br>
			</form>
		</td>
	</tr>
	<?php } ?>
</table><br>
<form method="POST">
	<input type="submit" name="logout" class="logout_btn" value="ログアウト">
</form>
</main>
</body>
</html>