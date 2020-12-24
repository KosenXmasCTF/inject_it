<?php 

$FLAG = file_get_contents("/var/www/flag.txt");

$password = "";
if (isset($_GET["password"])) {
	$password = $_GET["password"];
}

$pdo = new PDO('sqlite:/var/www/user.db');

function recursive_replace($pattern, $replacement, $subject) {
	while (preg_match($pattern, $subject) !== 0) {
		$subject = preg_replace($pattern, $replacement, $subject);
	}
	return $subject;
}

$query_password = $password;
$query_password = preg_replace("(')", "\\'", $query_password);
$query_password = preg_replace("([Oo][Rr]|=|[0-9]| |--)", "", $query_password);
$query_password = recursive_replace("(password|name)", "", $query_password);

$query = "SELECT * FROM user WHERE name='admin' AND password='$query_password'";
if (isset($_GET["password"])) {
	echo htmlspecialchars($query, ENT_QUOTES);
}

$stmt = $pdo->query($query);

$login = $stmt && $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login</title>
</head>
<body>
	<h1>管理者ログイン</h1>
	<form method="GET">
		<p>パスワード: <input type="text" name="password" value="<?= htmlspecialchars($password, ENT_QUOTES); ?>"></p>
		<input type="submit" value="送信">
	</form>
	<p>
		<?php
		if ($login) {
			echo $FLAG;
		} else if (isset($_GET["password"])) {
			echo "ログインに失敗しました。";
		} ?>
	</p>
</body>
</html>
