<?php
/**
 * Shortcode.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Shortcode
 */
class PF_Shortcode {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_shortcode( 'perguntas_frequentes', array( $this, 'render' ) );
		add_shortcode( 'pf_faq', array( $this, 'render' ) );
	}

	/**
	 * Render.
	 *
	 * @param array $atts Atributos.
	 * @return string
	 */
	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'categoria'       => '',
				'busca'           => '1',
				'categorias'      => '1',
				'abrir_primeira'  => '0',
				'titulo'          => '',
				'subtitulo'       => '',
			),
			$atts,
			'perguntas_frequentes'
		);

		return PF_Frontend::render(
			array(
				'category'        => $atts['categoria'],
				'show_search'     => (bool) (int) $atts['busca'],
				'show_categories' => (bool) (int) $atts['categorias'],
				'open_first'      => (bool) (int) $atts['abrir_primeira'],
				'heading'         => $atts['titulo'] ? $atts['titulo'] : __( 'Como podemos ajudar?', 'perguntas-frequentes' ),
				'subtitle'        => $atts['subtitulo'] ? $atts['subtitulo'] : __( 'Busque por palavra-chave ou escolha um tema para encontrar a resposta mais rápido.', 'perguntas-frequentes' ),
			)
		);
	}
}
