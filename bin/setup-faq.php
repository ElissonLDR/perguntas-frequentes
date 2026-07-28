<?php
/**
 * Pós-deploy: ativa plugin, seed e garante shortcode na página FAQ.
 * Uso remoto: php wp-content/plugins/perguntas-frequentes/bin/setup-faq.php
 *
 * @package PerguntasFrequentes
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only' );
}

$root = dirname( __DIR__, 4 ); // .../public_html
if ( ! file_exists( $root . '/wp-load.php' ) ) {
	$root = dirname( __DIR__, 3 ); // fallback local plugins parent
	if ( ! file_exists( $root . '/wp-load.php' ) ) {
		fwrite( STDERR, "wp-load.php não encontrado\n" );
		exit( 1 );
	}
}

require $root . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$plugin = 'perguntas-frequentes/perguntas-frequentes.php';

if ( ! is_plugin_active( $plugin ) ) {
	$result = activate_plugin( $plugin );
	if ( is_wp_error( $result ) ) {
		fwrite( STDERR, 'Erro ao ativar: ' . $result->get_error_message() . "\n" );
		exit( 1 );
	}
	echo "Plugin ativado.\n";
} else {
	echo "Plugin já ativo.\n";
}

if ( class_exists( 'PF_CPT' ) ) {
	PF_CPT::register();
}
if ( class_exists( 'PF_Seed' ) ) {
	PF_Seed::maybe_seed();
}

$counts = wp_count_posts( 'pf_faq' );
$published = $counts && isset( $counts->publish ) ? (int) $counts->publish : 0;
echo "Perguntas: {$published}\n";

$pages = get_posts(
	array(
		'name'        => 'faq',
		'post_type'   => 'page',
		'post_status' => 'any',
		'numberposts' => 1,
	)
);

$shortcode = '[perguntas_frequentes]';

if ( ! $pages ) {
	$id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'FAQ',
			'post_name'    => 'faq',
			'post_content' => $shortcode,
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		fwrite( STDERR, $id->get_error_message() . "\n" );
		exit( 1 );
	}
	echo "Página FAQ criada: {$id}\n";
} else {
	$page = $pages[0];
	$has_sc = ( false !== strpos( $page->post_content, 'perguntas_frequentes' ) )
		|| ( false !== strpos( $page->post_content, 'pf_faq' ) );

	$is_elementor = 'builder' === get_post_meta( $page->ID, '_elementor_edit_mode', true );

	if ( $is_elementor && ! $has_sc ) {
		// Substitui o conteúdo Elementor vazio/simples pelo shortcode clássico.
		delete_post_meta( $page->ID, '_elementor_edit_mode' );
		delete_post_meta( $page->ID, '_elementor_data' );
		delete_post_meta( $page->ID, '_elementor_template_type' );
		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => $shortcode,
				'post_status'  => 'publish',
			)
		);
		echo "Página FAQ (#{$page->ID}): Elementor desativado, shortcode aplicado.\n";
	} elseif ( ! $has_sc ) {
		$content = trim( $page->post_content );
		$new     = '' === $content ? $shortcode : $content . "\n\n" . $shortcode;
		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => $new,
				'post_status'  => 'publish',
			)
		);
		echo "Página FAQ (#{$page->ID}): shortcode adicionado.\n";
	} else {
		if ( 'publish' !== $page->post_status ) {
			wp_update_post(
				array(
					'ID'          => $page->ID,
					'post_status' => 'publish',
				)
			);
		}
		echo "Página FAQ (#{$page->ID}): shortcode já presente.\n";
	}
}

echo "Setup FAQ concluído.\n";
