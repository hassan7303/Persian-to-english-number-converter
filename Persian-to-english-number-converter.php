<?php

/**
 * Plugin Name: Persian to English Number Converter
 *
 * Description: Converts Persian numbers to English numbers in the phone number field for Digits registration.
 *
 * Version: 1.0.0
 *
 * Author: Hassan Ali Askari
 * Author URI: https://t.me/hassan7303
 * Plugin URI: https://github.com/hassan7303
 *
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 */

 if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

function add_custom_js_to_footer() {
    ?>
   <script>
     function persianToEnglish(numStr) {
          const persianNums = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
          const englishNums = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
          let result = "";
    
          for (let i = 0; i < numStr.length; i++) {
            const char = numStr[i];
            const persianIndex = persianNums.indexOf(char);
            if (persianIndex !== -1) {
              result += englishNums[persianIndex];
            } else {
              result += char;
            }
          }                      
          return result;
     };
     document.addEventListener('DOMContentLoaded', function() {
       document.querySelector(".woocommerce-account #reg_email").addEventListener('blur', function(event) {
            let phoneField = document.querySelector("#reg_email");
            if (phoneField) {
                let persianNumber = phoneField.value;
                console.log("persianNumber:"+persianNumber);
                let englishNumber = persianToEnglish(persianNumber);
                console.log("englishNumber:"+englishNumber);
                phoneField.value = englishNumber;
            }
            console.log("phoneField.value:"+phoneField.value);
            console.log(event.target);
            });
    });
    </script>   
    <?php
 }
 add_action('wp_footer', 'add_custom_js_to_footer');