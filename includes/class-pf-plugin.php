<?php
/**
 * Orquestra o plugin.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Plugin
 */
class PF_Plugin {

	/**
	 * Liga módulos.
	 */
	public function run() {
		load_plugin_textdomain( 'perguntas-frequentes', false, dirname( PF_FAQ_BASENAME ) . '/languages' );

		add_action( 'init', array( 'PF_CPT', 'register' ), 5 );
		add_action( 'init', array( $this, 'maybe_upgrade' ), 99 );

		$shortcode = new PF_Shortcode();
		$shortcode->hooks();

		$front = new PF_Frontend();
		$front->hooks();

		if ( is_admin() ) {
			$admin = new PF_Admin();
			$admin->hooks();
		}

		$elementor = new PF_Elementor();
		$elementor->hooks();
	}

	/**
	 * Upgrade / seed tardio.
	 */
	public function maybe_upgrade() {
		$stored = get_option( 'pf_faq_version', '' );
		if ( PF_FAQ_VERSION !== $stored ) {
			flush_rewrite_rules( false );
			update_option( 'pf_faq_version', PF_FAQ_VERSION );
		}
		PF_Seed::maybe_seed();
	}
}
