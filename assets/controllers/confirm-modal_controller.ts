import {Controller} from "@hotwired/stimulus";

export default class extends Controller<HTMLDivElement> {
  static readonly targets = ['action'];
  declare readonly actionTarget: HTMLElement;

  connect() {
    this.element.addEventListener('show.bs.modal', this.open.bind(this));
  }

  open(event) {
    console.log(event);
    const action = (event.relatedTarget as HTMLElement).getAttribute('data-action');

    if (action) {
      this.actionTarget!.setAttribute('href', action);
    }
  }
}
