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
        for(let r=0; r<bricks[c].length; r++){
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
const brickColumnCount = 11;
const brickWidth = 65;
const brickHeight = 20;
const brickPadding = 12;
const brickOffsetTop = 60;
let brickOffsetLeft = 0;
let score = 0;
let lives = 5;
let level = 1;
let isGameOver = false;
let isGameStarted = false;
let isPopupShown = false;
const baseBrickRowCount = 6;

function generateBricks() {
    let rows = baseBrickRowCount + level - 1;
    const bricksArray = [];
    for (let c = 0; c < brickColumnCount; c++) {
        bricksArray[c] = [];
        for (let r = 0; r < rows; r++) {
            let durability = Math.floor(Math.random() * level) + 1;
            let surprise = false;
            if(level > 1 && Math.random() < 0.15) {
                surprise = true;
            }
            bricksArray[c][r] = { x: 0, y: 0, status: durability, surprise: surprise };
        }
    }
    return bricksArray;
}
let bricks = generateBricks();

function levelUp() {
    level++;
    bricks = generateBricks();
    calculateLayout();
    x = canvas.width / 2;
    y = canvas.height - 40;
    dx = Math.sign(dx) * 4 || 4;
    dy = -4;
    paddleX = (canvas.width - paddleWidth) / 2;
    mouseX = paddleX + paddleWidth / 2;
    draw();
}

function checkLevelCompletion() {
    let allCleared = true;
    for(let c = 0; c < brickColumnCount; c++){
        for(let r = 0; r < bricks[c].length; r++){
            if(bricks[c][r].status > 0){
                allCleared = false;
                break;
            }
        }
        if(!allCleared) break;
    }
    if(allCleared){
        setTimeout(() => { levelUp(); }, 500);
    }
}

resizeCanvas();
window.addEventListener('resize', resizeCanvas);
function drawBricks() {
    for(let c=0; c<brickColumnCount; c++){
        for(let r=0; r<bricks[c].length; r++){
            if(bricks[c][r].status > 0){
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
    for(let c = 0; c < brickColumnCount; c++){
        for(let r = 0; r < bricks[c].length; r++){
            let b = bricks[c][r];
            if(b.status > 0){
                if(x + ballRadius > b.x && x - ballRadius < b.x + brickWidth &&
                   y + ballRadius > b.y && y - ballRadius < b.y + brickHeight){
                    
                    let overlapLeft = (x + ballRadius) - b.x;
                    let overlapRight = (b.x + brickWidth) - (x - ballRadius);
                    let overlapTop = (y + ballRadius) - b.y;
                    let overlapBottom = (b.y + brickHeight) - (y - ballRadius);
                    let minOverlap = Math.min(overlapLeft, overlapRight, overlapTop, overlapBottom);
                    
                    if(minOverlap === overlapLeft || minOverlap === overlapRight){
                        dx = -dx;
                    } else {
                        dy = -dy;
                    }
                    if(b.surprise) {
                        lives++;
                        b.surprise = false;
                    }
                    b.status = 0;
                    score++;
                    checkLevelCompletion();
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
        dx = -dx;
    }
    if(y + dy < ballRadius){
        dy = -dy;
    } else if(y + dy > paddleY - ballRadius && y + dy < paddleY + paddleHeight) {
        if(x > paddleX - ballRadius && x < paddleX + paddleWidth + ballRadius){
            let collidePoint = (x - (paddleX + paddleWidth/2)) / (paddleWidth/2);
            let angle = collidePoint * Math.PI/3;
            let speed = Math.sqrt(dx*dx + dy*dy);
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
                document.getElementById('playAgainBtn').addEventListener('click', function(){
                    document.body.removeChild(popup);
                    startGame();
                });
            }, 100);
            isGameOver = true;
        } else {
            x = canvas.width / 2;
            calculateLayout();
            x = canvas.width / 2;
            y = canvas.height - 40;
            dx = 3 * (Math.random() > 0.5 ? 1 : -1);
            dy = -3;
            paddleX = (canvas.width - paddleWidth) / 2;
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
    lives = 5;
    level = 1;
    bricks = generateBricks();
    calculateLayout();
    x = canvas.width/2;
    y = canvas.height-40;
    dx = Math.sign(dx) * 4 || 4;
    dy = -4;
    paddleX = (canvas.width-paddleWidth)/2;
    mouseX = paddleX + paddleWidth / 2;
    document.getElementById('restartBtn').style.display = 'none';
    draw();
}
document.getElementById('restartBtn').addEventListener('click', function(){
    startGame();
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