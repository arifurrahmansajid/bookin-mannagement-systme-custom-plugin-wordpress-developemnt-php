<div class="wrap">
	<h1 class="wp-heading-inline">Booking Requests</h1>
	<a href="<?php echo esc_url( admin_url( 'admin-post.php?action=cbm_export_csv' ) ); ?>" class="page-title-action">Export to CSV</a>
	
	<hr class="wp-header-end">

	<form method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>" />
		<?php $list_table->display(); ?>
	</form>
</div>
