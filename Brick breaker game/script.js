const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

// Tuğla ve oyun alanı hesaplamaları için yardımcı fonksiyon
function calculateLayout() {
    // Tuğlaların toplam genişliğini hesapla
    const totalBricksWidth = brickColumnCount * (brickWidth + brickPadding) - brickPadding;
    // Tuğlaları ekranın ortasına yerleştir
    brickOffsetLeft = Math.max(0, (canvas.width - totalBricksWidth) / 2);
    // Paddle'ı alt kenara yerleştir
    paddleY = canvas.height - paddleHeight - 20; // Alt kenara biraz daha boşluk
    
    // Başlangıç değerlerini ayarla
    if (!isGameStarted || isGameOver) {
        paddleX = (canvas.width - paddleWidth) / 2;
        x = canvas.width / 2;
        y = canvas.height - 40;
        mouseX = paddleX + paddleWidth / 2;
    }
    
    // Tuğlaların ekran dışına taşmasını engelle
    if (brickOffsetLeft < 0) {
        brickOffsetLeft = 10; // Minimum kenar boşluğu
    }
    
    // Tuğlaların konumlarını güncelle
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<brickRowCount; r++){
            bricks[c][r].x = (c*(brickWidth+brickPadding))+brickOffsetLeft;
            bricks[c][r].y = (r*(brickHeight+brickPadding))+brickOffsetTop;
        }
    }
}

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    calculateLayout();
    // Oyun yeniden boyutlandırıldığında paddle ve topu ortala (opsiyonel, oyun durumuna göre ayarlanabilir)
    if (!isGameStarted || isGameOver) {
        paddleX = (canvas.width - paddleWidth) / 2;
        x = canvas.width / 2;
        y = canvas.height - 40;
    }
    // Eğer oyun devam ediyorsa, paddle'ın ekran dışına taşmasını engelle
    if (paddleX + paddleWidth > canvas.width) {
        paddleX = canvas.width - paddleWidth;
    }
    if (paddleX < 0) {
        paddleX = 0;
    }
    // Topun ekran dışına taşmasını engelle (daha kapsamlı kontrol eklenebilir)
    if (x + ballRadius > canvas.width) x = canvas.width - ballRadius;
    if (x - ballRadius < 0) x = ballRadius;
    if (y + ballRadius > canvas.height) y = canvas.height - ballRadius; // Can kaybı zaten var
    if (y - ballRadius < 0) y = ballRadius;

    // Mouse pozisyonunu güncelle (paddle'ın aniden zıplamasını önlemek için)
    mouseX = paddleX + paddleWidth / 2;

    // Yeniden çizim yap (eğer oyun çalışıyorsa)
    if (isGameStarted && !isGameOver) {
         // Mevcut çizim döngüsü zaten devam ediyor, ekstra çizime gerek yok
         // Ancak bazı durumlarda anlık güncellemeler için draw() çağrılabilir.
    } else {
        // Oyun başlamadıysa veya bittiyse, statik elemanları çiz
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawBricks();
        drawPaddle();
        if (!isGameStarted) drawBall(); // Başlangıçta topu göster
        drawScore();
        drawLives();
    }
}

// Oyun değişkenleri
const paddleHeight = 16;
const paddleWidth = 120; // Paddle genişliği artırıldı
let paddleX = 0;
let paddleY = 0;
let rightPressed = false;
let leftPressed = false;
let mouseX = 0;

const ballRadius = 10;
let x = 0;
let y = 0;
let dx = 5; // Başlangıç hızı artırıldı
let dy = -5;

const brickRowCount = 5;
const brickColumnCount = 9;
const brickWidth = 70;
const brickHeight = 24;
const brickPadding = 12;
const brickOffsetTop = 60;
let brickOffsetLeft = 0;

let score = 0;
let lives = 3;
let isGameOver = false;
let isGameStarted = false;
let isPopupShown = false;

const bricks = [];
for(let c=0; c<brickColumnCount; c++){
    bricks[c] = [];
    for(let r=0; r<brickRowCount; r++){
        bricks[c][r] = { x: 0, y: 0, status: 1 };
    }
}

// İlk kurulum
resizeCanvas(); // Canvas boyutunu ayarla ve düzeni hesapla
window.addEventListener('resize', resizeCanvas);

