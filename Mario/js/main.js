// Ana giriş noktası modüler motoru yükler ve oyunu başlatır
import Input from './engine/input.js';
import Camera from './engine/camera.js';
import { EntityManager, Player, Enemy, Coin, Tile } from './engine/entities.js';
import Physics from './engine/physics.js';
import Level from './engine/level.js';

// Canvas setup
const canvas = document.getElementById('game');
const ctx = canvas.getContext('2d');

const WIDTH = canvas.width;
const HEIGHT = canvas.height;

const input = new Input();
const camera = new Camera(WIDTH, HEIGHT);
const physics = new Physics();

const entityManager = new EntityManager();

// Basit seviye arrayi 0 = boş 1 = blok 2 = coin 3 = enemy spawn 4 = goal
const levelData = [
  "........................................................",
  "........................................................",
  "........................................................",
  "........................................................",
  "...............c........................................",
  "...........#####.........e.............................",
  "...................................#####...............",
  "....####.......................####....................",
  "....................###...........................G....",
  "###############################################..######",
];

const TILE = 64;
const level = new Level(levelData, TILE);

function setup() {
  // Oyuncuyu yerleştir
  const player = new Player(120, HEIGHT - 3 * TILE - 40);
  entityManager.add(player);
  // leveldeki coin ve enemy tilelarına göre entity oluştur
  level.spawnEntities(entityManager);
  camera.follow(player);

  // Touch UI setup
  const controls = document.getElementById('controls');
  if ('ontouchstart' in window) {
    controls.classList.remove('hidden');
    document.getElementById('leftBtn').addEventListener('touchstart', e => { input.setKey('left', true); });
    document.getElementById('leftBtn').addEventListener('touchend', e => { input.setKey('left', false); });
    document.getElementById('rightBtn').addEventListener('touchstart', e => { input.setKey('right', true); });
    document.getElementById('rightBtn').addEventListener('touchend', e => { input.setKey('right', false); });
    document.getElementById('jumpBtn').addEventListener('touchstart', e => { input.setKey('jump', true); });
    document.getElementById('jumpBtn').addEventListener('touchend', e => { input.setKey('jump', false); });
  }

  // keyboard mappings
  input.mapKey('ArrowLeft', 'left');
  input.mapKey('ArrowRight', 'right');
  input.mapKey('ArrowUp', 'jump');
  input.mapKey('Space', 'jump');

  requestAnimationFrame(loop);
}

let last = performance.now();
let accumulator = 0;
const fixedDelta = 1/60;

let score = 0;
let lives = 3;

function loop(now) {
  const dt = (now - last)/1000;
  last = now;
  accumulator += dt;
  // fixed timestep
  while (accumulator >= fixedDelta) {
    update(fixedDelta);
    accumulator -= fixedDelta;
  }
  render();
  requestAnimationFrame(loop);
}

function update(dt) {
  entityManager.updateAll(dt, input, physics, level);
  // collisions with tiles
  physics.tileCollision(entityManager.entities, level);
  camera.update(dt);
  // update UI
  document.getElementById('score').textContent = `Puan: ${score}`;
  document.getElementById('lives').textContent = `Can: ${lives}`;
  // handle pickups and simple interactions
  entityManager.entities.forEach(ent => {
    if (ent.type === 'coin' && ent.picked) {
      score += 10;
      entityManager.remove(ent);
    }
    if (ent.type === 'enemy' && ent.dead) {
      score += 50;
      entityManager.remove(ent);
    }
    if (ent.type === 'player' && ent.gotHit) {
      lives -= 1;
      ent.respawn();
      ent.gotHit = false;
      if (lives <= 0) {
        resetGame();
      }
    }
    if (ent.type === 'player' && ent.reachedGoal) {
      // basit level complete
      alert('Level tamamlandı Tebrikler');
      resetGame();
    }
  });
}

function resetGame() {
  // basit reset
  score = 0;
  lives = 3;
  entityManager.clear();
  setup(); // yeniden başlat
}

function render() {
  ctx.clearRect(0,0,WIDTH,HEIGHT);

  // background parallax
  drawBackground(ctx, camera);

  // draw tiles
  level.draw(ctx, camera);

  // draw entities
  entityManager.drawAll(ctx, camera);

  // debug camera (opsiyonel)
  // ctx.fillStyle='rgba(255,0,0,0.2)'; ctx.fillRect(0,0,50,50);
}

function drawBackground(ctx, camera) {
  // very simple layered background
  const skyGradient = ctx.createLinearGradient(0,0,0,HEIGHT);
  skyGradient.addColorStop(0,'#87ceeb');
  skyGradient.addColorStop(1,'#7ec0ee');
  ctx.fillStyle = skyGradient;
  ctx.fillRect(0,0,WIDTH,HEIGHT);

  // distant hills
  ctx.save();
  ctx.translate(-camera.x * 0.2, 40);
  ctx.fillStyle = '#3ecf7a';
  for (let i= -2; i<30; i++) {
    const x = i * 300;
    ctx.beginPath();
    ctx.ellipse(x, 440, 200, 80, 0, 0, Math.PI*2);
    ctx.fill();
  }
  ctx.restore();
}

setup();
