import {Controller} from '@hotwired/stimulus';

export default class extends Controller<HTMLFormElement> {
  static targets:string[] = [ 'colorMix', 'red', 'green', 'blue' ];
  static values:{ red: number, green: number, blue: number } = {
    red: 0,
    green: 0,
    blue: 0,
  };
  declare redValue: number|undefined;
  declare greenValue: number|undefined;
  declare blueValue: number|undefined;
  declare colorMixTarget: HTMLElement|undefined;
  declare redTarget: HTMLSpanElement|undefined;
  declare greenTarget: HTMLSpanElement|undefined;
  declare blueTarget: HTMLSpanElement|undefined;

  connect() {
    this.colorMixTarget!.style.backgroundColor = `rgb(${this.redValue}, ${this.greenValue}, ${this.blueValue})`;
  }

  changeColor(event: Event) {
    const target = event.target as HTMLInputElement;

    //get name of event target
    const color = target.getAttribute('name');

    //get value of event target
    const value = target?.value;

    //set value of color
    this[`${color}Value`] = parseInt(value!);

    //set background color of colorMixTarget
    this.colorMixTarget!.style.backgroundColor = `rgb(${this.redValue}, ${this.greenValue}, ${this.blueValue})`;

    //set value of color input
    this[`${color}Target`]!.textContent = this[`${color}Value`];
  }
}
