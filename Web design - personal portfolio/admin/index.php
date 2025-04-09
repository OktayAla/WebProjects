<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Site içeriğini JSON dosyasından oku
$site_content = json_decode(file_get_contents('../content/site-content.json'), true);

// Mesajları doğru şekilde oku ve grupla
$messagesContent = file_get_contents('../messages.txt');
$messageGroups = explode("\n\n", $messagesContent);
$messages = [];

foreach ($messageGroups as $group) {
    if (trim($group) !== '') {
        $messages[] = trim($group);
    }
}
$messages = array_reverse($messages);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dark-theme">
    <div class="admin-container">
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h3>Admin Paneli</h3>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="#" class="nav-link active" data-section="about">
                        <i class="fas fa-user"></i> Hakkımda
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="projects">
                        <i class="fas fa-project-diagram"></i> Projeler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="skills">
                        <i class="fas fa-chart-bar"></i> Yetenekler
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-section="messages">
                        <i class="fas fa-envelope"></i> Mesajlar
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Çıkış
                    </a>
                </li>
            </ul>
        </nav>
        
        <main class="admin-content">
            <div id="about-section" class="content-section">
                <h2>Hakkımda Düzenle</h2>
                <form id="about-form">
                    <div class="mb-3">
                        <label class="form-label">Hakkımda Metni</label>
                        <textarea class="form-control" name="about_text" rows="6"><?php echo $site_content['about']['text']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </form>
            </div>

            <div id="projects-section" class="content-section" style="display: none;">
                <h2>Projeler Düzenle</h2>
                <div id="projects-list">
                    <?php foreach ($site_content['projects'] as $index => $project): ?>
                    <div class="project-item card mb-3">
                        <div class="card-body">
                            <input type="text" class="form-control mb-2" name="project_title[]" value="<?php echo $project['title']; ?>">
                            <textarea class="form-control mb-2" name="project_description[]" rows="3"><?php echo $project['description']; ?></textarea>
                            <input type="text" class="form-control mb-2" name="project_github[]" value="<?php echo $project['github']; ?>">
                            <button type="button" class="btn btn-danger btn-sm delete-project">Sil</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-success" id="add-project">Yeni Proje Ekle</button>
                <button type="button" class="btn btn-primary" id="save-projects">Kaydet</button>
            </div>

            <div id="skills-section" class="content-section" style="display: none;">
                <h2>Yetenekler Düzenle</h2>
                <div class="row">
                    <div class="col-md-6">
                        <h3>Yazılım Becerileri</h3>
                        <div id="software-skills">
                            <?php foreach ($site_content['skills']['software'] as $skill): ?>
                            <div class="skill-item mb-3">
                                <input type="text" class="form-control mb-2" name="skill_name[]" value="<?php echo $skill['name']; ?>">
                                <input type="number" class="form-control" name="skill_percentage[]" value="<?php echo $skill['percentage']; ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="add-software-skill">Yetenek Ekle</button>
                    </div>
                    <div class="col-md-6">
                        <h3>Sistem Becerileri</h3>
                        <div id="system-skills">
                            <?php foreach ($site_content['skills']['system'] as $skill): ?>
                            <div class="skill-item mb-3">
                                <input type="text" class="form-control mb-2" name="skill_name[]" value="<?php echo $skill['name']; ?>">
                                <input type="number" class="form-control" name="skill_percentage[]" value="<?php echo $skill['percentage']; ?>">
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-success btn-sm" id="add-system-skill">Yetenek Ekle</button>
                    </div>
                </div>
                <button type="button" class="btn btn-primary mt-3" id="save-skills">Kaydet</button>
            </div>

            <div id="messages-section" class="content-section" style="display: none;">
                <h2>Gelen Mesajlar</h2>
                <p class="text-muted mb-3">Tüm mesajlarınız aşağıda listelenmiştir.</p>
                <div class="messages-list">
                    <?php if (count($messages) > 0): ?>
                        <?php foreach ($messages as $index => $message): ?>
                            <div class="message-item card mb-3">
                                <div class="card-body">
                                    <div class="message-header d-flex justify-content-between align-items-center mb-3">
                                        <div class="message-number">
                                            <span class="badge bg-primary">Mesaj #<?php echo count($messages) - $index; ?></span>
                                        </div>
                                        <?php 
                                            // Tarih bilgisini çıkar
                                            preg_match('/Tarih: ([^\n]+)/', $message, $dateMatches);
                                            $date = isset($dateMatches[1]) ? $dateMatches[1] : 'Bilinmiyor';
                                        ?>
                                        <div class="message-date text-muted">
                                            <i class="fas fa-calendar-alt"></i> <?php echo $date; ?>
                                        </div>
                                    </div>
                                    <div class="message-content">
                                        <?php 
                                            // Ad Soyad bilgisini çıkar
                                            preg_match('/Ad Soyad: ([^\n]+)/', $message, $nameMatches);
                                            $name = isset($nameMatches[1]) ? $nameMatches[1] : 'Bilinmiyor';
                                            
                                            // E-posta bilgisini çıkar
                                            preg_match('/E-posta: ([^\n]+)/', $message, $emailMatches);
                                            $email = isset($emailMatches[1]) ? $emailMatches[1] : 'Bilinmiyor';
                                            
                                            // Mesaj içeriğini çıkar
                                            preg_match('/Mesaj: ([\s\S]+)$/', $message, $msgMatches);
                                            $msgContent = isset($msgMatches[1]) ? $msgMatches[1] : 'Mesaj içeriği bulunamadı';
                                        ?>
                                        <div class="sender-info mb-2">
                                            <strong><i class="fas fa-user"></i> <?php echo htmlspecialchars($name); ?></strong>
                                            <span class="text-muted ms-3"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($email); ?></span>
                                        </div>
                                        <div class="message-text p-3 bg-light rounded">
                                            <?php echo nl2br(htmlspecialchars($msgContent)); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Henüz hiç mesaj bulunmamaktadır.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>
