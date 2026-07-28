<?php
/**
 * Widget Elementor: FAQ.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Widget_Faq
 */
class PF_Widget_Faq extends \Elementor\Widget_Base {

	/**
	 * @return string
	 */
	public function get_name() {
		return 'pf-faq';
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return __( 'Perguntas Frequentes', 'perguntas-frequentes' );
	}

	/**
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-help-o';
	}

	/**
	 * @return array
	 */
	public function get_categories() {
		return array( 'perguntas-frequentes', 'general' );
	}

	/**
	 * @return array
	 */
	public function get_keywords() {
		return array( 'faq', 'perguntas', 'acordeão', 'ajuda' );
	}

	/**
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'pf-faq-front' );
	}

	/**
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'pf-faq-front' );
	}

	/**
	 * Controles.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => __( 'Conteúdo', 'perguntas-frequentes' ),
			)
		);

		$this->add_control(
			'heading',
			array(
				'label'   => __( 'Título', 'perguntas-frequentes' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Como podemos ajudar?', 'perguntas-frequentes' ),
			)
		);

		$this->add_control(
			'subtitle',
			array(
				'label'   => __( 'Subtítulo', 'perguntas-frequentes' ),
				'type'    => \Elementor\Controls_Manager::TEXTAREA,
				'default' => __( 'Busque por palavra-chave ou escolha um tema para encontrar a resposta mais rápido.', 'perguntas-frequentes' ),
			)
		);

		$options = array( '' => __( 'Todas as categorias', 'perguntas-frequentes' ) );
		$terms   = get_terms(
			array(
				'taxonomy'   => PF_CPT::TAXONOMY,
				'hide_empty' => false,
			)
		);
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$options[ $term->slug ] = $term->name;
			}
		}

		$this->add_control(
			'category',
			array(
				'label'   => __( 'Filtrar categoria', 'perguntas-frequentes' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => $options,
				'default' => '',
			)
		);

		$this->add_control(
			'show_search',
			array(
				'label'        => __( 'Mostrar busca', 'perguntas-frequentes' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_categories',
			array(
				'label'        => __( 'Mostrar categorias', 'perguntas-frequentes' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'open_first',
			array(
				'label'        => __( 'Abrir primeira pergunta', 'perguntas-frequentes' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render() {
		$s = $this->get_settings_for_display();
		echo PF_Frontend::render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			array(
				'heading'         => $s['heading'],
				'subtitle'        => $s['subtitle'],
				'category'        => $s['category'],
				'show_search'     => 'yes' === $s['show_search'],
				'show_categories' => 'yes' === $s['show_categories'],
				'open_first'      => 'yes' === $s['open_first'],
			)
		);
	}
}
