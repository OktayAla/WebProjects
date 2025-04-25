const GAME_SETTINGS = {
    easy: { ballSpeed: 4, paddleSpeed: 8, brickRows: 3 },
    medium: { ballSpeed: 5, paddleSpeed: 10, brickRows: 4 },
    hard: { ballSpeed: 6, paddleSpeed: 12, brickRows: 5 }
};

const BRICK_TYPES = {
    NORMAL: { color: '#f9d423', points: 10, durability: 1 },
    STRONG: { color: '#ff4e50', points: 20, durability: 2 },
    BONUS: { color: '#00ff00', points: 50, durability: 1 },
    LIFE: { color: '#00ffff', points: 0, durability: 1 }
};

const POWER_UPS = {
    WIDE_PADDLE: { duration: 10000, icon: 'arrows-alt-h' },
    SLOW_MOTION: { duration: 8000, icon: 'clock' },
    EXTRA_BALL: { duration: 0, icon: 'plus-circle' }
};

let gameState = {
    score: 0,
    lives: 3,
    level: 1,
    isGameOver: false,
    isGameStarted: false,
    isPaused: false,
    difficulty: 'medium',
    activePowerUps: new Map(),
    highScores: JSON.parse(localStorage.getItem('highScores')) || []
};

const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
const scoreElement = document.getElementById('score');
const livesElement = document.getElementById('lives');
const levelElement = document.getElementById('level');
const startScreen = document.getElementById('startScreen');
const startButton = document.getElementById('startGameBtn');
const pauseButton = document.getElementById('pauseBtn');
const restartButton = document.getElementById('restartBtn');
const difficultySelect = document.getElementById('difficulty');

let paddle = {
    width: 120,
    height: 16,
    x: 0,
    y: 0,
    speed: 0
};

let ball = {
    x: 0,
    y: 0,
    radius: 10,
    dx: 0,
    dy: 0,
    speed: 0
};

let bricks = [];
let extraBalls = [];

function initGame() {
    resizeCanvas();
    setupEventListeners();
    createStartScreen();
    loadSounds();
}

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    paddle.y = canvas.height - 40;
    resetBall();
}

function setupEventListeners() {
    window.addEventListener('resize', resizeCanvas);
    canvas.addEventListener('mousemove', handleMouseMove);
    canvas.addEventListener('click', handleCanvasClick);
    startButton.addEventListener('click', startGame);
    pauseButton.addEventListener('click', togglePause);
    restartButton.addEventListener('click', restartGame);
    difficultySelect.addEventListener('change', updateDifficulty);
}

function startGame() {
    gameState.isGameStarted = true;
    gameState.isGameOver = false;
    gameState.score = 0;
    gameState.lives = 3;
    gameState.level = 1;
    gameState.activePowerUps.clear();
    extraBalls = [];
    
    startScreen.style.display = 'none';
    restartButton.style.display = 'none';
    
    const settings = GAME_SETTINGS[gameState.difficulty];
    ball.speed = settings.ballSpeed;
    paddle.speed = settings.paddleSpeed;
    
    generateBricks();
    resetBall();
    gameLoop();
}

function gameLoop() {
    if (gameState.isPaused || gameState.isGameOver) return;
    
    update();
    draw();
    requestAnimationFrame(gameLoop);
}

function update() {
    updatePaddle();
    updateBall();
    updatePowerUps();
    checkCollisions();
    checkLevelCompletion();
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBricks();
    drawPaddle();
    drawBall();
    drawExtraBalls();
    drawPowerUps();
    updateUI();
}

function generateBricks() {
    bricks = [];
    const settings = GAME_SETTINGS[gameState.difficulty];
    const brickWidth = 80;
    const brickHeight = 20;
    const padding = 10;
    const offsetTop = 60;
    const offsetLeft = (canvas.width - (11 * (brickWidth + padding))) / 2;
    
    for (let c = 0; c < 11; c++) {
        bricks[c] = [];
        for (let r = 0; r < settings.brickRows; r++) {
            const brickX = c * (brickWidth + padding) + offsetLeft;
            const brickY = r * (brickHeight + padding) + offsetTop;
            
            const random = Math.random();
            let type;
            if (random < 0.6) type = BRICK_TYPES.NORMAL;
            else if (random < 0.8) type = BRICK_TYPES.STRONG;
            else if (random < 0.95) type = BRICK_TYPES.BONUS;
            else type = BRICK_TYPES.LIFE;
            
            bricks[c][r] = {
                x: brickX,
                y: brickY,
                width: brickWidth,
                height: brickHeight,
                type: type,
                durability: type.durability
            };
        }
    }
}

