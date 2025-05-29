import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [ "name", "output" ]

    toggle() {
    this.outputTarget.textContent =
      `Hello, ${this.nameTarget.value}!`
  }
}
