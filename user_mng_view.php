<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ユーザ管理ページ</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="../controller/item_mng.php" class="title">ECサイト</a>&nbsp;→&nbsp;ユーザ管理ページ
	</div>
</header>
<main>
<p class="top_link"><a href="../controller/item_mng.php">商品管理ページ</a></p>
	<table class="user_mng_table">
		<caption><strong>登録ユーザ情報一覧</strong></caption>
		<tr>
			<th>ユーザ名</th>
			<th>登録日時</th>
		</tr>
		<?php foreach ($result_uc as $value) { ?>
		<tr>
			<td><?= h($value['user_name']); ?></td>
			<td><?= h($value['created_date']); ?></td>
		</tr>
		<?php } ?>
	</table><br>
<form method="POST">
	<input type="submit" name="logout" class="logout_btn" value="ログアウト">
</form>
</body>
</html>