/**
 * Returns - progressive enhancement for the typed request form.
 *
 * Filters the reason list to the chosen request type and shows the preferred
 * remedy field only for complaint and repair requests. The form is fully usable
 * without this script: the server validates the reason against the chosen type
 * and only requires a remedy when the type calls for one.
 */
(function () {
	'use strict';

	var form = document.querySelector('.returns-form');

	if (!form) {
		return;
	}

	var typeInputs = form.querySelectorAll('input[name="returns_type"]');
	var reason = form.querySelector('#returns-reason');
	var remedy = form.querySelector('[data-returns-remedy]');

	if (!typeInputs.length) {
		return;
	}

	function currentType() {
		for (var i = 0; i < typeInputs.length; i++) {
			if (typeInputs[i].checked) {
				return typeInputs[i].value;
			}
		}

		return '';
	}

	function apply() {
		var type = currentType();

		if (reason) {
			var groups = reason.querySelectorAll('optgroup');

			for (var i = 0; i < groups.length; i++) {
				var match = groups[i].getAttribute('data-type') === type;
				groups[i].hidden = !match;
				groups[i].disabled = !match;

				var options = groups[i].querySelectorAll('option');
				for (var j = 0; j < options.length; j++) {
					options[j].hidden = !match;
					options[j].disabled = !match;
				}
			}

			// Clear a stale selection that belongs to another type.
			var selected = reason.options[reason.selectedIndex];
			if (selected && selected.value && selected.getAttribute('data-type') !== type) {
				reason.value = '';
			}
		}

		if (remedy) {
			var needs = type === 'complaint' || type === 'repair';
			remedy.hidden = !needs;

			var select = remedy.querySelector('select');
			if (select) {
				select.disabled = !needs;
				if (!needs) {
					select.value = '';
				}
			}
		}
	}

	for (var i = 0; i < typeInputs.length; i++) {
		typeInputs[i].addEventListener('change', apply);
	}

	apply();
})();