function spawnPowerUp(x, y) {
    const types = Object.keys(POWER_UPS);
    const randomType = types[Math.floor(Math.random() * types.length)];
    
    return {
        x: x,
        y: y,
        type: randomType,
        width: 20,
        height: 20,
        speed: 2
    };
}

function activatePowerUp(type) {
    const powerUp = POWER_UPS[type];
    gameState.activePowerUps.set(type, Date.now() + powerUp.duration);
    
    switch (type) {
        case 'WIDE_PADDLE':
            paddle.width = 180;
            break;
        case 'SLOW_MOTION':
            ball.speed = ball.speed / 2;
            break;
        case 'EXTRA_BALL':
            spawnExtraBall();
            break;
    }
    
    playSound('powerUp');
}

function updateScore(points) {
    gameState.score += points;
    const multiplier = gameState.level * (gameState.difficulty === 'hard' ? 2 : 1);
    gameState.score += points * multiplier;
    
    if (gameState.score > Math.max(...gameState.highScores)) {
        updateHighScores();
    }
}

function updateHighScores() {
    gameState.highScores.push(gameState.score);
    gameState.highScores.sort((a, b) => b - a);
    gameState.highScores = gameState.highScores.slice(0, 5);
    localStorage.setItem('highScores', JSON.stringify(gameState.highScores));
}

function loadSounds() {
    // Sound loading logic here
}

function playSound(soundId) {
    const sound = document.getElementById(soundId);
    if (sound) {
        sound.currentTime = 0;
        sound.play();
    }
}

function resetBall() {
    ball.x = canvas.width / 2;
    ball.y = canvas.height - 40;
    ball.dx = Math.sign(Math.random() - 0.5) * 4;
    ball.dy = -4;
    ball.speed = 4;
}

function updatePaddle() {
    paddle.x = mouseX - paddle.width / 2;
    if (paddle.x < 0) paddle.x = 0;
    if (paddle.x + paddle.width > canvas.width) paddle.x = canvas.width - paddle.width;
}

function updateBall() {
    ball.x += ball.dx * ball.speed;
    ball.y += ball.dy * ball.speed;

    if (ball.x + ball.radius > canvas.width || ball.x - ball.radius < 0) {
        ball.dx = -ball.dx;
    }
    if (ball.y - ball.radius < 0) {
        ball.dy = -ball.dy;
    } else if (ball.y + ball.radius > paddle.y - paddle.height && ball.y + ball.radius < paddle.y + paddle.height) {
        if (ball.x > paddle.x - ball.radius && ball.x < paddle.x + paddle.width + ball.radius) {
            let collidePoint = (ball.x - (paddle.x + paddle.width / 2)) / (paddle.width / 2);
            let angle = collidePoint * Math.PI / 3;
            let speed = Math.sqrt(ball.dx * ball.dx + ball.dy * ball.dy);
            ball.dx = speed * Math.sin(angle);
            ball.dy = -speed * Math.cos(angle);
        }
    }

    if (ball.y + ball.radius > canvas.height) {
        gameState.lives--;
        if (gameState.lives <= 0) {
            setTimeout(() => {
                document.getElementById('restartBtn').style.display = 'block';
                const popup = document.createElement('div');
                popup.className = 'popup';
                popup.innerHTML = `
                    <div class="popup-content">
                        <h2>Oyun Bitti!</h2>
                        <p>Skorunuz: ${gameState.score}</p>
                        <button id="playAgainBtn">Tekrar Oyna</button>
                    </div>
                `;
                document.body.appendChild(popup);
                document.getElementById('playAgainBtn').addEventListener('click', function () {
                    document.body.removeChild(popup);
                    startGame();
                });
            }, 100);
            gameState.isGameOver = true;
        } else {
            resetBall();
        }
    }
}

