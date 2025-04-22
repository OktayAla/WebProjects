const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

// Oyun değişkenleri
const paddleHeight = 16;
const paddleWidth = 90;
let paddleX = (canvas.width - paddleWidth) / 2;
const paddleY = canvas.height - paddleHeight - 10;
let rightPressed = false;
let leftPressed = false;
let mouseX = paddleX;

const ballRadius = 10;
let x = canvas.width / 2;
let y = canvas.height - 40;
let dx = 3;
let dy = -3;

const brickRowCount = 5;
const brickColumnCount = 7;
const brickWidth = 60;
const brickHeight = 22;
const brickPadding = 10;
const brickOffsetTop = 50;
const brickOffsetLeft = 18;

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

function drawBricks() {
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<brickRowCount; r++){
            if(bricks[c][r].status == 1){
                let brickX = (c*(brickWidth+brickPadding))+brickOffsetLeft;
                let brickY = (r*(brickHeight+brickPadding))+brickOffsetTop;
                bricks[c][r].x = brickX;
                bricks[c][r].y = brickY;
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
        dx = -dx;
    }
    if(y + dy < ballRadius){
        dy = -dy;
    } else if(y + dy > paddleY - ballRadius && y < paddleY + paddleHeight/2){
        if(x > paddleX && x < paddleX + paddleWidth){
            // Paddle'a çarpınca açı ver
            let collidePoint = x - (paddleX + paddleWidth/2);
            collidePoint = collidePoint / (paddleWidth/2);
            let angle = collidePoint * Math.PI/3;
            // Hız sınırlaması ekle
            let speed = Math.sqrt(dx*dx + dy*dy);
            speed = Math.min(speed, 7); // Maksimum hız
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
            y = canvas.height-40;
            dx = 3;
            dy = -3;
            paddleX = (canvas.width-paddleWidth)/2;
        }
    }

    x += dx;
    y += dy;

    // Paddle'ı mouse ile hareket ettir
    paddleX += (mouseX - paddleX - paddleWidth/2) * 0.2;
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
    x = canvas.width/2;
    y = canvas.height-40;
    dx = 3;
    dy = -3;
    paddleX = (canvas.width-paddleWidth)/2;
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
    
    const popup = document.createElement('div');
    popup.className = 'popup';
    popup.innerHTML = `
        <div class="popup-content">
            <h2>Tuğla Kırma Oyunu</h2>
            <p>Tuğlaları kırmak için topu paddle ile yönlendirin.</p>
            <p>Tüm tuğlaları kırmaya çalışın ve canlarınızı koruyun!</p>
            <button id="startGameBtn">Oyunu Başlat</button>
        </div>
    `;
    document.body.appendChild(popup);
    
    document.getElementById('startGameBtn').addEventListener('click', function() {
        document.body.removeChild(popup);
        startGame();
    });
}

// Sayfa yüklendiğinde popup'ı göster
window.addEventListener('load', function() {
    drawScore();
    drawLives();
    createStartPopup();
});

document.getElementById('fullscreenBtn').addEventListener('click', function(){
    if (document.fullscreenElement) {
        document.exitFullscreen();
    } else {
        document.querySelector('.game-container').requestFullscreen();
    }
});