<?php
/**
 * Pós-deploy: ativa plugin e importa o seed da FAQ (não altera páginas).
 * Uso: php wp-content/plugins/perguntas-frequentes/bin/setup-faq.php
 *
 * @package PerguntasFrequentes
 */

if ( 'cli' !== PHP_SAPI ) {
	exit( 'CLI only' );
}

$root = dirname( __DIR__, 4 ); // .../public_html
if ( ! file_exists( $root . '/wp-load.php' ) ) {
	$root = dirname( __DIR__, 3 );
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

$counts    = wp_count_posts( 'pf_faq' );
$published = $counts && isset( $counts->publish ) ? (int) $counts->publish : 0;
echo "Perguntas: {$published}\n";
echo "Setup FAQ concluído (página não alterada).\n";
