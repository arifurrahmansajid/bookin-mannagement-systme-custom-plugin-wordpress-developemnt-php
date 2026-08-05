<style>
#cbm-form-builder-container { display: flex; gap: 20px; background: #f1f1f1; padding: 20px; border: 1px solid #ccd0d4; margin-top: 15px; }
.cbm-builder-sidebar { width: 250px; background: #fff; padding: 15px; border: 1px solid #ddd; }
.cbm-builder-sidebar h4 { margin-top: 0; }
.cbm-field-btn { display: block; width: 100%; padding: 8px 12px; margin-bottom: 10px; background: #f8f9fa; border: 1px solid #ccc; cursor: grab; text-align: left; }
.cbm-field-btn:hover { background: #e9ecef; }
.cbm-builder-canvas { flex-grow: 1; background: #fff; padding: 20px; border: 2px dashed #bbb; min-height: 400px; }
.cbm-builder-canvas.ui-sortable-hover { background: #fcfcfc; border-color: #3498db; }
.cbm-field-item { background: #fafafa; border: 1px solid #eee; padding: 15px; margin-bottom: 15px; position: relative; cursor: move; }
.cbm-field-item-header { font-weight: bold; margin-bottom: 10px; display: flex; justify-content: space-between; }
.cbm-field-remove { color: red; cursor: pointer; }
.cbm-field-settings label { display: block; margin-bottom: 5px; }
.cbm-field-settings input[type="text"] { width: 100%; margin-bottom: 10px; }
</style>

<div id="cbm-form-builder-container">
	<div class="cbm-builder-sidebar">
		<h4>Add Fields</h4>
		<p class="description">Click a field to add it to your form.</p>
		<button type="button" class="cbm-field-btn" data-type="text">Text (Name, etc.)</button>
		<button type="button" class="cbm-field-btn" data-type="email">Email</button>
		<button type="button" class="cbm-field-btn" data-type="tel">Phone</button>
		<button type="button" class="cbm-field-btn" data-type="date">Date</button>
		<button type="button" class="cbm-field-btn" data-type="time">Time</button>
		<button type="button" class="cbm-field-btn" data-type="textarea">Textarea (Details)</button>
	</div>

	<div class="cbm-builder-canvas" id="cbm-builder-canvas">
		<!-- Dynamic fields go here -->
	</div>
</div>

<input type="hidden" name="cbm_form_schema" id="cbm_form_schema" value="<?php echo esc_attr( $schema ); ?>">
