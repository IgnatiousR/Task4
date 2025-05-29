    import { Controller } from "@hotwired/stimulus"

    export default class extends Controller {
      static targets = ["hideable"]

      toggleVisibility() {
        this.hideableTargets.forEach(element => {
          element.classList.toggle("hidden");
        });
      }
    }