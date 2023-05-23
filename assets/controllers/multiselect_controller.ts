import { Multiselect } from '@wizardhealth/stimulus-multiselect';

export default class extends Multiselect {
  connect() {
    super.connect()

    console.log(super.element);
    console.log(super.hiddenTarget);

    super.element.addEventListener('multiselect-change', (e: CustomEvent) => {
      console.log('multiselect-change', e.detail);
      super.element.querySelector('select').dispatchEvent(new Event('change', { bubbles: true }));
    });
  }
}
