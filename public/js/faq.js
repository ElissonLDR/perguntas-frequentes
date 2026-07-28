/**
 * FAQ front interactions: busca, categorias, acordeão.
 */
(function () {
	'use strict';

	function normalize(text) {
		return String(text || '')
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '')
			.toLowerCase()
			.trim();
	}

	function initRoot(root) {
		if (root.getAttribute('data-pf-ready')) {
			return;
		}
		root.setAttribute('data-pf-ready', '1');

		var searchInput = root.querySelector('[data-pf-search]');
		var clearBtn = root.querySelector('[data-pf-clear]');
		var resetBtn = root.querySelector('[data-pf-reset]');
		var emptyEl = root.querySelector('[data-pf-empty]');
		var listEl = root.querySelector('[data-pf-list]');
		var titleEl = root.querySelector('[data-pf-panel-title]');
		var countEl = root.querySelector('[data-pf-count]');
		var items = Array.prototype.slice.call(root.querySelectorAll('[data-pf-item]'));
		var catButtons = Array.prototype.slice.call(root.querySelectorAll('[data-pf-cat]'));
		var activeCat = '';
		var query = '';

		function labelResultado(n) {
			return n === 1 ? n + ' resultado' : n + ' resultados';
		}

		function updateCatCounts(visibleItems) {
			var map = {};
			var total = 0;
			visibleItems.forEach(function (item) {
				var slug = item.getAttribute('data-cat') || '';
				total += 1;
				if (!slug) return;
				map[slug] = (map[slug] || 0) + 1;
			});

			root.querySelectorAll('[data-pf-cat-count]').forEach(function (el) {
				var slug = el.getAttribute('data-pf-cat-count') || '';
				el.textContent = String(slug === '' ? total : map[slug] || 0);
			});
		}

		function apply() {
			var q = normalize(query);
			var visibleForCount = [];
			var visibleInList = 0;

			items.forEach(function (item) {
				var cat = item.getAttribute('data-cat') || '';
				var blob = item.getAttribute('data-search') || '';
				var matchQ = !q || blob.indexOf(q) !== -1;
				var matchCat = !activeCat || cat === activeCat;

				if (matchQ) {
					visibleForCount.push(item);
				}

				var show = matchQ && matchCat;
				item.hidden = !show;
				if (show) {
					visibleInList += 1;
				} else if (item.open) {
					item.open = false;
				}
			});

			updateCatCounts(visibleForCount);

			if (emptyEl) {
				emptyEl.hidden = visibleInList > 0;
			}
			if (listEl) {
				listEl.hidden = visibleInList === 0;
			}

			if (countEl) {
				var suffix = query ? ' para “' + query + '”' : '';
				countEl.textContent = labelResultado(visibleInList) + suffix;
			}

			if (titleEl) {
				if (!activeCat) {
					titleEl.textContent = 'Todas as perguntas';
				} else {
					var btn = root.querySelector('[data-pf-cat="' + activeCat + '"]');
					titleEl.textContent = btn
						? btn.getAttribute('data-pf-cat-name') || activeCat
						: activeCat;
				}
			}

			root.classList.toggle('is-cat-filtered', !!activeCat);

			if (clearBtn) {
				clearBtn.hidden = !query;
			}
		}

		function setCat(slug) {
			activeCat = slug || '';
			catButtons.forEach(function (btn) {
				var isActive = (btn.getAttribute('data-pf-cat') || '') === activeCat;
				btn.classList.toggle('is-active', isActive);
				if (isActive) {
					btn.setAttribute('aria-current', 'true');
				} else {
					btn.removeAttribute('aria-current');
				}
			});
			apply();
		}

		catButtons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				setCat(btn.getAttribute('data-pf-cat') || '');
			});
		});

		if (searchInput) {
			searchInput.addEventListener('input', function () {
				query = searchInput.value || '';
				apply();
			});
		}

		if (clearBtn) {
			clearBtn.addEventListener('click', function () {
				query = '';
				if (searchInput) {
					searchInput.value = '';
					searchInput.focus();
				}
				apply();
			});
		}

		if (resetBtn) {
			resetBtn.addEventListener('click', function () {
				query = '';
				if (searchInput) {
					searchInput.value = '';
				}
				setCat('');
			});
		}

		// Accordion: um aberto por vez.
		items.forEach(function (item) {
			item.addEventListener('toggle', function () {
				if (!item.open) return;
				items.forEach(function (other) {
					if (other !== item && other.open) {
						other.open = false;
					}
				});
			});
		});

		apply();
	}

	function boot() {
		document.querySelectorAll('[data-pf-faq]').forEach(initRoot);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	// Elementor preview / AJAX.
	if (window.elementorFrontend && window.elementorFrontend.hooks) {
		window.elementorFrontend.hooks.addAction('frontend/element_ready/pf-faq.default', boot);
	}
})();
