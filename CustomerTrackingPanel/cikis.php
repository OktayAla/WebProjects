<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php
	require_once __DIR__ . '/includes/auth.php';
	logout();
	header('Location: giris.php');
	exit;
?>