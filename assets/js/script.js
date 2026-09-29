// dashboard interactions - tab toggles + product filter + favourites scroll
jQuery(function ($) {

	var $root = $('.buymore');
	if (!$root.length) {
		return;
	}

	function setActive($buttons, $target) {
		$buttons.removeClass('is-active').attr('aria-selected', 'false');
		$target.addClass('is-active').attr('aria-selected', 'true');
	}


	var $headerToggle = $root.find('.buymore-header__toggle button');
	$headerToggle.on('click', function () {
		setActive($headerToggle, $(this));
	});

	// All / Men / Women product filter
	var $filterButtons = $root.find('.buymore-filters button');
	var $products = $root.find('.buymore-spotlight[data-cat]');

	$filterButtons.on('click', function () {
		var $btn = $(this);
		setActive($filterButtons, $btn);

		var filter = $btn.data('filter');

		$products.each(function () {
			var $product = $(this);
			if (!filter) {
				$product.prop('hidden', false);
				return;
			}
			var cats = ($product.data('cat') || '').toString().split(' ');
			$product.prop('hidden', cats.indexOf(filter) === -1);
		});
	});

	// Favourites carousel: pages through all favourite products 2 at a time.
	var $favNav    = $root.find('.buymore-favourites__nav');
	var $favThumbs = $root.find('.buymore-favourites__thumb');
	var favTotal   = $favThumbs.length;
	var favStart   = 0;

	function showFavPair() {
		var second = (favStart + 1) % favTotal;
		$favThumbs.each(function (i) {
			$(this).prop('hidden', i !== favStart && i !== second);
		});
	}

	if ($favNav.length && favTotal > 2) {
		$favNav.find('button').each(function (index) {
			$(this).on('click', function () {
				var direction = index === 0 ? -1 : 1;
				favStart = (favStart + direction + favTotal) % favTotal;
				showFavPair();
			});
		});
	} else if ($favNav.length) {
		$favNav.find('button').prop('disabled', true).css('opacity', 0.35);
	}

});
