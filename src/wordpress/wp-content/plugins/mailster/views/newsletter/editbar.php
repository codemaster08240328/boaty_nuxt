<div id="editbar">
	<a class="cancel top-cancel" href="#">&#10005;</a>
	<h4 class="editbar-title"></h4><span class="spinner" id="editbar-ajax-loading"></span>

		<div class="conditions">
			<span class="condition-labels">
				<span class="condition-if">if</span>
				<span class="condition-elseif">elseif</span>
				<span class="condition-else">else</span>
			</span>
		<?php

		$fields = array(
			'email' => mailster_text( 'email' ),
			'firstname' => mailster_text( 'firstname' ),
			'lastname' => mailster_text( 'lastname' ),
		);

		$customfields = mailster()->get_custom_fields();
		foreach ( $customfields as $field => $data ) {
			$fields[ $field ] = $data['name'];
		}
		$operators = array(
			'is' => __( 'is', 'mailster' ),
			'is_not' => __( 'is not', 'mailster' ),
			'contains' => __( 'contains', 'mailster' ),
			'contains_not' => __( 'contains not', 'mailster' ),
			'begin_with' => __( 'begins with', 'mailster' ),
			'end_with' => __( 'ends with', 'mailster' ),
			'is_greater' => __( 'is greater', 'mailster' ),
			'is_smaller' => __( 'is smaller', 'mailster' ),
			'pattern' => __( 'match regex pattern', 'mailster' ),
			'not_pattern' => __( 'does not match regex pattern', 'mailster' ),
		);

		?>
		<select class="condition-fields">
		<?php
		foreach ( $fields as $key => $name ) {
			echo '<option value="' . $key . '">' . $name . '</option>';
		}

		?>
		</select>
		<select class="condition-operators">
		<?php
		foreach ( $operators as $key => $name ) {
			echo '<option value="' . $key . '">' . $name . '</option>';
		}
		?>
		</select>
		<input class="condition-value" type="text" value="" class="widefat">
		</div>

		<div class="editbar-types">

		<div class="type single">
			<div class="conditinal-area-wrap">
				<div class="conditinal-area">
					<div class="type-input"><input type="text" class="input live widefat" value=""></div>
				</div>
			</div>
			<div class="clear clearfix">
				<a class="single-link-content" href="#"><?php esc_html_e( 'convert to link', 'mailster' );?></a> |
				<a class="replace-image" href="#"><?php esc_html_e( 'replace with image', 'mailster' ) ?></a>
			</div>
			<div id="single-link">
				<div class="clearfix">
						<label class="block"><div class="left"><?php esc_html_e( 'Link', 'mailster' ) ?></div><div class="right"><input type="text" class="input singlelink" value="" placeholder="<?php esc_html_e( 'insert URL', 'mailster' );?>"></div></label>
				</div>
				<div class="link-wrap">
					<div class="postlist">
					</div>
				</div>
			</div>
		</div>

		<div class="type btn">

			<div id="button-type-bar" class="nav-tab-wrapper hide-if-no-js">
				<a class="nav-tab" href="#text_button" data-type="dynamic"><?php esc_html_e( 'Text Button', 'mailster' );?></a>
				<a class="nav-tab nav-tab-active" href="#image_button"><?php esc_html_e( 'Image Button', 'mailster' );?></a>
			</div>
			<div id="image_button" class="tab">
			<?php $this->templateobj->buttons();?>
			<div class="clearfix">
					<label class="block"><div class="left"><?php esc_html_e( 'Alt Text', 'mailster' ) ?></div><div class="right"><input type="text" class="input buttonalt" value="" placeholder="<?php esc_html_e( 'image description', 'mailster' );?>"></div></label>
			</div>
			</div>
			<div id="text_button" class="tab" style="display:none">
			<div class="clearfix">
					<label class="block"><div class="left"><?php esc_html_e( 'Button Label', 'mailster' ) ?></div><div class="right"><input type="text" class="input buttonlabel" value="" placeholder="<?php esc_html_e( 'button label', 'mailster' );?>"></div></label>
			</div>
			</div>

			<div class="clearfix">
					<label class="block"><div class="left"><?php esc_html_e( 'Link Button', 'mailster' ) ?> <span class="description">(<?php esc_html_e( 'required', 'mailster' ) ?>)</span></div><div class="right"><input type="text" class="input buttonlink" value="" placeholder="<?php esc_html_e( 'insert URL', 'mailster' );?>"></div></label>
			</div>
			<div class="link-wrap">
				<div class="postlist">
				</div>
			</div>

		</div>

		<div class="type multi">
