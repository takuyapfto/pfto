<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ログインページ</title>
	<link rel="stylesheet" type="text/css" href="../include/view/styles.css">
	<script>
	// パスワード入力フォームの伏字化のオン・オフ
	document.addEventListener('DOMContentLoaded', function(event) {
		const targetElement  = document.getElementById('pw');
		const triggerElement = document.getElementById('show_pw');
		triggerElement.addEventListener('change', function(event) {
			if (this.checked) {
				targetElement.setAttribute('type', 'text');
			} else {
				targetElement.setAttribute('type', 'password');
			}
		}, false);
	}, false);
	</script>
</head>
<body>
<header>
	<div class="header_wrapper">
		<a href="../controller/login.php" class="title">ECサイト</a>&nbsp;→&nbsp;ログインページ
	</div>
</header>
<main> 
<?= '<p class="messages_red">' . $messages . '</p>'; ?>
<div class="body_wrapper">
	<div class="contents_wrapper">
		<div class="contents_forms">
			<form method="POST">
				<input type="text" name="user_name" class="input_form" placeholder="ユーザ名"><br>
				<input type="password" name="password" id="pw" class="input_form" placeholder="パスワード"><br>
				<label for="show_pw"><input id="show_pw" class="pw_hide" type="checkbox"> パスワードを表示する</label><br>
				<input type="submit" name="login_btn" class="login_btn_css" value="ログイン">
			</form>
			<p><a href="../controller/sign_up.php">ユーザ登録ページはこちら</a></p>
		</div>
	</div>
</div>
</main>
</body>
</html>