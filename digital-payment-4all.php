<?php
/**
  * Plugin Name: Pagamentos Digitais 4all for classipress
  * Plugin URI:  https://github.com/4alltecnologia/plugin_classipress.git
  * Description: Includes 4all as a payment gateway to theme classipress designed by AppThemes
  * Author: 4all
  * Version: 1.0.0
  * License: GPLv2 or later
  * Text Domain: digital-payment-4all
  * Domain Path: /languages
  *
  * 4all is free software: you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
  * the Free Software Foundation, either version 2 of the License, or
  * any later version.
  *
  * 4all is distributed in the hope that it will be useful,
  * but WITHOUT ANY WARRANTY; without even the implied warranty of
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  * GNU General Public License for more details.
  *
  * You should have received a copy of the GNU General Public License
  * along with 4all. If not, see
  * <https://www.gnu.org/licenses/gpl-2.0.txt>.
*/

function init_setup_4all_classipress(){
  load_plugin_textdomain('digital-payment-4all', false, dirname(plugin_basename( __FILE__ )) . '/languages/');
  include 'includes/class-digital-payment-4all.php';
}

function add_css_4all_classipress() {
  $basePluginName = plugin_basename(plugin_dir_path( __FILE__ ));
  $styleUrl = plugins_url( $basePluginName . '/assets/css/4all-style.css');
  wp_enqueue_style( 'app_4all_style', $styleUrl);
}

function add_js_4all_classipress() {
  $basePluginName = plugin_basename(plugin_dir_path( __FILE__ ));
  $scriptUrl = plugins_url( $basePluginName . '/assets/js/4all-scripts.js');
  wp_enqueue_script( 'app_4all_script', $scriptUrl, array('jquery'));
}

add_action( 'wp_enqueue_scripts', 'add_css_4all_classipress' );
add_action( 'wp_footer', 'add_js_4all_classipress' );

add_action( 'init', 'init_setup_4all_classipress' );