<?php
/**
 * CPT e taxonomia.
 *
 * @package PerguntasFrequentes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classe PF_CPT
 */
class PF_CPT {

	const POST_TYPE = 'pf_faq';
	const TAXONOMY  = 'pf_faq_categoria';

	/**
	 * Registra CPT e taxonomia.
	 */
	public static function register() {
		$labels = array(
			'name'               => __( 'Perguntas Frequentes', 'perguntas-frequentes' ),
			'singular_name'      => __( 'Pergunta', 'perguntas-frequentes' ),
			'add_new'            => __( 'Adicionar nova', 'perguntas-frequentes' ),
			'add_new_item'       => __( 'Adicionar pergunta', 'perguntas-frequentes' ),
			'edit_item'          => __( 'Editar pergunta', 'perguntas-frequentes' ),
			'new_item'           => __( 'Nova pergunta', 'perguntas-frequentes' ),
			'view_item'          => __( 'Ver pergunta', 'perguntas-frequentes' ),
			'search_items'       => __( 'Buscar perguntas', 'perguntas-frequentes' ),
			'not_found'          => __( 'Nenhuma pergunta encontrada', 'perguntas-frequentes' ),
			'not_found_in_trash' => __( 'Nenhuma na lixeira', 'perguntas-frequentes' ),
			'menu_name'          => __( 'FAQ', 'perguntas-frequentes' ),
			'all_items'          => __( 'Todas as perguntas', 'perguntas-frequentes' ),
		);

		register_post_type(
			self::POST_TYPE,
			array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 25,
				'menu_icon'           => 'dashicons-editor-help',
				'capability_type'     => 'post',
				'hierarchical'        => false,
				'supports'            => array( 'title', 'editor', 'page-attributes' ),
				'has_archive'         => false,
				'rewrite'             => false,
				'query_var'           => false,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
			)
		);

		$tax_labels = array(
			'name'          => __( 'Categorias da FAQ', 'perguntas-frequentes' ),
			'singular_name' => __( 'Categoria', 'perguntas-frequentes' ),
			'search_items'  => __( 'Buscar categorias', 'perguntas-frequentes' ),
			'all_items'     => __( 'Todas as categorias', 'perguntas-frequentes' ),
			'edit_item'     => __( 'Editar categoria', 'perguntas-frequentes' ),
			'update_item'   => __( 'Atualizar categoria', 'perguntas-frequentes' ),
			'add_new_item'  => __( 'Adicionar categoria', 'perguntas-frequentes' ),
			'new_item_name' => __( 'Nome da categoria', 'perguntas-frequentes' ),
			'menu_name'     => __( 'Categorias', 'perguntas-frequentes' ),
		);

		register_taxonomy(
			self::TAXONOMY,
			array( self::POST_TYPE ),
			array(
				'labels'            => $tax_labels,
				'hierarchical'      => true,
				'public'            => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => false,
				'query_var'         => false,
			)
		);
	}
}