function updatePowerUps() {
    // Implementation of updatePowerUps function
}

function checkCollisions() {
    for (let c = 0; c < 11; c++) {
        for (let r = 0; r < bricks[c].length; r++) {
            let brick = bricks[c][r];
            if (brick.durability > 0) {
                if (ball.x + ball.radius > brick.x && ball.x - ball.radius < brick.x + brick.width &&
                    ball.y + ball.radius > brick.y && ball.y - ball.radius < brick.y + brick.height) {
                    
                    let overlapLeft = (ball.x + ball.radius) - brick.x;
                    let overlapRight = (brick.x + brick.width) - (ball.x - ball.radius);
                    let overlapTop = (ball.y + ball.radius) - brick.y;
                    let overlapBottom = (brick.y + brick.height) - (ball.y - ball.radius);
                    let minOverlap = Math.min(overlapLeft, overlapRight, overlapTop, overlapBottom);
                    
                    if (minOverlap === overlapLeft || minOverlap === overlapRight) {
                        ball.dx = -ball.dx;
                    } else {
                        ball.dy = -ball.dy;
                    }
                    brick.durability--;
                    updateScore(brick.type.points);
                    if (brick.durability === 0) {
                        bricks[c].splice(r, 1);
                    }
                }
            }
        }
    }
}

function checkLevelCompletion() {
    let allCleared = true;
    for (let c = 0; c < 11; c++) {
        if (bricks[c].length > 0) {
            allCleared = false;
            break;
        }
    }
    if (allCleared) {
        setTimeout(() => { levelUp(); }, 500);
    }
}

function levelUp() {
    gameState.level++;
    generateBricks();
    resetBall();
    draw();
}

function drawBricks() {
    for (let c = 0; c < 11; c++) {
        for (let r = 0; r < bricks[c].length; r++) {
            let brick = bricks[c][r];
            if (brick.durability > 0) {
                ctx.beginPath();
                ctx.rect(brick.x, brick.y, brick.width, brick.height);
                ctx.fillStyle = brick.type === BRICK_TYPES.STRONG ? '#ff4e50' : brick.type === BRICK_TYPES.BONUS ? '#00ff00' : brick.type === BRICK_TYPES.LIFE ? '#00ffff' : '#f9d423';
                ctx.fill();
                ctx.closePath();
            }
        }
    }
}

function drawPaddle() {
    ctx.beginPath();
    ctx.roundRect(paddle.x, paddle.y, paddle.width, paddle.height, 8);
    ctx.fillStyle = '#f83600';
    ctx.fill();
    ctx.closePath();
}

function drawBall() {
    ctx.beginPath();
    ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
    ctx.fillStyle = '#f9d423';
    ctx.shadowColor = '#f9d423';
    ctx.shadowBlur = 10;
    ctx.fill();
    ctx.closePath();
    ctx.shadowBlur = 0;
}

function drawExtraBalls() {
    // Implementation of drawExtraBalls function
}

function drawPowerUps() {
    // Implementation of drawPowerUps function
}

function updateUI() {
    scoreElement.textContent = `Skor: ${gameState.score}`;
    livesElement.textContent = `Can: ${gameState.lives}`;
    levelElement.textContent = `Level: ${gameState.level}`;
}

function handleMouseMove(e) {
    let rect = canvas.getBoundingClientRect();
    mouseX = e.clientX - rect.left;
}

function handleCanvasClick() {
    if (!gameState.isGameStarted || gameState.isGameOver) {
        startGame();
    }
}

function togglePause() {
    gameState.isPaused = !gameState.isPaused;
}

function restartGame() {
    startGame();
}

function updateDifficulty() {
    gameState.difficulty = difficultySelect.value;
    generateBricks();
    resetBall();
}

function createStartScreen() {
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
    initGame();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBricks();
    drawPaddle();
    drawBall();
    drawScore();
    drawLives();
    setTimeout(function() {
        createStartScreen();
    }, 300);
});