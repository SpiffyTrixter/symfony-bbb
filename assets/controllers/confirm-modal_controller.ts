import {Controller} from "@hotwired/stimulus";

export default class extends Controller {
  static readonly targets = ['action'];
  declare readonly actionTarget: HTMLElement;

  open(action: string) {
    console.log('open', action);

    if (action !== '' && action !== null && action !== undefined) {
      this.actionTarget!.setAttribute('href', action);
    }
  }
}