function drawBricks() {
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<brickRowCount; r++){
            if(bricks[c][r].status == 1){
                // Tuğlaların konumlarını calculateLayout'ta hesapladığımız için doğrudan kullanabiliriz
                const brickX = bricks[c][r].x;
                const brickY = bricks[c][r].y;
                ctx.beginPath();
                ctx.rect(brickX, brickY, brickWidth, brickHeight);
                ctx.fillStyle = `hsl(${(r*60+c*20)%360}, 80%, 55%)`;
                ctx.fill();
                ctx.closePath();
            }
        }
    }
}

function drawBall() {
    ctx.beginPath();
    ctx.arc(x, y, ballRadius, 0, Math.PI*2);
    ctx.fillStyle = '#f9d423';
    ctx.shadowColor = '#f9d423';
    ctx.shadowBlur = 10;
    ctx.fill();
    ctx.closePath();
    ctx.shadowBlur = 0;
}

function drawPaddle() {
    ctx.beginPath();
    ctx.roundRect(paddleX, paddleY, paddleWidth, paddleHeight, 8);
    ctx.fillStyle = '#f83600';
    ctx.fill();
    ctx.closePath();
}

function drawScore() {
    document.getElementById('score').textContent = `Skor: ${score}`;
}

function drawLives() {
    document.getElementById('lives').textContent = `Can: ${lives}`;
}

function collisionDetection() {
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<brickRowCount; r++){
            let b = bricks[c][r];
            if(b.status == 1){
                if(x > b.x && x < b.x+brickWidth && y > b.y && y < b.y+brickHeight){
                    dy = -dy;
                    b.status = 0;
                    score++;
                    if(score == brickRowCount*brickColumnCount){
                        setTimeout(()=>{
                            // Kazanma popup'ı
                            const popup = document.createElement('div');
                            popup.className = 'popup';
                            popup.innerHTML = `
                                <div class="popup-content">
                                    <h2>Tebrikler!</h2>
                                    <p>Tüm tuğlaları kırdınız!</p>
                                    <p>Skorunuz: ${score}</p>
                                    <button id="playAgainBtn">Tekrar Oyna</button>
                                </div>
                            `;
                            document.body.appendChild(popup);
                            
                            document.getElementById('playAgainBtn').addEventListener('click', function() {
                                document.body.removeChild(popup);
                                startGame();
                            });
                            document.getElementById('restartBtn').style.display = 'block';
                        }, 100);
                        isGameOver = true;
                    }
                }
            }
        }
    }
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBricks();
    drawBall();
    drawPaddle();
    drawScore();
    drawLives();
    collisionDetection();

    // Topun kenarlara çarpması
    if(x + dx > canvas.width - ballRadius || x + dx < ballRadius){
        dx = -dx * 1.02; // Hız artışı eklendi
    }
    if(y + dy < ballRadius){
        dy = -dy * 1.02; // Hız artışı eklendi
    // Paddle çarpışma kontrolü - geliştirilmiş hassasiyet
    } else if(y + dy > paddleY - ballRadius && y + dy < paddleY + paddleHeight) {
        if(x > paddleX - ballRadius && x < paddleX + paddleWidth + ballRadius){
            // Paddle'a çarpınca açı ver - geliştirilmiş fizik
            let collidePoint = (x - (paddleX + paddleWidth/2)) / (paddleWidth/2);
            let angle = collidePoint * Math.PI/2.5; // Açı aralığı daraltıldı
            let speed = Math.sqrt(dx*dx + dy*dy);
            speed = Math.min(speed * 1.05, 9); // Maksimum hız artırıldı
            dx = speed * Math.sin(angle);
            dy = -speed * Math.cos(angle);
        }
    }
    
    // Topun alt kenara çarpması (can kaybı)
    if(y + dy > canvas.height - ballRadius) {
        lives--;
        if(!lives){
            setTimeout(()=>{
                document.getElementById('restartBtn').style.display = 'block';
                // Oyun bitti popup'ı
                const popup = document.createElement('div');
                popup.className = 'popup';
                popup.innerHTML = `
                    <div class="popup-content">
                        <h2>Oyun Bitti!</h2>
                        <p>Skorunuz: ${score}</p>
                        <button id="playAgainBtn">Tekrar Oyna</button>
                    </div>
                `;
                document.body.appendChild(popup);
                
                document.getElementById('playAgainBtn').addEventListener('click', function() {
                    document.body.removeChild(popup);
                    startGame();
                });
            }, 100);
            isGameOver = true;
        } else {
            x = canvas.width/2;
            // Reset positions after losing a life
            calculateLayout();
            x = canvas.width/2;
            y = canvas.height-40;
            dx = 4 * (Math.random() > 0.5 ? 1 : -1); // Random initial horizontal direction
            dy = -4;
            paddleX = (canvas.width-paddleWidth)/2;
            mouseX = paddleX + paddleWidth / 2;
        }
    }

    x += dx;
    y += dy;

    // Paddle'ı mouse ile daha duyarlı hareket ettir
    paddleX = mouseX - paddleWidth / 2;
    // Paddle'ın sınırlar içinde kalmasını sağla
    const targetX = mouseX - paddleWidth / 2;
    const paddleSpeed = Math.abs(targetX - paddleX) * 0.2; // Yumuşak hareket
    if(Math.abs(targetX - paddleX) > 1) {
        paddleX += (targetX - paddleX) > 0 ? paddleSpeed : -paddleSpeed;
    }
    
    // Paddle sınırları
    if(paddleX < 0) paddleX = 0;
    if(paddleX + paddleWidth > canvas.width) paddleX = canvas.width - paddleWidth;

    if(!isGameOver && isGameStarted){
        requestAnimationFrame(draw);
    }
}

