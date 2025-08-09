// Ana giriş noktası modüler motoru yükler ve oyunu başlatır
import Input from './engine/input.js';
import Camera from './engine/camera.js';
import { EntityManager, Player } from './engine/entities.js';
import Physics from './engine/physics.js';
import Level from './engine/level.js';

// Canvas setup
let canvas, ctx, WIDTH, HEIGHT;
let input, camera, physics, entityManager, level;
let score = 0, lives = 3;
let last = 0, accumulator = 0;
const fixedDelta = 1/60;
const TILE = 64;
const levelData = [
  "                                                  ",
  "                                                  ",
  "                    c                             ",
  "                  #####      e                    ",
  "         ##                      ####             ",
  "                          ###                 G   ",
  "##################################################"
];

function init() {
  canvas = document.getElementById('game');
  ctx = canvas.getContext('2d');
  WIDTH = canvas.width;
  HEIGHT = canvas.height;

  input = new Input();
  camera = new Camera(WIDTH, HEIGHT);
  physics = new Physics();
  entityManager = new EntityManager();
  level = new Level(levelData, TILE);

  score = 0;
  lives = 3;
  setup();
}

function setup() {
  entityManager.clear();
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
    document.getElementById('leftBtn').ontouchstart = e => { input.setKey('left', true); };
    document.getElementById('leftBtn').ontouchend = e => { input.setKey('left', false); };
    document.getElementById('rightBtn').ontouchstart = e => { input.setKey('right', true); };
    document.getElementById('rightBtn').ontouchend = e => { input.setKey('right', false); };
    document.getElementById('jumpBtn').ontouchstart = e => { input.setKey('jump', true); };
    document.getElementById('jumpBtn').ontouchend = e => { input.setKey('jump', false); };
  }

  // keyboard mappings
  input.mapKey('ArrowLeft', 'left');
  input.mapKey('ArrowRight', 'right');
  input.mapKey('ArrowUp', 'jump');
  input.mapKey('Space', 'jump');

  last = performance.now();
  accumulator = 0;
  requestAnimationFrame(loop);
}

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
  for (let i = entityManager.entities.length - 1; i >= 0; i--) {
    const ent = entityManager.entities[i];
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
      setTimeout(() => {
        alert('Level tamamlandı Tebrikler');
        resetGame();
      }, 100);
    }
  }
}

function resetGame() {
  score = 0;
  lives = 3;
  setup();
}

function render() {
  ctx.clearRect(0,0,WIDTH,HEIGHT);
  drawBackground(ctx, camera);
  level.draw(ctx, camera);
  entityManager.drawAll(ctx, camera);
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

// Oyun otomatik başlasın
window.addEventListener('DOMContentLoaded', init);
  }
  ctx.restore();
}

setup();

setup();
