const GAME_SETTINGS = {
    easy: { ballSpeed: 4, paddleSpeed: 8, brickRows: 3 },
    medium: { ballSpeed: 5, paddleSpeed: 10, brickRows: 4 },
    hard: { ballSpeed: 6, paddleSpeed: 12, brickRows: 5 }
};

const BRICK_TYPES = {
    NORMAL: { color: '#f9d423', points: 10, durability: 1 },
    STRONG: { color: '#ff4e50', points: 20, durability: 2 },
    BONUS: { color: '#00ff00', points: 50, durability: 1, hasPowerUp: false },
    LIFE: { color: '#00ffff', points: 0, durability: 1 }
};

const POWER_UPS = {
    WIDE_PADDLE: { duration: 10000, icon: 'arrows-alt-h' },
    SLOW_MOTION: { duration: 8000, icon: 'clock' },
    EXTRA_BALL: { duration: 0, icon: 'plus-circle' }
};

let gameState = {
    score: 0,
    lastScore: 0, // Son skoru takip etmek için
    lives: 3,
    level: 1,
    isGameOver: false,
    isGameStarted: false,
    isPaused: false,
    difficulty: 'medium',
    activePowerUps: new Map(),
    highScores: JSON.parse(localStorage.getItem('highScores')) || [],
    sounds: {},
    particles: [] // Parçacık efektleri için
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
let mouseX = 0;

function initGame() {
    resizeCanvas();
    setupEventListeners();
    setupPowerUps();
    createStartScreen();
    loadSounds();
    drawInitialScreen();
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
    updateExtraBalls();
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
    
    // Yeşil tuğlalara güç artırımı eklemek için sayaç
    let powerUpCount = 0;
    const maxPowerUps = 5;
    
    for (let c = 0; c < 11; c++) {
        bricks[c] = [];
        for (let r = 0; r < settings.brickRows; r++) {
            const brickX = c * (brickWidth + padding) + offsetLeft;
            const brickY = r * (brickHeight + padding) + offsetTop;
            
            const random = Math.random();
            let type;
            if (random < 0.6) type = BRICK_TYPES.NORMAL;
            else if (random < 0.8) type = BRICK_TYPES.STRONG;
            else if (random < 0.95) {
                type = BRICK_TYPES.BONUS;
                // Yeşil tuğlalara rastgele güç artırımı ekle (en fazla 5 tane)
                if (powerUpCount < maxPowerUps && Math.random() < 0.5) {
                    type = {...BRICK_TYPES.BONUS, hasPowerUp: true};
                    powerUpCount++;
                }
            }
            else type = BRICK_TYPES.LIFE;
            
            bricks[c][r] = {
                x: brickX,
                y: brickY,
                width: brickWidth,
                height: brickHeight,
                type: type,
                durability: type.durability,
                hasPowerUp: type.hasPowerUp || false
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

function setupPowerUps() {
    // Artık butonlar olmadığı için bu fonksiyon sadece oyun başlangıcında çağrılacak
    // Güç artırımları yeşil tuğlalarda olacak
}
}

function showPowerUpNotification(powerUpType) {
    // Güç artırımı bildirimini göster
    const notification = document.createElement('div');
    notification.className = 'power-up-notification';
    
    let message = '';
    let icon = '';
    
    switch(powerUpType) {
        case 'WIDE_PADDLE':
            message = 'Geniş Raket!';
            icon = 'arrows-alt-h';
            break;
        case 'SLOW_MOTION':
            message = 'Yavaş Hareket!';
            icon = 'clock';
            break;
        case 'EXTRA_BALL':
            message = 'Ekstra Top!';
            icon = 'plus-circle';
            break;
    }
    
    notification.innerHTML = `<i class="fas fa-${icon}"></i> ${message}`;
    document.body.appendChild(notification);
    
    // Animasyon sonrası bildirimi kaldır
    setTimeout(() => {
        notification.classList.add('fade-out');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 500);
    }, 2000);
}

function activatePowerUp(type) {
    const powerUp = POWER_UPS[type];
    gameState.activePowerUps.set(type, Date.now() + powerUp.duration);
    
    switch (type) {
        case 'WIDE_PADDLE':
            paddle.width = 180;
            setTimeout(() => {
                paddle.width = 120;
            }, powerUp.duration);
            break;
        case 'SLOW_MOTION':
            ball.speed = ball.speed / 2;
            setTimeout(() => {
                ball.speed = ball.speed * 2;
            }, powerUp.duration);
            break;
        case 'EXTRA_BALL':
            spawnExtraBall();
            break;
    }
    
    playSound('powerUp');
}

function spawnExtraBall() {
    const extraBall = {
        x: ball.x,
        y: ball.y,
        radius: ball.radius,
        dx: -ball.dx,
        dy: ball.dy,
        speed: ball.speed
    };
    extraBalls.push(extraBall);
    
    setTimeout(() => {
        const index = extraBalls.indexOf(extraBall);
        if (index > -1) {
            extraBalls.splice(index, 1);
        }
    }, 10000); // Extra ball lasts for 10 seconds
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
    // Ses dosyalarını oluştur
    const sounds = {
        brickHit: new Audio(),
        paddleHit: new Audio(),
        powerUp: new Audio(),
        levelComplete: new Audio(),
        gameOver: new Audio()
    };

    // Ses dosyalarını gameState'e kaydet
    gameState.sounds = sounds;
    
    // Ses dosyaları için URL'ler (gelecekte eklenecek)
    // sounds.brickHit.src = 'sounds/brick-hit.mp3';
    // sounds.paddleHit.src = 'sounds/paddle-hit.mp3';
    // sounds.powerUp.src = 'sounds/power-up.mp3';
    // sounds.levelComplete.src = 'sounds/level-complete.mp3';
    // sounds.gameOver.src = 'sounds/game-over.mp3';
}

function playSound(soundId) {
    // For now, just log the sound that would be played
    console.log(`Playing sound: ${soundId}`);
    // In the future, when we have actual sound files:
    // const sound = gameState.sounds[soundId];
    // if (sound) {
    //     sound.currentTime = 0;
    //     sound.play();
    // }
}

function resetBall() {
    ball.x = canvas.width / 2;
    ball.y = canvas.height - 40;
    ball.dx = Math.sign(Math.random() - 0.5) * ball.speed;
    ball.dy = -ball.speed;
}

function updatePaddle() {
    paddle.x = mouseX - paddle.width / 2;
    if (paddle.x < 0) paddle.x = 0;
    if (paddle.x + paddle.width > canvas.width) paddle.x = canvas.width - paddle.width;
}

function updateBall() {
    ball.x += ball.dx;
    ball.y += ball.dy;

    if (ball.x + ball.radius > canvas.width || ball.x - ball.radius < 0) {
        ball.dx = -ball.dx;
    }
    if (ball.y - ball.radius < 0) {
        ball.dy = -ball.dy;
    } else if (ball.y + ball.radius > paddle.y && ball.y + ball.radius < paddle.y + paddle.height) {
        if (ball.x > paddle.x - ball.radius && ball.x < paddle.x + paddle.width + ball.radius) {
            let collidePoint = (ball.x - (paddle.x + paddle.width / 2)) / (paddle.width / 2);
            let angle = collidePoint * Math.PI / 3;
            let speed = Math.sqrt(ball.dx * ball.dx + ball.dy * ball.dy);
            ball.dx = speed * Math.sin(angle);
            ball.dy = -speed * Math.cos(angle);
            playSound('paddleHit');
        }
    }

    if (ball.y + ball.radius > canvas.height) {
        gameState.lives--;
        if (gameState.lives <= 0) {
            gameOver();
        } else {
            resetBall();
        }
    }
}

function updateExtraBalls() {
    extraBalls.forEach(extraBall => {
        extraBall.x += extraBall.dx;
        extraBall.y += extraBall.dy;

        // Wall collision
        if (extraBall.x + extraBall.radius > canvas.width || extraBall.x - extraBall.radius < 0) {
            extraBall.dx = -extraBall.dx;
        }
        if (extraBall.y - extraBall.radius < 0) {
            extraBall.dy = -extraBall.dy;
        }

        // Paddle collision
        if (extraBall.y + extraBall.radius > paddle.y && extraBall.y + extraBall.radius < paddle.y + paddle.height) {
            if (extraBall.x > paddle.x - extraBall.radius && extraBall.x < paddle.x + paddle.width + extraBall.radius) {
                let collidePoint = (extraBall.x - (paddle.x + paddle.width / 2)) / (paddle.width / 2);
                let angle = collidePoint * Math.PI / 3;
                let speed = Math.sqrt(extraBall.dx * extraBall.dx + extraBall.dy * extraBall.dy);
                extraBall.dx = speed * Math.sin(angle);
                extraBall.dy = -speed * Math.cos(angle);
                playSound('paddleHit');
            }
        }

        // Bottom collision
        if (extraBall.y + extraBall.radius > canvas.height) {
            const index = extraBalls.indexOf(extraBall);
            if (index > -1) {
                extraBalls.splice(index, 1);
            }
        }

        // Brick collision
        for (let c = 0; c < 11; c++) {
            for (let r = 0; r < bricks[c].length; r++) {
                let brick = bricks[c][r];
                if (brick.durability > 0) {
                    if (extraBall.x + extraBall.radius > brick.x && extraBall.x - extraBall.radius < brick.x + brick.width &&
                        extraBall.y + extraBall.radius > brick.y && extraBall.y - extraBall.radius < brick.y + brick.height) {
                        
                        let overlapLeft = (extraBall.x + extraBall.radius) - brick.x;
                        let overlapRight = (brick.x + brick.width) - (extraBall.x - extraBall.radius);
                        let overlapTop = (extraBall.y + extraBall.radius) - brick.y;
                        let overlapBottom = (brick.y + brick.height) - (extraBall.y - extraBall.radius);
                        let minOverlap = Math.min(overlapLeft, overlapRight, overlapTop, overlapBottom);
                        
                        if (minOverlap === overlapLeft || minOverlap === overlapRight) {
                            extraBall.dx = -extraBall.dx;
                        } else {
                            extraBall.dy = -extraBall.dy;
                        }
                        
                        brick.durability--;
                        updateScore(brick.type.points);
                        playSound('brickHit');
                        
                        // Eğer tuğlada güç artırımı varsa, aktifleştir
                        if (brick.hasPowerUp) {
                            const types = Object.keys(POWER_UPS);
                            const randomType = types[Math.floor(Math.random() * types.length)];
                            activatePowerUp(randomType);
                            showPowerUpNotification(randomType);
                        }
                        
                        if (brick.durability === 0) {
                            if (brick.type === BRICK_TYPES.LIFE) {
                                gameState.lives++;
                            }
                            bricks[c].splice(r, 1);
                        }
                    }
                }
            }
        }
    });
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
                    playSound('brickHit');
                    
                    // Eğer tuğlada güç artırımı varsa, aktifleştir
                    if (brick.hasPowerUp) {
                        const types = Object.keys(POWER_UPS);
                        const randomType = types[Math.floor(Math.random() * types.length)];
                        activatePowerUp(randomType);
                        showPowerUpNotification(randomType);
                    }
                    
                    if (brick.durability === 0) {
                        if (brick.type === BRICK_TYPES.LIFE) {
                            gameState.lives++;
                        }
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
    playSound('levelComplete');
    generateBricks();
    resetBall();
}

function drawBricks() {
    for (let c = 0; c < 11; c++) {
        for (let r = 0; r < bricks[c].length; r++) {
            let brick = bricks[c][r];
            if (brick.durability > 0) {
                ctx.beginPath();
                ctx.roundRect(brick.x, brick.y, brick.width, brick.height, 4);
                
                // Tuğla tipine göre farklı görsel efektler
                if (brick.type === BRICK_TYPES.STRONG && brick.durability === 2) {
                    // Güçlü tuğla için gradient efekti
                    const gradient = ctx.createLinearGradient(brick.x, brick.y, brick.x, brick.y + brick.height);
                    gradient.addColorStop(0, '#ff4e50');
                    gradient.addColorStop(1, '#ff0000');
                    ctx.fillStyle = gradient;
                } else if (brick.type === BRICK_TYPES.BONUS) {
                    // Bonus tuğla için parlama efekti
                    ctx.fillStyle = brick.type.color;
                    ctx.shadowColor = brick.type.color;
                    ctx.shadowBlur = 10;
                } else if (brick.type === BRICK_TYPES.LIFE) {
                    // Can tuğlası için nabız efekti
                    const pulseIntensity = Math.sin(Date.now() / 200) * 0.2 + 0.8;
                    ctx.fillStyle = brick.type.color;
                    ctx.globalAlpha = pulseIntensity;
                } else {
                    ctx.fillStyle = brick.type.color;
                }
                
                ctx.fill();
                
                // Tuğla kenarları için ince çizgi
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.3)';
                ctx.lineWidth = 1;
                ctx.stroke();
                
                ctx.closePath();
                ctx.shadowBlur = 0;
                ctx.globalAlpha = 1;
            }
        }
    }
}

function drawPaddle() {
    // Raket için gradient ve gölge efektleri ekle
    ctx.beginPath();
    ctx.roundRect(paddle.x, paddle.y, paddle.width, paddle.height, 8);
    
    // Gradient renk geçişi
    const gradient = ctx.createLinearGradient(paddle.x, paddle.y, paddle.x, paddle.y + paddle.height);
    gradient.addColorStop(0, '#ff4e50');
    gradient.addColorStop(1, '#f83600');
    
    ctx.fillStyle = gradient;
    ctx.shadowColor = '#f83600';
    ctx.shadowBlur = 10;
    ctx.shadowOffsetY = 5;
    ctx.fill();
    
    // Raket üzerinde parlaklık efekti
    ctx.beginPath();
    ctx.roundRect(paddle.x, paddle.y, paddle.width, paddle.height/2, 8);
    ctx.fillStyle = 'rgba(255, 255, 255, 0.2)';
    ctx.fill();
    ctx.closePath();
    ctx.shadowBlur = 0;
    ctx.shadowOffsetY = 0;
}

function drawBall() {
    // Top çizimi için gölge ve parlaklık efektleri ekle
    ctx.beginPath();
    ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
    ctx.fillStyle = '#f9d423';
    ctx.shadowColor = '#f9d423';
    ctx.shadowBlur = 15;
    ctx.fill();
    
    // Top üzerinde parlaklık efekti
    const gradient = ctx.createRadialGradient(
        ball.x - ball.radius/3, ball.y - ball.radius/3, 1,
        ball.x, ball.y, ball.radius
    );
    gradient.addColorStop(0, 'rgba(255, 255, 255, 0.8)');
    gradient.addColorStop(0.3, 'rgba(255, 255, 255, 0.2)');
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
    
    ctx.beginPath();
    ctx.arc(ball.x, ball.y, ball.radius, 0, Math.PI * 2);
    ctx.fillStyle = gradient;
    ctx.fill();
    ctx.closePath();
    ctx.shadowBlur = 0;
}

function drawExtraBalls() {
    extraBalls.forEach(extraBall => {
        ctx.beginPath();
        ctx.arc(extraBall.x, extraBall.y, extraBall.radius, 0, Math.PI * 2);
        ctx.fillStyle = '#f9d423';
        ctx.fill();
        ctx.closePath();
    });
}

function drawPowerUps() {
    // Implementation of drawPowerUps function
}

function updateUI() {
    // Skor, can ve level bilgilerini güncelle
    scoreElement.textContent = `Skor: ${gameState.score}`;
    livesElement.textContent = `Can: ${gameState.lives}`;
    levelElement.textContent = `Level: ${gameState.level}`;
    
    // Skor değiştiğinde animasyon efekti ekle
    if (gameState.score > 0 && gameState.lastScore !== gameState.score) {
        scoreElement.classList.add('score-updated');
        setTimeout(() => {
            scoreElement.classList.remove('score-updated');
        }, 300);
        gameState.lastScore = gameState.score;
    }
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
    if (!gameState.isPaused) {
        gameLoop();
    }
}

function restartGame() {
    gameState.isGameOver = false;
    gameState.score = 0;
    gameState.lives = 3;
    gameState.level = 1;
    gameState.activePowerUps.clear();
    extraBalls = [];
    generateBricks();
    resetBall();
    gameLoop();
}

function updateDifficulty() {
    gameState.difficulty = difficultySelect.value;
    if (gameState.isGameStarted) {
        generateBricks();
        resetBall();
    }
}

function createStartScreen() {
    startScreen.style.display = 'flex';
    const highScoresList = document.getElementById('highScoresList');
    highScoresList.innerHTML = gameState.highScores
        .map((score, index) => `<div>${index + 1}. ${score}</div>`)
        .join('');
}

function drawInitialScreen() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    ctx.fillStyle = '#fff';
    ctx.font = '30px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('Tuğla Kırma Oyunu', canvas.width / 2, canvas.height / 2 - 50);
    ctx.font = '20px Arial';
    ctx.fillText('Başlamak için tıklayın', canvas.width / 2, canvas.height / 2);
}

function gameOver() {
    gameState.isGameOver = true;
    playSound('gameOver');
    restartButton.style.display = 'block';
    
    // Oyun sonu ekranı için gelişmiş popup
    const gameOverPopup = document.createElement('div');
    gameOverPopup.className = 'popup';
    
    // Yüksek skor kontrolü
    const isHighScore = gameState.highScores.length === 0 || gameState.score > Math.min(...gameState.highScores);
    const highScoreMessage = isHighScore ? '<div class="high-score-badge">Yeni Yüksek Skor!</div>' : '';
    
    gameOverPopup.innerHTML = `
        <div class="popup-content">
            <h2>Oyun Bitti!</h2>
            ${highScoreMessage}
            <p>Skorunuz: <span class="final-score">${gameState.score}</span></p>
            <p>Ulaştığınız Seviye: ${gameState.level}</p>
            <button id="playAgainBtn">Tekrar Oyna</button>
        </div>
    `;
    document.body.appendChild(gameOverPopup);
    
    // Skor animasyonu
    setTimeout(() => {
        const finalScore = document.querySelector('.final-score');
        if (finalScore) {
            finalScore.classList.add('highlight');
        }
    }, 500);
    
    document.getElementById('playAgainBtn').addEventListener('click', function() {
        document.body.removeChild(gameOverPopup);
        restartGame();
    });
}

window.addEventListener('load', initGame);