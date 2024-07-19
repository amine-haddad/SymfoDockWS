import { Controller } from '@hotwired/stimulus';

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
    static targets = ["body"];

    connect() {
        // Récupère le mode sombre à partir du localStorage
        const darkMode = JSON.parse(localStorage.getItem('darkMode'));
        // Applique le thème correspondant
        this.updateTheme(darkMode !== null ? darkMode : false);
    }

    toggleDarkMode(event) {
        // Inverse le mode sombre
        this.darkMode = !this.darkMode;
        // Stocke la nouvelle valeur dans le localStorage
        localStorage.setItem('darkMode', this.darkMode);
        // Met à jour le thème
        this.updateTheme(this.darkMode);
    }

    updateTheme(darkMode) {
        // Ajoute ou enlève l'attribut data-bs-theme en fonction du mode sombre
        this.bodyTarget.setAttribute('data-bs-theme', darkMode ? 'dark' : '');
    }
}