canvas.addEventListener('mousemove', function(e){
    let rect = canvas.getBoundingClientRect();
    mouseX = e.clientX - rect.left;
});

function startGame(){
    if(isGameStarted) return;
    isGameStarted = true;
    isGameOver = false;
    score = 0;
    lives = 3;
    // Reset positions based on current canvas size
    calculateLayout(); // Ensure layout variables are up-to-date
    x = canvas.width/2;
    y = canvas.height-40;
    dx = Math.sign(dx) * 4 || 4; // Keep direction but reset speed
    dy = -4; // Always launch upwards
    paddleX = (canvas.width-paddleWidth)/2;
    mouseX = paddleX + paddleWidth / 2; // Reset mouseX sync
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<brickRowCount; r++){
            bricks[c][r].status = 1;
        }
    }
    document.getElementById('restartBtn').style.display = 'none';
    draw();
}

document.getElementById('restartBtn').addEventListener('click', function(){
    startGame();
});

canvas.addEventListener('click', function(){
    if(!isGameStarted || isGameOver){
        startGame();
    }
});

// Oyun başlangıç popup'ını oluştur
function createStartPopup() {
    if (isPopupShown) return;
    isPopupShown = true;
    
    // Önce varsa eski popup'ları temizle
    const oldPopups = document.querySelectorAll('.popup');
    oldPopups.forEach(popup => {
        try {
            document.body.removeChild(popup);
        } catch (e) {
            console.log('Popup kaldırılırken hata:', e);
        }
    });
    
    const popup = document.createElement('div');
    popup.className = 'popup';
    popup.style.display = 'flex'; // Görünürlüğü garantile
    popup.innerHTML = `
        <div class="popup-content">
            <h2>Tuğla Kırma Oyunu</h2>
            <p>Tuğlaları kırmak için topu paddle ile yönlendirin.</p>
            <p>Tüm tuğlaları kırmaya çalışın ve canlarınızı koruyun!</p>
            <button id="startGameBtn">Oyunu Başlat</button>
        </div>
    `;
    document.body.appendChild(popup);
    
    // Popup'ın DOM'a eklenmesini garantilemek için timeout kullan
    setTimeout(() => {
        const startBtn = document.getElementById('startGameBtn');
        if (startBtn) {
            startBtn.addEventListener('click', function() {
                document.body.removeChild(popup);
                startGame();
            });
        } else {
            console.error('Başlat butonu bulunamadı!');
        }
    }, 50);
}

// Sayfa yüklendiğinde popup'ı göster
window.addEventListener('load', function() {
    // Oyun değişkenlerini başlangıçta ayarla
    calculateLayout(); // Tuğlaların konumlarını hesapla
    
    // Başlangıç ekranını çiz
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBricks();
    drawPaddle();
    drawBall();
    drawScore();
    drawLives();
    
    // Oyun başlangıç popup'ını göster
    setTimeout(function() {
        createStartPopup();
    }, 300); // Sayfanın tam yüklenmesi için kısa bir gecikme
});

document.getElementById('fullscreenBtn').addEventListener('click', function(){
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        document.querySelector('.game-container').requestFullscreen();
    }
});