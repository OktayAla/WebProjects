import { Coin, Enemy, Tile } from './entities.js';

export default class Level {
  constructor(lines, tileSize=64){
    this.tileSize = tileSize;
    this.width = lines[0].length;
    this.height = lines.length;
    this.tiles = [];
    this.lines = lines;
    for(let y=0;y<this.height;y++){
      this.tiles[y] = [];
      for(let x=0;x<this.width;x++){
        const ch = lines[y][x];
        if(ch === '#'){
          this.tiles[y][x] = new Tile(x*tileSize,y*tileSize,tileSize,true);
        } else {
          this.tiles[y][x] = null;
        }
      }
    }
    this.lines = lines;
  }

  getTile(tx,ty){
    if(tx < 0 || tx >= this.width || ty < 0 || ty >= this.height) return null;
    return this.tiles[ty][tx];
  }

  spawnEntities(entityManager){
    for(let y=0;y<this.height;y++){
      for(let x=0;x<this.width;x++){
        const ch = this.lines[y][x];
        const px = x*this.tileSize;
        const py = y*this.tileSize;
        if(ch === 'c'){
          entityManager.add(new Coin(px+this.tileSize/4, py+this.tileSize/4));
        } else if(ch === 'e'){
          entityManager.add(new Enemy(px, py));
        } else if(ch === 'G'){
          // Goal tile as a non-solid entity
          entityManager.add({type:'goal', x:px, y:py, w:this.tileSize, h:this.tileSize, draw(ctx,camera){
            ctx.fillStyle = '#ffd166';
            ctx.fillRect(this.x - camera.x, this.y - camera.y, this.w, this.h);
            ctx.fillStyle = '#333';
            ctx.fillText('GOAL', this.x - camera.x + 10, this.y - camera.y + 40);
          }});
        }
      }
    }
  }

  draw(ctx, camera){
    const ts = this.tileSize;
    const startX = Math.floor(camera.x / ts);
    const endX = Math.floor((camera.x + camera.viewW) / ts)+1;
    const startY = Math.floor(camera.y / ts);
    const endY = Math.floor((camera.y + camera.viewH) / ts)+1;
    ctx.fillStyle='#7ec0ff';
    for(let y = startY; y < endY; y++){
      for(let x = startX; x < endX; x++){
        const t = this.getTile(x,y);
        if(t){
          t.draw(ctx, camera);
        }
      }
    }
  }
}
