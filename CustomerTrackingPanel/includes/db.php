<?php
	require_once __DIR__ . '/config.php';

	function get_pdo_connection() {
		static $pdo = null;
		if ($pdo !== null) return $pdo;
		$host = DB_HOST;
		$port = null;
		if (strpos($host, ':') !== false) {
			list($host, $port) = explode(':', $host, 2);
		}
		$dsn = 'mysql:host=' . $host . ';dbname=' . DB_NAME . ';charset=utf8mb4';
		if ($port) {
			$dsn .= ';port=' . $port;
		}
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		];
		$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
		return $pdo;
	}
?>


