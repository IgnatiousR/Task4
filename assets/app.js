import './bootstrap.js';
// const $ = require('jquery');
// global.$ = global.jQuery = $;
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
// $('#select-all').on('change', function()
// {
//     if ($(this).is(':checked')) {
//         $('#test').attr('checked', 'checked')
//     }
// })

document.getElementById('form_selectAll').addEventListener('click', function() {
      var checkboxes = document.querySelectorAll('.item');
      for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = this.checked;
      }
    });

document.getElementById('hamburger_icon').addEventListener('click', function() {
    document.getElementById("mobile-menu").toggle("flex");
    document.getElementById("mobile-menu").toggle("hidden");
});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// const btn = document.getElementById("hamburger_icon");
// const mobile_menu = document.getElementById("mobile-menu");

// btn.addEventListener("click", ()=>{
//     mobile_menu.classList.toggle("flex");
//     mobile_menu.classList.toggle("hidden");
// })