<?php
$update = isset( $mailster_templates[ $slug ] ) && $mailster_templates[ $slug ]['update'] && current_user_can( 'mailster_update_templates' );
$licensecode = isset( $licensecodes[ $slug ] ) ? $licensecodes[ $slug ] : '';
$envato_item_id = isset( $mailster_templates[ $slug ]['envato_item_id'] ) ? $mailster_templates[ $slug ]['envato_item_id'] : null;

$is_free = isset( $mailster_templates[ $slug ] ) && isset( $mailster_templates[ $slug ]['is_free'] );

$class = array( 'mailster-box' );
if ( $update ) {
	$class[] = 'update';
}
if ( $default == $slug ) {
	$class[] = 'is-default';
}
if ( $new == $slug ) {
	$class[] = 'is-new';
}

?>
	<li class="<?php echo implode( ' ', $class ) ?>" id="template-<?php echo esc_attr( $slug ) ?>" name="mailster_template_<?php echo $i ?>" data-id="<?php echo $i++ ?>">
		<?php if ( isset( $updates[ $slug ] ) ) : ?>
			<span class="update-badge"><?php echo $updates[ $slug ] ?></span>
		<?php endif; ?>
		<div class="screenshot" style="background-image:url(<?php echo $t->get_screenshot( $slug ) ?>)" title="<?php echo esc_attr( $data['name'] . ' ' . $data['version'] ) ?> <?php esc_html_e( 'by', 'mailster' );?> <?php echo $data['author'] ?>">
			<a class="thickbox-preview" href="<?php echo esc_url( $t->url . '/' . $slug . '/index.html' ) ?>" data-slug="<?php echo esc_attr( $slug ) ?>"><?php esc_html_e( 'Preview', 'mailster' );?></a>
			<a class="" href="<?php echo admin_url( 'post-new.php?post_type=newsletter&template=' . $slug . '' ) ?>" data-slug="<?php echo esc_attr( $slug ) ?>"><?php esc_html_e( 'Start new Campaign', 'mailster' );?></a>
		</div>
		<div class="meta">
			<h3><?php echo esc_html( $data['name'] ) ?> <span class="version"><?php echo esc_html( $data['version'] ) ?></span>
				<?php if ( $update ) : ?>
					<?php if ( empty( $licensecode ) && ! $is_free ) : ?>

							<?php if ( $envato_item_id ) : ?>
								<a title="<?php esc_html_e( 'update via Envato', 'mailster' );?>" class="update envato-activate button button-primary button-small alignright" href="<?php echo add_query_arg( array( 'auth' => wp_create_nonce( 'envato-activate' ), 'item_id' => $mailster_templates[ $slug ]['envato_item_id'], 'slug' => $slug, 'returnto' => urlencode( admin_url( 'edit.php?post_type=newsletter&page=mailster_templates' ) ) ), $mailster_templates[ $slug ]['endpoint'] ) ?>" data-slug="<?php echo $slug ?>"><?php echo sprintf( __( 'Update to %s', 'mailster' ), $mailster_templates[ $slug ]['new_version'] ); ?></a>
							<?php else : ?>
								<a title="<?php esc_html_e( 'activate with licensecode', 'mailster' );?>" class="activate button button-primary button-small alignright" href="edit.php?post_type=newsletter&page=mailster_templates&action=license&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce( 'license-' . $slug ) ?>" data-license="<?php echo $licensecode ?>" data-slug="<?php echo $slug ?>"><?php esc_html_e( 'Activate', 'mailster' );?></a>
							<?php endif; ?>

						<?php else : ?>

							<a title="<?php esc_html_e( 'update template', 'mailster' );?>" class="update button button-primary button-small alignright" href="edit.php?post_type=newsletter&page=mailster_templates&action=update&template=<?php echo $slug ?>&_wpnonce=<?php echo wp_create_nonce( 'download-' . $slug ) ?>" data-license="<?php echo $licensecode ?>" data-slug="<?php echo $slug ?>"><?php echo sprintf( __( 'Update to %s', 'mailster' ), $mailster_templates[ $slug ]['new_version'] ); ?></a>

					<?php endif; ?>
				<?php endif; ?>
			</h3>
			<div>
				<?php esc_html_e( 'by', 'mailster' );?> <?php if ( ! empty( $data['author_uri'] ) ) : ?><a class="external" href="<?php echo esc_html( $data['author_uri'] ) ?>"><?php echo esc_html( $data['author'] )  ?></a><?php else : ?> <?php echo esc_html( $data['author'] ) ?><?php endif; ?>
			</div>
		</div>
		<?php if ( ! empty( $data['description'] ) ) : ?>
			<p class="description"><?php echo $data['description'] ?></p>
		<?php elseif ( ! empty( $mailster_templates[ $slug ]['description'] ) ) : ?>
			<p class="description"><?php echo $mailster_templates[ $slug ]['description'] ?></p>
		<?php endif; ?>
		<div class="licensecode">
			<form action="edit.php?post_type=newsletter&page=mailster_templates" method="get">
			<input type="hidden" name="post_type" value="newsletter">
			<input type="hidden" name="page" value="mailster_templates">
			<input type="hidden" name="more" value="1">
			<input type="hidden" name="action" value="license">
			<input type="hidden" name="template" value="<?php echo $slug ?>">
			<input type="text" name="license" class="widefat license" value="" placeholder="<?php esc_html_e( 'Enter Licensecode', 'mailster' ) ?>">
			<?php wp_nonce_field( 'license-' . $slug, '_wpnonce', false );?>
			<input type="submit" class="button save-license" value="<?php esc_html_e( 'save', 'mailster' ) ?>">
			</form>
		</div>
		<div class="action-links">
			<ul>
				<?php if ( $default != $slug ) : ?>
				<li><a title="Set &quot;<?php echo $data['name'] ?>&quot; as default" class="activatelink button" href="edit.php?post_type=newsletter&amp;page=mailster_templates&amp;action=activate&amp;template=<?php echo $slug ?>&amp;_wpnonce=<?php echo wp_create_nonce( 'activate-' . $slug ) ?>"><?php esc_html_e( 'Use as default', 'mailster' );?></a></li>
				<?php endif; ?>
			 	<?php if ( current_user_can( 'mailster_edit_templates' ) ) :
					$writeable = wp_is_writable( $t->path . '/' . $slug . '/index.html' ); ?>

					<li><a title="<?php echo esc_attr( sprintf( 'Edit %s', '"' . $data['name'] . '"' ) ) ?>" class="edit <?php echo ! $writeable ? 'disabled' : '' ?> button" data-slug="<?php echo esc_attr( $slug ) ?>" href="<?php echo $slug . '/index.html' ?>" <?php if ( ! $writeable ) : ?>onclick="alert('<?php esc_html_e( 'This file is not writeable! Please change the file permission', 'mailster' );?>');return false;"<?php endif; ?>><?php esc_html_e( 'Edit HTML', 'mailster' ) ?></a></li>
				<?php endif; ?>
			</ul>
			<?php if ( $slug != mailster_option( 'default_template' ) && current_user_can( 'mailster_delete_templates' ) ) {?>
				<div class="delete-theme">
					<a data-name="<?php echo esc_attr( $data['name'] ) ?>" href="edit.php?post_type=newsletter&amp;page=mailster_templates&amp;action=delete&amp;template=<?php echo $slug ?>&amp;_wpnonce=<?php echo wp_create_nonce( 'delete-' . $slug ) ?>" class="submitdelete deletion"><?php esc_html_e( 'Delete', 'mailster' );?></a>
				</div>
		<?php }?>
		</div>
		<div class="loader"></div>
	</li>
