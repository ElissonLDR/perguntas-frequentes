<?php
/**
 * Elementor bootstrap.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Elementor
 */
class PF_Elementor {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
	}

	/**
	 * Categoria.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Manager.
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'perguntas-frequentes',
			array(
				'title' => __( 'Perguntas Frequentes', 'perguntas-frequentes' ),
				'icon'  => 'fa fa-question-circle',
			)
		);
	}

	/**
	 * Widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
	 */
	public function register_widgets( $widgets_manager ) {
		if ( ! did_action( 'elementor/loaded' ) && ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}
		require_once PF_FAQ_PATH . 'includes/elementor/class-widget-faq.php';
		$widgets_manager->register( new PF_Widget_Faq() );
	}
}
