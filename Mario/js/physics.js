export default class Physics {
  constructor(){
    this.gravity = 2000; // px/s^2
  }

  integrate(ent, dt){
    ent.vy += this.gravity * dt;
    ent.x += ent.vx * dt;
    ent.y += ent.vy * dt;
  }

  // tile collision AABB resolution, simple
  tileCollision(entities, level){
    const tiles = level.tiles;
    const tileSize = level.tileSize;
    for(const ent of entities.slice()){
      if(ent.solid === false) continue;
      // compute overlapped tiles
      const left = Math.floor(ent.x / tileSize);
      const right = Math.floor((ent.x + ent.w - 1) / tileSize);
      const top = Math.floor(ent.y / tileSize);
      const bottom = Math.floor((ent.y + ent.h - 1) / tileSize);

      for(let tx = left; tx <= right; tx++){
        for(let ty = top; ty <= bottom; ty++){
          const t = level.getTile(tx, ty);
          if(!t || !t.solid) continue;
          const tileRect = {x:tx*tileSize, y:ty*tileSize, w:tileSize, h:tileSize};
          const mtv = this.aabbOverlap(ent, tileRect);
          if(mtv){
            // resolve with minimum translation vector
            ent.x += mtv.x;
            ent.y += mtv.y;
            // reset velocities on appropriate axis
            if(Math.abs(mtv.y) > 0){
              if(mtv.y < 0){ // landed on tile
                ent.vy = 0;
                ent.onGround = true;
              } else {
                ent.vy = 0;
              }
            }
            if(Math.abs(mtv.x) > 0){
              ent.vx = 0;
            }
          }
        }
      }
    }
  }

  aabbOverlap(a,b){
    const ax1 = a.x, ay1 = a.y, ax2 = a.x + a.w, ay2 = a.y + a.h;
    const bx1 = b.x, by1 = b.y, bx2 = b.x + b.w, by2 = b.y + b.h;
    if(ax2 <= bx1 || ax1 >= bx2 || ay2 <= by1 || ay1 >= by2) return null;
    // compute minimal translation
    const overlapX = Math.min(ax2 - bx1, bx2 - ax1);
    const overlapY = Math.min(ay2 - by1, by2 - ay1);
    if(overlapX < overlapY){
      // push on x
      return {x: ax2 - bx1 < bx2 - ax1 ? -overlapX : overlapX, y:0};
    } else {
      // push on y
      return {x:0, y: ay2 - by1 < by2 - ay1 ? -overlapY : overlapY};
    }
  }
}
