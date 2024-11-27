<?php

/**
 * Plugin Name: Persian to English Number Converter
 *
 * Description: Converts Persian numbers to English numbers in the phone number field for Digits registration.
 *
 * Version: 2.1.0
 *
 * Author: Hassan Ali Askari
 * Author URI: https://t.me/hassan7303
 * Plugin URI: https://github.com/hassan7303/Persian-to-english-number-converter
 * GitHub Plugin URI: https://github.com/hassan7303/Persian-to-english-number-converter
 * GitHub Branch: master
 * 
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 */

 if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}


/**
 * Injects custom JavaScript to convert Persian numbers to English numbers in specific fields.
 *
 * The script listens for blur events on specified fields and converts any Persian numbers entered
 * into English numbers for compatibility purposes.
 *
 * @return void Outputs JavaScript directly to the footer.
 */
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

      function convertFieldToEnglish(selector) {
        let phoneNumberField = document.querySelector(selector);
        if (!phoneNumberField) return;

        phoneNumberField.addEventListener('blur', function(event) {
          let persianNumber = phoneNumberField.value;
          let englishNumber = persianToEnglish(persianNumber);
          phoneNumberField.value = englishNumber;
        });
      }

      document.addEventListener('DOMContentLoaded', function() {
        convertFieldToEnglish(".woocommerce-account #reg_email");
        convertFieldToEnglish(".woocommerce-account #username");
      });
    </script>   
    <?php
 }
 add_action('wp_footer', 'add_custom_js_to_footer');



 /**
 * Checks for plugin updates from GitHub and notifies WordPress.
 *
 * Fetches the latest release information from GitHub. If a newer version is available,
 * it adds the update to the WordPress update queue.
 *
 * @param object $transient The transient object containing update information.
 * @return object Modified transient object with update details if available.
 */
function check_for_plugin_update($transient) {
  if (empty($transient->checked)) {
      return $transient;
  }

  $plugin_slug = plugin_basename(__FILE__);
  $github_url = 'https://api.github.com/repos/hassan7303/Persian-to-english-number-converter/releases/latest';

  $response = wp_remote_get($github_url, ['sslverify' => false]);
  if (is_wp_error($response)) {
      return $transient;
  }

  $release_info = json_decode(wp_remote_retrieve_body($response), true);

  if (isset($release_info['tag_name']) && isset($transient->checked[$plugin_slug])) {
      $new_version = $release_info['tag_name'];
      if (version_compare($transient->checked[$plugin_slug], $new_version, '<')) {
          $transient->response[$plugin_slug] = (object) [
              'slug' => $plugin_slug,
              'new_version' => $new_version,
              'package' => $release_info['zipball_url'],
              'url' => 'https://github.com/hassan7303/Persian-to-english-number-converter',
          ];
      }
  }

  return $transient;
}
add_filter('pre_set_site_transient_update_plugins', 'check_for_plugin_update');

/**
* Adjust plugin folder name after installation to maintain original name.
*/
function fix_plugin_folder_name($response, $hook_extra, $result) {
  global $wp_filesystem;

  $plugin_slug = 'Persian-to-english-number-converter';
  $original_folder = WP_PLUGIN_DIR . '/' . $plugin_slug;
  $new_folder = $result['destination'];

  if (basename($new_folder) !== $plugin_slug) {
      $wp_filesystem->move($new_folder, $original_folder);
      $result['destination'] = $original_folder;
  }

  return $response;
}
add_filter('upgrader_post_install', 'fix_plugin_folder_name', 10, 3);
