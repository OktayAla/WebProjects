export default class Camera {
  constructor(viewW, viewH){
    this.viewW = viewW;
    this.viewH = viewH;
    this.x = 0;
    this.y = 0;
    this.target = null;
  }
  follow(entity){
    this.target = entity;
  }
  update(dt){
    if(!this.target) return;
    // simple smooth follow
    const targetX = this.target.x - this.viewW/2 + this.target.w/2;
    const targetY = this.target.y - this.viewH/2 + this.target.h/2;
    this.x += (targetX - this.x) * Math.min(1, 5*dt);
    this.y += (targetY - this.y) * Math.min(1, 5*dt);
    if(this.y < 0) this.y = 0;
  }
}
