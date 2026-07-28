<?php
/**
 * Frontend: assets + render.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Frontend
 */
class PF_Frontend {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	/**
	 * Registra assets.
	 */
	public function register_assets() {
		wp_register_style(
			'pf-faq-front',
			PF_FAQ_URL . 'public/css/faq.css',
			array(),
			PF_FAQ_VERSION
		);
		wp_register_script(
			'pf-faq-front',
			PF_FAQ_URL . 'public/js/faq.js',
			array(),
			PF_FAQ_VERSION,
			true
		);
	}

	/**
	 * Enfileira.
	 */
	public function enqueue_assets() {
		if ( ! wp_style_is( 'pf-faq-front', 'registered' ) ) {
			$this->register_assets();
		}
		wp_enqueue_style( 'pf-faq-front' );
		wp_enqueue_script( 'pf-faq-front' );
	}

	/**
	 * Renderiza a FAQ.
	 *
	 * @param array $args Args.
	 * @return string
	 */
	public static function render( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'category'       => '',
				'show_search'    => true,
				'show_categories'=> true,
				'open_first'     => false,
				'heading'        => __( 'Como podemos ajudar?', 'perguntas-frequentes' ),
				'subtitle'       => __( 'Busque por palavra-chave ou escolha um tema para encontrar a resposta mais rápido.', 'perguntas-frequentes' ),
				'eyebrow'        => __( 'FAQ', 'perguntas-frequentes' ),
				'extra_class'    => '',
				'uid'            => 'pf-faq-' . wp_unique_id(),
			)
		);

		$posts = PF_Query::get_items(
			array(
				'category' => $args['category'],
			)
		);

		if ( empty( $posts ) ) {
			return '';
		}

		$categories = PF_Query::get_categories();
		$items      = array();

		foreach ( $posts as $post ) {
			$terms = get_the_terms( $post->ID, PF_CPT::TAXONOMY );
			$cat_name = '';
			$cat_slug = '';
			if ( $terms && ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$cat_name = $terms[0]->name;
				$cat_slug = $terms[0]->slug;
			}

			$items[] = array(
				'id'       => (int) $post->ID,
				'pergunta' => get_the_title( $post ),
				'resposta' => apply_filters( 'the_content', $post->post_content ),
				'resposta_plain' => wp_strip_all_tags( $post->post_content ),
				'categoria'=> $cat_name,
				'cat_slug' => $cat_slug,
			);
		}

		$front = new self();
		$front->enqueue_assets();

		ob_start();
		include PF_FAQ_PATH . 'public/views/faq.php';
		return (string) ob_get_clean();
	}
}
