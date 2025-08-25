<?php
	session_start();
	require_once __DIR__ . '/db.php';

	function users_count() {
		$pdo = get_pdo_connection();
		return (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
	}

	function current_user() {
		return isset($_SESSION['user']) ? $_SESSION['user'] : null;
	}

	function require_login() {
		if (!current_user()) {
			header('Location: login.php');
			exit;
		}
	}

	function login($identifier, $password) {
		$pdo = get_pdo_connection();
		// Check if users.username column exists
		$hasUsername = false;
		try {
			$colStmt = $pdo->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'username' LIMIT 1");
			$hasUsername = (bool)$colStmt->fetchColumn();
		} catch (Exception $e) {
			$hasUsername = false;
		}

		$user = null;
		if ($hasUsername) {
			$stmt = $pdo->prepare('SELECT id, name, email, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
			$stmt->execute([$identifier]);
			$user = $stmt->fetch();
			if (!$user) {
				// Fallback to email if not found by username
				$stmt = $pdo->prepare('SELECT id, name, email, username, password_hash, role FROM users WHERE email = ? LIMIT 1');
				$stmt->execute([$identifier]);
				$user = $stmt->fetch();
				if (!$user) {
					// Fallback to name if not found by email
					$stmt = $pdo->prepare('SELECT id, name, email, username, password_hash, role FROM users WHERE name = ? LIMIT 1');
					$stmt->execute([$identifier]);
					$user = $stmt->fetch();
				}
			}
		} else {
			$stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
			$stmt->execute([$identifier]);
			$user = $stmt->fetch();
			if (!$user) {
				// Fallback to name if not found by email
				$stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE name = ? LIMIT 1');
				$stmt->execute([$identifier]);
				$user = $stmt->fetch();
			}
		}
		if (!$user) return false;
		$hash = $user['password_hash'];
		$verified = false;
		if (strpos($hash, '$2y$') === 0 || strpos($hash, '$argon2') === 0) {
			$verified = password_verify($password, $hash);
		} else {
			// Fallback for plaintext (not recommended) for first-time setups only
			$verified = hash_equals($hash, $password);
		}
		if ($verified) {
			unset($user['password_hash']);
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


