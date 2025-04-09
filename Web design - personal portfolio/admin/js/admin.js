// Admin Panel JavaScript
// Bu JavaScript dosyası, admin panelinin işlevselliğini kontrol eder.

// Sayfa yüklendiğinde tüm baslatma fonksiyonlarını çagir
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nav-link[data-section]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sectionId = this.dataset.section + '-section';
            
            // Tüm içerik bölümlerini gizle ve sadece tıklanan bölümü göster
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Gösterilecek bölümü göster
            document.getElementById(sectionId).style.display = 'block';
            
            // Aktif bağlantıyı ayarla
            document.querySelectorAll('.nav-link').forEach(navLink => {
                navLink.classList.remove('active');
            });
            this.classList.add('active');
        });
    });

    // Default olarak ilk bölümü göster
    const aboutForm = document.getElementById('about-form');
    if (aboutForm) {
        aboutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            try {
                const response = await fetch('save-content.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    alert('Değişiklikler kaydedildi!');
                } else {
                    throw new Error('Bir hata oluştu');
                }
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        });
    }
    // Proje ekleme
    const addProjectBtn = document.getElementById('add-project');
    if (addProjectBtn) {
        addProjectBtn.addEventListener('click', function() {
            const projectTemplate = `
                <div class="project-item card mb-3">
                    <div class="card-body">
                        <input type="text" class="form-control mb-2" name="project_title[]" placeholder="Proje Başlığı">
                        <textarea class="form-control mb-2" name="project_description[]" rows="3" placeholder="Proje Açıklaması"></textarea>
                        <input type="text" class="form-control mb-2" name="project_github[]" placeholder="Github Linki">
                        <button type="button" class="btn btn-danger btn-sm delete-project">Sil</button>
                    </div>
                </div>
            `;
            document.getElementById('projects-list').insertAdjacentHTML('beforeend', projectTemplate);
        });
    }

    // Proje silme
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-project')) {
            if (confirm('Bu projeyi silmek istediğinize emin misiniz?')) {
                e.target.closest('.project-item').remove();
            }
        }
    });

    // Proje kaydetme
    const saveProjectsBtn = document.getElementById('save-projects');
    if (saveProjectsBtn) {
        saveProjectsBtn.addEventListener('click', async function() {
            const projects = [];
            document.querySelectorAll('.project-item').forEach(item => {
                projects.push({
                    title: item.querySelector('[name="project_title[]"]').value,
                    description: item.querySelector('[name="project_description[]"]').value,
                    github: item.querySelector('[name="project_github[]"]').value
                });
            });

            try {
                const response = await fetch('save-content.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ type: 'projects', data: projects })
                });

                if (response.ok) {
                    alert('Projeler kaydedildi!');
                } else {
                    throw new Error('Bir hata oluştu');
                }
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        });
    }

    // Skill ekleme
    function addSkillItem(containerId) {
        const skillTemplate = `
            <div class="skill-item mb-3">
                <input type="text" class="form-control mb-2" name="skill_name[]" placeholder="Yetenek Adı">
                <input type="number" class="form-control" name="skill_percentage[]" placeholder="Yüzde" min="0" max="100">
                <button type="button" class="btn btn-danger btn-sm mt-2 delete-skill">Sil</button>
            </div>
        `;
        document.getElementById(containerId).insertAdjacentHTML('beforeend', skillTemplate);
    }

    document.getElementById('add-software-skill')?.addEventListener('click', () => addSkillItem('software-skills'));
    document.getElementById('add-system-skill')?.addEventListener('click', () => addSkillItem('system-skills'));

    // Skill silme
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-skill')) {
            if (confirm('Bu yeteneği silmek istediğinize emin misiniz?')) {
                e.target.closest('.skill-item').remove();
            }
        }
    });
    
    // Yetenekler kaydetme
    const saveSkillsBtn = document.getElementById('save-skills');
    if (saveSkillsBtn) {
        saveSkillsBtn.addEventListener('click', async function() {
            const skills = {
                software: [],
                system: []
            };
            
            // Yazılım becerilerini topla
            document.querySelectorAll('#software-skills .skill-item').forEach(item => {
                skills.software.push({
                    name: item.querySelector('[name="skill_name[]"]').value,
                    percentage: parseInt(item.querySelector('[name="skill_percentage[]"]').value)
                });
            });
            
            // Sistem becerilerini topla
            document.querySelectorAll('#system-skills .skill-item').forEach(item => {
                skills.system.push({
                    name: item.querySelector('[name="skill_name[]"]').value,
                    percentage: parseInt(item.querySelector('[name="skill_percentage[]"]').value)
                });
            });

            try {
                const response = await fetch('save-content.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ type: 'skills', data: skills })
                });

                if (response.ok) {
                    alert('Yetenekler kaydedildi!');
                } else {
                    throw new Error('Bir hata oluştu');
                }
            } catch (error) {
                alert('Hata: ' + error.message);
            }
        });
    }
});
