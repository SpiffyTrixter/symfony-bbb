import {Controller} from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
  static targets:string[] = [ "next" ];
  static values:{ red: number, green: number, blue: number } = {
    red: 0,
    green: 0,
    blue: 0,
  };
  private redValue: number|undefined;
  private greenValue: number|undefined;
  private blueValue: number|undefined;
  private nextTarget: HTMLElement|undefined;

  connect() {
    this.nextTarget!.style.backgroundColor = `rgb(${this.redValue}, ${this.greenValue}, ${this.blueValue})`;
    console.log('Hello, Stimulus!', this.nextTarget);
  }

  changeColor(event: Event) {
    const target = event.target as HTMLInputElement;

    //get name of event target
    const color = target.getAttribute('data-color');

    //get value of event target
    const value = target?.value;

    //set value of color
    this[`${color}Value`] = parseInt(value!);

    this.nextTarget!.style.backgroundColor = `rgb(${this.redValue}, ${this.greenValue}, ${this.blueValue})`;
  }

  next() {
    //redirect to next page url is in data attribute of nextTarget
    const urlString: string = this.nextTarget?.getAttribute('data-url');
    const url = new URL(urlString!);

    //set query params
    url.searchParams.set('red', this.redValue!.toString());
    url.searchParams.set('green', this.greenValue!.toString());
    url.searchParams.set('blue', this.blueValue!.toString());

    //redirect
    window.location.href = url.toString();
  }
}
