import { Controller } from '@hotwired/stimulus';
import fitty from 'fitty';

export default class extends Controller<HTMLElement> {
  connect() {
    fitty(this.element);
  }
}
