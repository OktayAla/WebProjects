<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php
	require_once __DIR__ . '/includes/auth.php';
	if (current_user()) {
		header('Location: index.php');
		exit;
	}

	$error = '';
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		if (!$identifier || !$password) {
			$error = 'Kullanıcı adı, isim veya e-posta ve şifre gerekli.';
		} else if (!login($identifier, $password)) {
			$error = 'Geçersiz giriş bilgileri.';
		} else {
			header('Location: index.php');
			exit;
		}
	}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Giriş</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/login.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center bg-light">


	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-4">
				<div class="card shadow-sm border-0">
					<div class="card-body">
						<!-- Logo ve animasyonlu giriş -->
<div class="login-logo-container">
    <img src="img/logo.jpg" alt="Logo" class="login-logo-img">
</div>
<?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
<form method="post" autocomplete="off">
	<div class="mb-3">
		<label class="form-label">Kullanıcı adı</label>
		<input type="text" name="identifier" class="form-control" required>
	</div>
	<div class="mb-3">
		<label class="form-label">Şifre</label>
		<input type="password" name="password" class="form-control" required>
	</div>
	<button class="btn btn-primary w-100" type="submit">Giriş Yap</button>
</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- Not: login.css'de login-logo-container, login-logo-circle ve animate-logo class'ları tanımlanmalı. -->