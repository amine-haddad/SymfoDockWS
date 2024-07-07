import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.scss';

/**
 * Import Bootstrap JavaScript library.
 * This library provides the necessary JavaScript functionality for Bootstrap components.
 * 
 * @see {@link https://getbootstrap.com/docs/5.0/getting-started/javascript/|Bootstrap JavaScript Documentation}
 */
import 'bootstrap';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
document.addEventListener('DOMContentLoaded', function() {
    function updateTime() {
        var now = new Date();
        var hours = now.getHours();
        var minutes = now.getMinutes();
        var seconds = now.getSeconds();

        // Formatage pour toujours avoir deux chiffres pour les minutes et les secondes
        minutes = ('0' + minutes).slice(-2);
        seconds = ('0' + seconds).slice(-2);

        var timeString = hours + ':' + minutes + ':' + seconds;
        document.getElementById('current-time').innerHTML = timeString;

        // Rafraîchir l'heure toutes les secondes
        setTimeout(updateTime, 1000);
    }

    // Appel initial pour démarrer l'heure
    updateTime();
});