<?php

	add_filter( 'quicktags_settings', function( $qtint, $editor_id ) {
		$qtint['buttons'] = apply_filters( 'mymail_editor_quicktags', apply_filters( 'mailster_editor_quicktags', 'strong,em,link,block,del,img,ul,ol,li,spell,close' ) );
		return $qtint;

	}, 99, 2 );

	$toolbar1 = (string) apply_filters( 'mymail_editor_toolbar1', apply_filters( 'mailster_editor_toolbar1', 'bold,italic,underline,strikethrough,|,mailster_mce_button,|,bullist,numlist,|,alignleft,aligncenter,alignright,alignjustify,|,forecolor,backcolor,|,undo,redo,|,link,unlink,|,removeformat' ) );
	$toolbar2 = (string) apply_filters( 'mymail_editor_toolbar2', apply_filters( 'mailster_editor_toolbar2', '' ) );
	$toolbar3 = (string) apply_filters( 'mymail_editor_toolbar3', apply_filters( 'mailster_editor_toolbar3', '' ) );
	$toolbar4 = (string) apply_filters( 'mymail_editor_toolbar4', apply_filters( 'mailster_editor_toolbar4', '' ) );

	if ( ($toolbar2 || $toolbar3) && false === strpos( $toolbar1, 'wp_adv' ) ) {
		$toolbar1 .= ',|,wp_adv';
	}

	$editor_height = 295;

	$usersettings = get_all_user_settings();
	if ( isset( $usersettings['hidetb'] ) && $usersettings['hidetb'] ) {
		if ( $toolbar2 ) {
			$editor_height -= 30;
		}
		if ( $toolbar3 ) {
			$editor_height -= 60;
		}
	}


	wp_editor( '', 'mailster-editor', array(
		'wpautop' => false,
		'remove_linebreaks' => false,
		'media_buttons' => false,
		'textarea_rows' => 18,
		'teeny' => false,
		'quicktags' => true,
		'editor_height' => $editor_height,
		'tinymce' => array(
			'theme_advanced_buttons1' => $toolbar1,
			'theme_advanced_buttons2' => $toolbar2,
			'theme_advanced_buttons3' => $toolbar3,
			'theme_advanced_buttons4' => $toolbar4,
			'toolbar1' => $toolbar1,
			'toolbar2' => $toolbar2,
			'toolbar3' => $toolbar3,
			'toolbar4' => $toolbar4,
			'apply_source_formatting' => true,
			'content_css' => MAILSTER_URI . 'assets/css/tinymce-style.css?v=' . MAILSTER_VERSION,
		),
	) );
