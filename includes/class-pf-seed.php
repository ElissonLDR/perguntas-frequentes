<?php
/**
 * Seed do conteúdo inicial (Lovable).
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Seed
 */
class PF_Seed {

	const OPTION_SEEDED = 'pf_faq_seeded';

	/**
	 * Importa FAQ se ainda não houver itens.
	 *
	 * @param bool $force Força reimportação (não apaga existentes).
	 */
	public static function maybe_seed( $force = false ) {
		if ( ! $force && get_option( self::OPTION_SEEDED ) ) {
			return;
		}

		$existing = wp_count_posts( PF_CPT::POST_TYPE );
		$published = $existing && isset( $existing->publish ) ? (int) $existing->publish : 0;
		if ( $published > 0 && ! $force ) {
			update_option( self::OPTION_SEEDED, 1 );
			return;
		}

		$file = PF_FAQ_PATH . 'data/seed-faq.json';
		if ( ! file_exists( $file ) ) {
			return;
		}

		$raw  = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data = json_decode( (string) $raw, true );
		if ( ! is_array( $data ) || empty( $data['items'] ) ) {
			return;
		}

		$order_by_cat = array();
		$menu_order   = 0;

		$categorias = isset( $data['categorias'] ) && is_array( $data['categorias'] )
			? $data['categorias']
			: array();

		foreach ( $categorias as $i => $nome ) {
			$nome = sanitize_text_field( (string) $nome );
			if ( '' === $nome ) {
				continue;
			}
			if ( ! term_exists( $nome, PF_CPT::TAXONOMY ) ) {
				wp_insert_term(
					$nome,
					PF_CPT::TAXONOMY,
					array(
						'slug' => sanitize_title( $nome ),
					)
				);
			}
			$term = get_term_by( 'name', $nome, PF_CPT::TAXONOMY );
			if ( $term && ! is_wp_error( $term ) ) {
				wp_update_term(
					(int) $term->term_id,
					PF_CPT::TAXONOMY,
					array(
						'description' => '',
					)
				);
				update_term_meta( (int) $term->term_id, 'pf_faq_order', (int) $i );
			}
		}

		foreach ( $data['items'] as $item ) {
			$pergunta  = isset( $item['pergunta'] ) ? sanitize_text_field( (string) $item['pergunta'] ) : '';
			$resposta  = isset( $item['resposta'] ) ? wp_kses_post( (string) $item['resposta'] ) : '';
			$categoria = isset( $item['categoria'] ) ? sanitize_text_field( (string) $item['categoria'] ) : '';

			if ( '' === $pergunta || '' === $resposta ) {
				continue;
			}

			// Evita duplicar pelo título.
			global $wpdb;
			$dup_id = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_title = %s AND post_status != 'trash' LIMIT 1",
					PF_CPT::POST_TYPE,
					$pergunta
				)
			);
			if ( $dup_id ) {
				continue;
			}

			if ( $categoria && ! isset( $order_by_cat[ $categoria ] ) ) {
				$order_by_cat[ $categoria ] = 0;
			}
			++$menu_order;

			$post_id = wp_insert_post(
				array(
					'post_type'    => PF_CPT::POST_TYPE,
					'post_status'  => 'publish',
					'post_title'   => $pergunta,
					'post_content' => $resposta,
					'menu_order'   => $menu_order,
				),
				true
			);

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			if ( $categoria ) {
				wp_set_object_terms( (int) $post_id, $categoria, PF_CPT::TAXONOMY, false );
			}
		}

		update_option( self::OPTION_SEEDED, 1 );
	}
}
