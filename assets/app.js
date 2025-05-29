import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

const btn = document.getElementById("hamburger-icon");
const mobile_menu = document.getElementById("mobile-menu");

btn.addEventListener("click", ()=>{
    mobile_menu.classList.toggle("flex");
    mobile_menu.classList.toggle("hidden");
})