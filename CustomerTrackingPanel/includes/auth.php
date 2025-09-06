<?php
	session_start();
	require_once __DIR__ . '/db.php';

	function users_count() {
		$pdo = get_pdo_connection();
		return (int)$pdo->query('SELECT COUNT(*) FROM kullanicilar')->fetchColumn();
	}

	function current_user() {
		return isset($_SESSION['user']) ? $_SESSION['user'] : null;
	}

	function require_login() {
		if (!current_user()) {
			header('Location: giris.php');
			exit;
		}
	}

	function login($identifier, $password) {
		$pdo = get_pdo_connection();
		$hasUsername = false;
		try {
			$colStmt = $pdo->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'kullanicilar' AND COLUMN_NAME = 'username' LIMIT 1");
			$hasUsername = (bool)$colStmt->fetchColumn();
		} catch (Exception $e) {
			$hasUsername = false;
		}

		$user = null;
		if ($hasUsername) {
			$stmt = $pdo->prepare('SELECT id, isim, eposta, username, sifre, rol FROM kullanicilar WHERE username = ? LIMIT 1');
			$stmt->execute([$identifier]);
			$user = $stmt->fetch();
			if (!$user) {
				$stmt = $pdo->prepare('SELECT id, isim, eposta, username, sifre, rol FROM kullanicilar WHERE eposta = ? LIMIT 1');
				$stmt->execute([$identifier]);
				$user = $stmt->fetch();
				if (!$user) {
					$stmt = $pdo->prepare('SELECT id, isim, eposta, username, sifre, rol FROM kullanicilar WHERE isim = ? LIMIT 1');
					$stmt->execute([$identifier]);
					$user = $stmt->fetch();
				}
			}
		} else {
			$stmt = $pdo->prepare('SELECT id, isim, eposta, sifre, rol FROM kullanicilar WHERE eposta = ? LIMIT 1');
			$stmt->execute([$identifier]);
			$user = $stmt->fetch();
			if (!$user) {
				$stmt = $pdo->prepare('SELECT id, isim, eposta, sifre, rol FROM kullanicilar WHERE isim = ? LIMIT 1');
				$stmt->execute([$identifier]);
				$user = $stmt->fetch();
			}
		}
		if (!$user) return false;
		$hash = $user['sifre'];
		$verified = false;
		if (strpos($hash, '$2y$') === 0 || strpos($hash, '$argon2') === 0) {
			$verified = password_verify($password, $hash);
		} else {
			$verified = hash_equals($hash, $password);
		}
		if ($verified) {
			unset($user['sifre']);
			$_SESSION['user'] = $user;
			return true;
		}
		return false;
	}

	function logout() {
		$_SESSION = [];
		if (ini_get('session.use_cookies')) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
		}
		session_destroy();
	}
?>