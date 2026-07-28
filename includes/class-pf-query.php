<?php
/**
 * Consultas da FAQ.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_Query
 */
class PF_Query {

	/**
	 * Lista categorias ordenadas.
	 *
	 * @return WP_Term[]
	 */
	public static function get_categories() {
		$terms = get_terms(
			array(
				'taxonomy'   => PF_CPT::TAXONOMY,
				'hide_empty' => true,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		usort(
			$terms,
			static function ( $a, $b ) {
				$oa = (int) get_term_meta( $a->term_id, 'pf_faq_order', true );
				$ob = (int) get_term_meta( $b->term_id, 'pf_faq_order', true );
				if ( $oa === $ob ) {
					return strcasecmp( $a->name, $b->name );
				}
				return $oa <=> $ob;
			}
		);

		return $terms;
	}

	/**
	 * Lista perguntas publicadas.
	 *
	 * @param array $args Args: category (slug), search, ids.
	 * @return WP_Post[]
	 */
	public static function get_items( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'category' => '',
				'search'   => '',
				'ids'      => array(),
			)
		);

		$query_args = array(
			'post_type'      => PF_CPT::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'  => true,
		);

		if ( ! empty( $args['ids'] ) && is_array( $args['ids'] ) ) {
			$query_args['post__in'] = array_map( 'absint', $args['ids'] );
			$query_args['orderby']  = 'post__in';
		}

		if ( ! empty( $args['category'] ) ) {
			$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => PF_CPT::TAXONOMY,
					'field'    => 'slug',
					'terms'    => sanitize_title( $args['category'] ),
				),
			);
		}

		if ( ! empty( $args['search'] ) ) {
			$query_args['s'] = sanitize_text_field( $args['search'] );
		}

		$q = new WP_Query( $query_args );
		return $q->posts;
	}

	/**
	 * Normaliza texto para busca no front (JS espelha isso).
	 *
	 * @param string $text Texto.
	 * @return string
	 */
	public static function normalize_search( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		if ( function_exists( 'remove_accents' ) ) {
			$text = remove_accents( $text );
		}
		return strtolower( trim( $text ) );
	}
}
