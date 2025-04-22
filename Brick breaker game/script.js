const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

function calculateLayout() {
    const totalBricksWidth = brickColumnCount * (brickWidth + brickPadding) - brickPadding;
    brickOffsetLeft = Math.max(0, (canvas.width - totalBricksWidth) / 2);
    paddleY = canvas.height - paddleHeight - 20;
    if (!isGameStarted || isGameOver) {
        paddleX = (canvas.width - paddleWidth) / 2;
        x = canvas.width / 2;
        y = canvas.height - 40;
        mouseX = paddleX + paddleWidth / 2;
    }
    if (brickOffsetLeft < 0) {
        brickOffsetLeft = 10;
    }
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<brickRowCount; r++){
            bricks[c][r].x = (c*(brickWidth+brickPadding)) + brickOffsetLeft;
            bricks[c][r].y = (r*(brickHeight+brickPadding)) + brickOffsetTop;
        }
    }
}
function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    calculateLayout();
    if (!isGameStarted || isGameOver) {
        paddleX = (canvas.width - paddleWidth) / 2;
        x = canvas.width / 2;
        y = canvas.height - 40;
    }
    if (paddleX + paddleWidth > canvas.width) {
        paddleX = canvas.width - paddleWidth;
    }
    if (paddleX < 0) {
        paddleX = 0;
    }
    if (x + ballRadius > canvas.width) x = canvas.width - ballRadius;
    if (x - ballRadius < 0) x = ballRadius;
    if (y + ballRadius > canvas.height) y = canvas.height - ballRadius;
    if (y - ballRadius < 0) y = ballRadius;
    mouseX = paddleX + paddleWidth / 2;
    if (isGameStarted && !isGameOver) {
    } else {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawBricks();
        drawPaddle();
        if (!isGameStarted) drawBall();
        drawScore();
        drawLives();
    }
}
const paddleHeight = 16;
const paddleWidth = 120;
let paddleX = 0;
let paddleY = 0;
let rightPressed = false;
let leftPressed = false;
let mouseX = 0;
const ballRadius = 10;
let x = 0;
let y = 0;
let dx = 5;
let dy = -5;
const brickRowCount = 6;
const brickColumnCount = 11;
const brickWidth = 65;
const brickHeight = 20;
const brickDurability = [1,1,2,1,3,2];
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
        bricks[c][r] = { x: 0, y: 0, status: brickDurability[r] || 1 };
    }
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);
function drawBricks() {
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<brickRowCount; r++){
            if(bricks[c][r].status == 1){
                const brickX = bricks[c][r].x;
                const brickY = bricks[c][r].y;
                ctx.beginPath();
                ctx.rect(brickX, brickY, brickWidth, brickHeight);
                ctx.fillStyle = bricks[c][r].status === 3 ? '#FFD700' : bricks[c][r].status === 2 ? '#CD5C5C' : `hsl(${(r*60+c*20)%360}, 80%, 55%)`;
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
    if(x + dx > canvas.width - ballRadius || x + dx < ballRadius){
        dx = -dx * 1.02;
    }
    if(y + dy < ballRadius){
        dy = -dy * 1.02;
    } else if(y + dy > paddleY - ballRadius && y + dy < paddleY + paddleHeight) {
        if(x > paddleX - ballRadius && x < paddleX + paddleWidth + ballRadius){
            let collidePoint = (x - (paddleX + paddleWidth/2)) / (paddleWidth/2);
            let angle = collidePoint * Math.PI/3;
            let speed = Math.sqrt(dx*dx + dy*dy);
            speed = Math.min(speed * 1.02, 8);
            dx = speed * Math.sin(angle);
            dy = -speed * Math.cos(angle);
        }
    }
    if(y + dy > canvas.height - ballRadius) {
        lives--;
        if(!lives){
            setTimeout(()=>{
                document.getElementById('restartBtn').style.display = 'block';
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
            calculateLayout();
            x = canvas.width/2;
            y = canvas.height-40;
            dx = 3 * (Math.random() > 0.5 ? 1 : -1);
            dy = -3;
            paddleX = (canvas.width-paddleWidth)/2;
            mouseX = paddleX + paddleWidth / 2;
        }
    }
    x += dx;
    y += dy;
    paddleX = mouseX - paddleWidth / 2;
    const targetX = mouseX - paddleWidth / 2;
    const paddleSpeed = Math.abs(targetX - paddleX) * 0.3;
    if(Math.abs(targetX - paddleX) > 1) {
        paddleX += (targetX - paddleX) > 0 ? paddleSpeed : -paddleSpeed;
    }
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
canvas.addEventListener('click', function(){
    if(!isGameStarted || isGameOver){
        startGame();
    }
});
function startGame(){
    if(isGameStarted) return;
    isGameStarted = true;
    isGameOver = false;
    score = 0;
    lives = 3;
    calculateLayout();
    x = canvas.width/2;
    y = canvas.height-40;
    dx = Math.sign(dx) * 4 || 4;
    dy = -4;
    paddleX = (canvas.width-paddleWidth)/2;
    mouseX = paddleX + paddleWidth / 2;
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
const fullscreenBtn = document.getElementById('fullscreenBtn');
fullscreenBtn.addEventListener('click', function(){
    const gameContainer = document.querySelector('.game-container');
    if (!document.fullscreenElement) {
        if(gameContainer.requestFullscreen) {
            gameContainer.requestFullscreen();
        } else if(gameContainer.mozRequestFullScreen) {
            gameContainer.mozRequestFullScreen();
        } else if(gameContainer.webkitRequestFullscreen) {
            gameContainer.webkitRequestFullscreen();
        } else if(gameContainer.msRequestFullscreen) {
            gameContainer.msRequestFullscreen();
        }
    } else {
        if(document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
});
function createStartPopup() {
    if (isPopupShown) return;
    isPopupShown = true;
    const oldPopups = document.querySelectorAll('.popup');
    oldPopups.forEach(popup => {
        try {
            document.body.removeChild(popup);
        } catch (e) {
            console.log(e);
        }
    });
    const popup = document.createElement('div');
    popup.className = 'popup';
    popup.style.display = 'flex';
    popup.innerHTML = `
        <div class="popup-content">
            <h2>Tuğla Kırma Oyunu</h2>
            <p>Tuğlaları kırmak için topu paddle ile yönlendirin.</p>
            <p>Tüm tuğlaları kırmaya çalışın ve canlarınızı koruyun!</p>
            <button id="startGameBtn">Oyunu Başlat</button>
        </div>
    `;
    document.body.appendChild(popup);
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
window.addEventListener('load', function() {
    calculateLayout();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBricks();
    drawPaddle();
    drawBall();
    drawScore();
    drawLives();
    setTimeout(function() {
        createStartPopup();
    }, 300);
});