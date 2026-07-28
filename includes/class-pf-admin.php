<?php
/**
 * Admin: labels amigáveis.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Admin
 */
class PF_Admin {

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'enter_title_here', array( $this, 'title_placeholder' ), 10, 2 );
		add_action( 'edit_form_after_title', array( $this, 'editor_hint' ) );
		add_filter( 'manage_' . PF_CPT::POST_TYPE . '_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_' . PF_CPT::POST_TYPE . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Placeholder do título.
	 *
	 * @param string  $text Placeholder.
	 * @param WP_Post $post Post.
	 * @return string
	 */
	public function title_placeholder( $text, $post ) {
		if ( $post && PF_CPT::POST_TYPE === $post->post_type ) {
			return __( 'Digite a pergunta aqui…', 'perguntas-frequentes' );
		}
		return $text;
	}

	/**
	 * Dica abaixo do título.
	 *
	 * @param WP_Post $post Post.
	 */
	public function editor_hint( $post ) {
		if ( ! $post || PF_CPT::POST_TYPE !== $post->post_type ) {
			return;
		}
		echo '<p class="description" style="margin:8px 0 12px;">';
		esc_html_e( 'No campo abaixo, escreva a resposta. Depois escolha a categoria na caixa à direita e publique.', 'perguntas-frequentes' );
		echo '</p>';
	}

	/**
	 * Colunas.
	 *
	 * @param array $cols Colunas.
	 * @return array
	 */
	public function columns( $cols ) {
		$new = array();
		foreach ( $cols as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['pf_faq_ordem'] = __( 'Ordem', 'perguntas-frequentes' );
			}
		}
		return $new;
	}

	/**
	 * Conteúdo das colunas.
	 *
	 * @param string $col Coluna.
	 * @param int    $post_id ID.
	 */
	public function column_content( $col, $post_id ) {
		if ( 'pf_faq_ordem' === $col ) {
			$post = get_post( $post_id );
			echo esc_html( $post ? (string) (int) $post->menu_order : '0' );
		}
	}

	/**
	 * CSS mínimo no admin do CPT.
	 *
	 * @param string $hook Hook.
	 */
	public function enqueue( $hook ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || PF_CPT::POST_TYPE !== $screen->post_type ) {
			return;
		}
		wp_enqueue_style(
			'pf-faq-admin',
			PF_FAQ_URL . 'admin/css/admin.css',
			array(),
			PF_FAQ_VERSION
		);
	}
}
