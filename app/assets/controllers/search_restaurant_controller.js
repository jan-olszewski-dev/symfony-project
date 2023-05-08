import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["name"];
    static values = {url: String};

    search() {
        window.location.href = `${this.urlValue}/${this.nameTarget.value}`;
    }
}
