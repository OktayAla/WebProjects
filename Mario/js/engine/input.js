export default class Input {
  constructor(){
    this.keyMap = {};
    this.state = {};
    window.addEventListener('keydown', e => {
      const name = this.keyMap[e.code] || e.key;
      if(name) this.state[name] = true;
    });
    window.addEventListener('keyup', e => {
      const name = this.keyMap[e.code] || e.key;
      if(name) this.state[name] = false;
    });
    // mouse/touch prevents focus loss simple handlers could be added
  }
  mapKey(code, name){ this.keyMap[code] = name; }
  isDown(name){ return !!this.state[name]; }
  setKey(name, val){ this.state[name] = val; }
}
