jQuery(document).ready(function($) {
	var $canvas = $('#cbm-builder-canvas');
	var $schemaInput = $('#cbm_form_schema');

	// Initialize sortable
	$canvas.sortable({
		placeholder: "ui-state-highlight",
		update: function() {
			updateSchema();
		}
	});

	// Load existing schema
	try {
		var schema = JSON.parse($schemaInput.val());
		if (Array.isArray(schema)) {
			schema.forEach(function(field) {
				addFieldToCanvas(field.type, field.label, field.name, field.required);
			});
		}
	} catch (e) {
		// Schema is empty or invalid
	}

	// Add new field
	$('.cbm-field-btn').on('click', function() {
		var type = $(this).data('type');
		var defaultLabel = $(this).text();
		var randomName = 'field_' + Math.random().toString(36).substr(2, 9);
		addFieldToCanvas(type, defaultLabel, randomName, false);
		updateSchema();
	});

	// Remove field
	$canvas.on('click', '.cbm-field-remove', function() {
		$(this).closest('.cbm-field-item').remove();
		updateSchema();
	});

	// Input changes trigger schema update
	$canvas.on('input change', 'input', function() {
		updateSchema();
	});

	function addFieldToCanvas(type, label, name, required) {
		var html = `
			<div class="cbm-field-item" data-type="${type}" data-name="${name}">
				<div class="cbm-field-item-header">
					<span>Type: ${type.toUpperCase()}</span>
					<span class="cbm-field-remove">Remove &times;</span>
				</div>
				<div class="cbm-field-settings">
					<label>Field Label</label>
					<input type="text" class="cbm-field-label" value="${label}">
					<label>
						<input type="checkbox" class="cbm-field-required" ${required ? 'checked' : ''}> Required Field
					</label>
				</div>
			</div>
		`;
		$canvas.append(html);
	}

	function updateSchema() {
		var fields = [];
		$canvas.find('.cbm-field-item').each(function() {
			var $item = $(this);
			fields.push({
				type: $item.data('type'),
				name: $item.data('name'),
				label: $item.find('.cbm-field-label').val(),
				required: $item.find('.cbm-field-required').is(':checked')
			});
		});
		$schemaInput.val(JSON.stringify(fields));
	}
	
	// Hook into post submit to ensure latest schema is saved
	$('#post').on('submit', function() {
		updateSchema();
	});
});
