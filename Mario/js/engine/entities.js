// Entities manager and classes: Player Enemy Coin Tile
export class EntityManager {
  constructor(){ this.entities = []; }
  add(e){ this.entities.push(e); }
  updateAll(dt,input,physics,level){
    for(const e of this.entities){
      if(typeof e.update === 'function'){
        e.update(dt,input,physics,level,this);
      }
    }
    // physics integrate for entities with physics
    for(const e of this.entities){
      if(e.physics !== false){
        physics.integrate(e, dt);
      }
    }
  }
  drawAll(ctx,camera){
    for(const e of this.entities){
      if(typeof e.draw === 'function'){
        e.draw(ctx,camera);
      }
    }
  }
  remove(entity){
    const i = this.entities.indexOf(entity);
    if(i>=0) this.entities.splice(i,1);
  }
  clear(){ this.entities.length = 0; }
}

export class Tile {
  constructor(x,y,size,solid=true){
    this.x = x; this.y = y; this.w = size; this.h = size; this.solid = solid;
    this.type='tile';
  }
  draw(ctx,camera){
    const sx = this.x - camera.x;
    const sy = this.y - camera.y;
    ctx.fillStyle = '#5f8a44';
    ctx.fillRect(sx,sy,this.w,this.h);
    ctx.strokeStyle = '#2f5a22';
    ctx.strokeRect(sx,sy,this.w,this.h);
  }
}

export class Player {
  constructor(x,y){
    this.type='player';
    this.x = x; this.y = y;
    this.w = 44; this.h = 60;
    this.vx = 0; this.vy = 0;
    this.speed = 280;
    this.jumpPower = -720;
    this.onGround = false;
    this.solid = true;
    this.physics = true;
    this.gotHit = false;
    this.reachedGoal = false;
    // animation timer
    this.frame = 0;
    this.frameTimer = 0;
    // respawn
    this.startX = x; this.startY = y;
  }

  update(dt,input,physics,level,manager){
    // controls
    const left = input.isDown('left');
    const right = input.isDown('right');
    const jump = input.isDown('jump');

    // horizontal movement
    if(left) this.vx = -this.speed;
    else if(right) this.vx = this.speed;
    else this.vx = 0;

    // jump
    if(jump && this.onGround){
      this.vy = this.jumpPower;
      this.onGround = false;
    }

    // reset onGround each frame then physics can set it
    this.onGround = false;

    // interactions with simple entities
    for(const e of manager.entities){
      if(e === this) continue;
      if(this.aabbIntersect(e)){
        if(e.type === 'coin'){
          e.picked = true;
        } else if(e.type === 'enemy'){
          // if player is falling onto enemy enemy dies else player gets hit
          if(this.vy > 100){
            e.dead = true;
            this.vy = -300; // bounce
          } else {
            this.gotHit = true;
          }
        } else if(e.type === 'goal'){
          this.reachedGoal = true;
        }
      }
    }

    // basic friction
    // handled by immediate setting vx in this implementation

    // simple animation
    this.frameTimer += dt;
    if(this.frameTimer > 0.12){
      this.frame = (this.frame+1) % 4;
      this.frameTimer = 0;
    }
  }

  aabbIntersect(e){
    return !(this.x + this.w <= e.x || this.x >= e.x + e.w || this.y + this.h <= e.y || this.y >= e.y + e.h);
  }

  respawn(){
    this.x = this.startX;
    this.y = this.startY;
    this.vx = 0; this.vy = 0;
  }

  draw(ctx,camera){
    const sx = this.x - camera.x;
    const sy = this.y - camera.y;
    // simple character drawing with head + body
    ctx.save();
    ctx.translate(sx, sy);
    // shadow
    ctx.fillStyle = 'rgba(0,0,0,0.15)';
    ctx.fillRect(5, this.h-8, this.w-10, 6);
    // body
    ctx.fillStyle = '#ff4757';
    ctx.fillRect(6, 8, this.w-12, this.h-16);
    // head
    ctx.fillStyle = '#ffd9b3';
    ctx.beginPath();
    ctx.ellipse(this.w/2, 8, 18, 12, 0, 0, Math.PI*2);
    ctx.fill();
    // eyes
    ctx.fillStyle = '#333';
    ctx.fillRect(this.w/2 - 6, 4, 4, 6);
    ctx.fillRect(this.w/2 + 4, 4, 4, 6);
    ctx.restore();
  }
}

export class Coin {
  constructor(x,y){
    this.type='coin';
    this.x=x; this.y=y; this.w=32; this.h=32;
    this.picked = false;
    this.physics=false;
    this.solid=false;
    this.frame=0;
  }
  update(dt){
    this.frame += dt * 6;
  }
  draw(ctx,camera){
    if(this.picked) return;
    const sx = this.x - camera.x;
    const sy = this.y - camera.y;
    ctx.save();
    ctx.translate(sx,sy);
    ctx.beginPath();
    ctx.fillStyle = '#ffd700';
    ctx.ellipse(16,16,14,12,0,0,Math.PI*2);
    ctx.fill();
    ctx.strokeStyle = '#b8860b';
    ctx.stroke();
    ctx.restore();
  }
}

export class Enemy {
  constructor(x,y){
    this.type='enemy';
    this.x = x; this.y = y; this.w=56; this.h=48;
    this.vx = -60;
    this.vy = 0;
    this.solid = true;
    this.physics = true;
    this.dead = false;
    this.onGround = false;
  }
  update(dt,input,physics,level,manager){
    // basic patrol
    if(this.dead) return;
    // if hits edge or tile in front reverse
    // simple lookahead
    const aheadX = this.vx < 0 ? this.x - 5 : this.x + this.w + 5;
    // gravity handled by physics integrate
    // simple collision with tiles will zero vx; we reverse if vx==0
    // but since integration comes later we do check approximate
    // simple flip based on a timer or random
    // keep vx constant
  }
  draw(ctx,camera){
    if(this.dead) return;
    const sx = this.x - camera.x;
    const sy = this.y - camera.y;
    ctx.save();
    ctx.translate(sx,sy);
    ctx.fillStyle = '#2f3542';
    ctx.fillRect(0,0,this.w,this.h);
    ctx.fillStyle = '#fff';
    ctx.fillRect(8,12,10,10);
    ctx.fillRect(this.w-18,12,10,10);
    ctx.restore();
  }
}
