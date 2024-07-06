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