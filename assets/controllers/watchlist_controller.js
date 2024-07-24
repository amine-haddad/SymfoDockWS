import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static values = {
        url: String
    }

    connect() {

    }

    async toggle(event) {
        event.preventDefault();

        const response = await fetch(this.urlValue, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (response.ok) {
            const data = await response.json();
            // Mettre à jour l'interface utilisateur si nécessaire
        } else {
            console.error('Erreur lors de la mise à jour de la watchlist');
        }
    }
}