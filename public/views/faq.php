<?php
/**
 * View pública da FAQ.
 *
 * @package PerguntasFrequentes
 *
 * @var array $args
 * @var array $items
 * @var WP_Term[] $categories
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$uid            = esc_attr( $args['uid'] );
$show_search    = ! empty( $args['show_search'] );
$show_cats      = ! empty( $args['show_categories'] );
$open_first     = ! empty( $args['open_first'] );
$extra_class    = sanitize_html_class( $args['extra_class'] );
$count_by_slug  = array();
foreach ( $items as $it ) {
	$slug = $it['cat_slug'];
	if ( ! $slug ) {
		continue;
	}
	if ( ! isset( $count_by_slug[ $slug ] ) ) {
		$count_by_slug[ $slug ] = 0;
	}
	++$count_by_slug[ $slug ];
}
$total = count( $items );
?>
<section
	class="pf-faq <?php echo esc_attr( $extra_class ); ?>"
	id="<?php echo $uid; ?>"
	data-pf-faq
	data-open-first="<?php echo $open_first ? '1' : '0'; ?>"
	aria-labelledby="<?php echo $uid; ?>-heading"
>
	<div class="pf-faq__intro">
		<div class="pf-faq__intro-text">
			<?php if ( ! empty( $args['eyebrow'] ) ) : ?>
				<p class="pf-faq__eyebrow"><?php echo esc_html( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $args['heading'] ) ) : ?>
				<h2 id="<?php echo $uid; ?>-heading" class="pf-faq__heading">
					<?php echo esc_html( $args['heading'] ); ?>
				</h2>
			<?php endif; ?>
			<?php if ( ! empty( $args['subtitle'] ) ) : ?>
				<p class="pf-faq__subtitle"><?php echo esc_html( $args['subtitle'] ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $show_search ) : ?>
			<label class="pf-faq__search">
				<span class="screen-reader-text"><?php esc_html_e( 'Buscar na FAQ', 'perguntas-frequentes' ); ?></span>
				<span class="pf-faq__search-icon" aria-hidden="true">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
				</span>
				<input
					type="search"
					class="pf-faq__search-input"
					data-pf-search
					placeholder="<?php esc_attr_e( 'Buscar por dúvida, produto, pedido…', 'perguntas-frequentes' ); ?>"
					autocomplete="off"
				/>
				<button type="button" class="pf-faq__search-clear" data-pf-clear hidden aria-label="<?php esc_attr_e( 'Limpar busca', 'perguntas-frequentes' ); ?>">
					<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
				</button>
			</label>
		<?php endif; ?>
	</div>

	<div class="pf-faq__layout<?php echo $show_cats ? ' pf-faq__layout--with-cats' : ''; ?>">
		<div class="pf-faq__main">
			<div class="pf-faq__panel">
				<div class="pf-faq__panel-head">
					<div>
						<p class="pf-faq__panel-label"><?php esc_html_e( 'Perguntas e respostas', 'perguntas-frequentes' ); ?></p>
						<h3 class="pf-faq__panel-title" data-pf-panel-title>
							<?php esc_html_e( 'Todas as perguntas', 'perguntas-frequentes' ); ?>
						</h3>
					</div>
					<p class="pf-faq__panel-count" data-pf-count>
						<?php
						echo esc_html(
							sprintf(
								/* translators: %d: quantidade */
								_n( '%d resultado', '%d resultados', $total, 'perguntas-frequentes' ),
								$total
							)
						);
						?>
					</p>
				</div>

				<div class="pf-faq__empty" data-pf-empty hidden>
					<p class="pf-faq__empty-title"><?php esc_html_e( 'Nenhuma dúvida encontrada', 'perguntas-frequentes' ); ?></p>
					<p class="pf-faq__empty-text"><?php esc_html_e( 'Tente outro termo ou limpe os filtros.', 'perguntas-frequentes' ); ?></p>
					<button type="button" class="pf-faq__empty-btn" data-pf-reset>
						<?php esc_html_e( 'Limpar filtros', 'perguntas-frequentes' ); ?>
					</button>
				</div>

				<div class="pf-faq__list" data-pf-list>
					<?php foreach ( $items as $index => $item ) : ?>
						<?php
						$item_id   = $uid . '-item-' . (int) $item['id'];
						$is_open   = $open_first && 0 === $index;
						$search_blob = PF_Query::normalize_search(
							$item['pergunta'] . ' ' . $item['resposta_plain'] . ' ' . $item['categoria']
						);
						?>
						<details
							class="pf-faq__item"
							data-pf-item
							data-cat="<?php echo esc_attr( $item['cat_slug'] ); ?>"
							data-search="<?php echo esc_attr( $search_blob ); ?>"
							<?php echo $is_open ? ' open' : ''; ?>
						>
							<summary class="pf-faq__question">
								<?php if ( ! empty( $item['categoria'] ) ) : ?>
									<span class="pf-faq__cat-tag" data-pf-cat-tag><?php echo esc_html( $item['categoria'] ); ?></span>
								<?php endif; ?>
								<span class="pf-faq__question-text"><?php echo esc_html( $item['pergunta'] ); ?></span>
								<span class="pf-faq__chevron" aria-hidden="true"></span>
							</summary>
							<div class="pf-faq__answer" id="<?php echo esc_attr( $item_id ); ?>">
								<?php echo $item['resposta']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — já filtrado por the_content ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php if ( $show_cats && ! empty( $categories ) ) : ?>
			<aside class="pf-faq__aside">
				<nav class="pf-faq__cats" aria-label="<?php esc_attr_e( 'Categorias da FAQ', 'perguntas-frequentes' ); ?>">
					<p class="pf-faq__cats-label"><?php esc_html_e( 'Categorias', 'perguntas-frequentes' ); ?></p>
					<ul class="pf-faq__cats-list">
						<li>
							<button
								type="button"
								class="pf-faq__cat is-active"
								data-pf-cat=""
								aria-current="true"
							>
								<span><?php esc_html_e( 'Todas', 'perguntas-frequentes' ); ?></span>
								<span class="pf-faq__cat-count" data-pf-cat-count=""><?php echo esc_html( (string) $total ); ?></span>
							</button>
						</li>
						<?php foreach ( $categories as $term ) : ?>
							<?php
							$qtd = isset( $count_by_slug[ $term->slug ] ) ? (int) $count_by_slug[ $term->slug ] : 0;
							?>
							<li>
								<button
									type="button"
									class="pf-faq__cat"
									data-pf-cat="<?php echo esc_attr( $term->slug ); ?>"
									data-pf-cat-name="<?php echo esc_attr( $term->name ); ?>"
								>
									<span><?php echo esc_html( $term->name ); ?></span>
									<span class="pf-faq__cat-count" data-pf-cat-count="<?php echo esc_attr( $term->slug ); ?>">
										<?php echo esc_html( (string) $qtd ); ?>
									</span>
								</button>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			</aside>
		<?php endif; ?>
	</div>

	<script type="application/ld+json"><?php
		$entities = array();
	foreach ( $items as $item ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => wp_strip_all_tags( $item['pergunta'] ),
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => wp_strip_all_tags( $item['resposta_plain'] ),
			),
		);
	}
		echo wp_json_encode(
			array(
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
		);
	?></script>
</section>
