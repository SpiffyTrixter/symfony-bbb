import { Controller } from '@hotwired/stimulus';
import { Tooltip } from 'bootstrap';

enum Placement {
  TOP = 'top',
  BOTTOM = 'bottom',
  LEFT = 'left',
  RIGHT = 'right',
}

export default class extends Controller {
  static values:{ title: string, placement: Placement } = {
    title: 'Tooltip on top',
    placement: Placement.TOP,
  }

  declare titleValue: string|undefined;
  declare placementValue: Placement|undefined;

  connect() {
    this.element.attributes.setNamedItem(document.createAttribute('data-bs-toggle'));
    this.element.attributes.setNamedItem(document.createAttribute('data-bs-placement'));
    this.element.attributes.setNamedItem(document.createAttribute('data-bs-title'));

    this.element.setAttribute('data-bs-toggle', 'tooltip');
    this.element.setAttribute('data-bs-placement', this.placementValue);
    this.element.setAttribute('data-bs-title', this.titleValue);

    new Tooltip(this.element);
  }
}