?>
		</div>

		<div class="type img">
			<div class="imagecontentwrap">
				<div class="left">
					<p><?php esc_html_e( 'Size', 'mailster' );?>: <input type="number" class="imagewidth">&times;<input type="number" class="imageheight">px
					</p>
					<div class="imagewrap">
					<img src="" alt="" class="imagepreview">
					</div>
				</div>
				<div class="right">
					<p>
						<label><input type="text" class="widefat" id="image-search" placeholder="<?php esc_html_e( 'search for images', 'mailster' );?>..." ></label>
					</p>
					<div class="imagelist">
					</div>
					<p>
						<a class="button button-small add_image"><?php ( ( ! function_exists( 'wp_enqueue_media' ) ) ? esc_html_e( 'Upload', 'mailster' ) : esc_html_e( 'Media Manager', 'mailster' ) ) ?></a>
						<a class="button button-small reload"><?php esc_html_e( 'Reload', 'mailster' ) ?></a>
						<a class="button button-small add_image_url"><?php esc_html_e( 'Insert from URL', 'mailster' ) ?></a>
					</p>
				</div>
			<br class="clear">
			</div>
			<p class="clearfix">
				<div class="imageurl-popup">
					<label class="block"><div class="left"><?php esc_html_e( 'Image URL', 'mailster' ) ?></div><div class="right"><input type="text" class="input imageurl" value="" placeholder="http://example.com/image.jpg"></div></label>
				</div>
					<label class="block"><div class="left"><?php esc_html_e( 'Alt Text', 'mailster' ) ?></div><div class="right"><input type="text" class="input imagealt" value="" placeholder="<?php esc_html_e( 'image description', 'mailster' );?>"></div></label>
					<label class="block"><div class="left"><?php esc_html_e( 'Link image to the this URL', 'mailster' ) ?></div><div class="right"><input type="text" class="input imagelink" value="" placeholder="<?php esc_html_e( 'insert URL', 'mailster' );?>"></div></label>
					<input type="hidden" class="input orgimageurl" value="">
			</p>
			<br class="clear">
		</div>

		<div class="type auto">

			<div id="embedoption-bar" class="nav-tab-wrapper hide-if-no-js">
				<a class="nav-tab nav-tab-active" href="#static_embed_options" data-type="static"><?php esc_html_e( 'static', 'mailster' );?></a>
				<a class="nav-tab" href="#dynamic_embed_options" data-type="dynamic"><?php esc_html_e( 'dynamic', 'mailster' );?></a>
				<a class="nav-tab" href="#rss_embed_options" data-type="rss"><?php esc_html_e( 'RSS', 'mailster' );?></a>
			</div>

			<div id="static_embed_options" class="tab">
				<p class="editbarinfo"><?php esc_html_e( 'Select a post', 'mailster' ) ?></p>
				<p class="alignleft">
					<label title="<?php esc_html_e( 'use the excerpt if exists otherwise use the content', 'mailster' );?>"><input type="radio" name="embed_options_content" class="embed_options_content" value="excerpt" checked> <?php esc_html_e( 'excerpt', 'mailster' );?> </label>
					<label title="<?php esc_html_e( 'use the content', 'mailster' );?>"><input type="radio" name="embed_options_content" class="embed_options_content" value="content"> <?php esc_html_e( 'full content', 'mailster' );?> </label>
				</p>
				<p id="post_type_select" class="alignright">
				<?php
				$pts = get_post_types( array( 'public' => true ), 'objects' );
				foreach ( $pts as $pt => $data ) {
					if ( in_array( $pt, array( 'attachment', 'newsletter' ) ) ) {
						continue;
					}
					?>
					<label><input type="checkbox" name="post_types[]" value="<?php echo $pt ?>" <?php checked( 'post' == $pt, true );?>> <?php echo $data->labels->name ?> </label>
				<?php } ?>
				</p>
				<p>
					<label><input type="text" class="widefat" id="post-search" placeholder="<?php esc_html_e( 'search for posts', 'mailster' );?>..." ></label>
				</p>
				<div class="postlist">
				</div>
			</div>

			<div id="dynamic_embed_options" class="clear tab" style="display:none;">

				<p>
				<?php
					$content = '<select id="dynamic_embed_options_content" class="check-for-posts"><option value="excerpt">' . __( 'the excerpt', 'mailster' ) . '</option><option value="content">' . __( 'the full content', 'mailster' ) . '</option></select>';

					$relative = '<select id="dynamic_embed_options_relative" class="check-for-posts">';
					$relativenames = array(
						-1 => __( 'the latest', 'mailster' ),
						-2 => __( 'the second latest', 'mailster' ),
						-3 => __( 'the third latest', 'mailster' ),
						-4 => __( 'the fourth latest', 'mailster' ),
						-5 => __( 'the fifth latest', 'mailster' ),
						-6 => __( 'the sixth latest', 'mailster' ),
						-7 => __( 'the seventh latest', 'mailster' ),
						-8 => __( 'the eighth latest', 'mailster' ),
						-9 => __( 'the ninth latest', 'mailster' ),
						-10 => __( 'the tenth latest', 'mailster' ),
						-11 => __( 'the eleventh latest', 'mailster' ),
						-12 => __( 'the twelfth latest', 'mailster' ),
					);

					foreach ( $relativenames as $key => $name ) {
						$relative .= '<option value="' . $key . '">' . $name . '</option>';
					}

					$relative .= '</select>';
					$post_types = '<select id="dynamic_embed_options_post_type">';
					foreach ( $pts as $pt => $data ) {
						if ( in_array( $pt, array( 'attachment', 'newsletter' ) ) ) {
							continue;
						}

						$post_types .= '<option value="' . $pt . '">' . $data->labels->singular_name . '</option>';
					}
					$post_types .= '</select>';

					printf( _x( 'Insert %1$s of %2$s %3$s', 'Insert [excerpt] of [latest] [post]', 'mailster' ), $content, $relative, $post_types );
				?>

				</p>
				<div class="right">
					<div class="current-preview">
					<label><?php esc_html_e( 'Current Match', 'mailster' ) ?></label>
					<h4 class="current-match">&hellip;</h4>
					<div class="current-tag code">&hellip;</div>
					</div>
				</div>
				<div class="left">
				<div id="dynamic_embed_options_cats"></div>
				</div>
				<p class="description clear"><?php esc_html_e( 'dynamic content get replaced with the proper content as soon as the campaign get send. Check the quick preview to see the current status of dynamic elements', 'mailster' );?></p>
			</div>

			<div id="rss_embed_options" class="tab">

				<div id="rss_input">
					<p>
						<?php esc_html_e( 'Enter feed URL', 'mailster' ) ?><br>
						<label><input type="text" id="rss_url" class="widefat" placeholder="http://example.com/feed.xml" value=""></label>
					</p>
					<ul id="recent_feeds">
					<?php if ( $recent_feeds = get_option( 'mailster_recent_feeds' ) ) : ?>
						<?php echo '<li><strong>' . __( 'Recent Feeds', 'mailster' ) . '</strong></li>';
						foreach ( $recent_feeds as $title => $url ) {
							echo '<li><a href="' . $url . '">' . $title . '</a></li>';
						}
						?>
					<?php endif; ?>
					</ul>
				</div>

				<div id="rss_more" style="display:none;">
					<div class="alignright"><a href="#" class="rss_change"><?php esc_html_e( 'change', 'mailster' );?></a></div>
					<div class="rss_info"></div>
					<p class="editbarinfo clear">&nbsp;</p>
					<p class="alignleft">
						<label title="<?php esc_html_e( 'use the excerpt if exists otherwise use the content', 'mailster' );?>"><input type="radio" name="embed_options_content_rss" class="embed_options_content_rss" value="excerpt" checked> <?php esc_html_e( 'excerpt', 'mailster' );?> </label>
						<label title="<?php esc_html_e( 'use the content', 'mailster' );?>"><input type="radio" name="embed_options_content_rss" class="embed_options_content_rss" value="content"> <?php esc_html_e( 'full content', 'mailster' );?> </label>
					</p>
					<div class="postlist">
					</div>
				</div>
			</div>

		</div>
			<div class="type codeview">
				<textarea id="module-codeview-textarea" autocomplete="off"></textarea>
			</div>

		</div>

		<div class="buttons clearfix">
			<button class="button button-primary save"><?php esc_html_e( 'Save', 'mailster' ) ?></button>
			<button class="button cancel"><?php esc_html_e( 'Cancel', 'mailster' ) ?></button>
			<label class="highdpi-checkbox" title="<?php esc_html_e( 'use HighDPI/Retina ready images if available', 'mailster' );?>">
				<input type="checkbox" class="highdpi" <?php checked( mailster_option( 'high_dpi' ) ); ?>> <?php esc_html_e( 'HighDPI/Retina ready', 'mailster' );?>
			</label>
			<a class="remove mailster-icon" title="<?php esc_html_e( 'remove element', 'mailster' ) ?>"></a>
		</div>
		<input type="hidden" class="factor" value="1">

	</div>
