<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>未ログインリダイレクトページ</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
	<script>
		setTimeout(function () {
			window.location.href = './login.php';
		}, 10000);
	</script>
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="./login.php" class="title">ECサイト</a>&nbsp;→&nbsp;未ログインリダイレクトページ
	</div>
</header>
<main>
<p>ログインしていないか、管理者用アカウントではありません。10秒後、自動的にログインページに移動します。</p>
<p>移動しない場合は<a href="./login.php">こちら</a>をクリックしてください。</p>
</main>
</body>
</html>