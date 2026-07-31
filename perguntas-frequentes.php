<?php
/**
 * Plugin Name:       Perguntas Frequentes
 * Plugin URI:        https://github.com/ElissonLDR/perguntas-frequentes
 * Description:       Gerencie FAQs com categorias, busca e acordeão. Shortcode e widget Elementor.
 * Version:           1.0.5
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            ElissonLDR
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       perguntas-frequentes
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PF_FAQ_VERSION', '1.0.5' );
define( 'PF_FAQ_FILE', __FILE__ );
define( 'PF_FAQ_PATH', plugin_dir_path( __FILE__ ) );
define( 'PF_FAQ_URL', plugin_dir_url( __FILE__ ) );
define( 'PF_FAQ_BASENAME', plugin_basename( __FILE__ ) );

require_once PF_FAQ_PATH . 'includes/class-pf-cpt.php';
require_once PF_FAQ_PATH . 'includes/class-pf-seed.php';
require_once PF_FAQ_PATH . 'includes/class-pf-query.php';
require_once PF_FAQ_PATH . 'includes/class-pf-frontend.php';
require_once PF_FAQ_PATH . 'includes/class-pf-shortcode.php';
require_once PF_FAQ_PATH . 'includes/class-pf-elementor.php';
require_once PF_FAQ_PATH . 'includes/class-pf-admin.php';
require_once PF_FAQ_PATH . 'includes/class-pf-plugin.php';

/**
 * Ativação.
 */
function pf_faq_activate() {
	PF_CPT::register();
	flush_rewrite_rules( false );
	update_option( 'pf_faq_version', PF_FAQ_VERSION );
	PF_Seed::maybe_seed();
}
register_activation_hook( __FILE__, 'pf_faq_activate' );

/**
 * Desativação.
 */
function pf_faq_deactivate() {
	flush_rewrite_rules( false );
}
register_deactivation_hook( __FILE__, 'pf_faq_deactivate' );

/**
 * Boot.
 */
function pf_faq_run() {
	$plugin = new PF_Plugin();
	$plugin->run();
}
add_action( 'plugins_loaded', 'pf_faq_run' );
