<?php

class MailsterCampaigns {

	private $defaultTemplate = 'mailster';
	private $template;
	private $templatefile;
	private $templateobj;

	public function __construct() {

		add_action( 'plugins_loaded', array( &$this, 'init' ) );
		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'init', array( &$this, 'register_post_status' ) );

		if ( $hooks = get_option( 'mailster_hooks', false ) ) {

			add_action( 'mailster_autoresponder_hook', array( &$this, 'autoresponder_hook' ), 10, 2 );

			foreach ( (array) $hooks as $campaign_id => $hook ) {
				if ( $hook ) {
					add_action( $hook, array( &$this, 'autoresponder_hook_' . $campaign_id ), 10, 5 );
				}
			}
		}

	}


	public function init() {

		add_filter( 'transition_post_status', array( &$this, 'check_for_autoresponder' ), 10, 3 );
		add_action( 'mailster_finish_campaign', array( &$this, 'remove_revisions' ) );

		add_action( 'mailster_auto_post_thumbnail', array( &$this, 'get_post_thumbnail' ), 10, 1 );

		if ( is_admin() ) {

			add_action( 'paused_to_trash', array( &$this, 'paused_to_trash' ) );
			add_action( 'active_to_trash', array( &$this, 'active_to_trash' ) );
			add_action( 'queued_to_trash', array( &$this, 'queued_to_trash' ) );
			add_action( 'finished_to_trash', array( &$this, 'finished_to_trash' ) );
			add_action( 'trash_to_paused', array( &$this, 'trash_to_paused' ), 999 );

			add_action( 'admin_menu', array( &$this, 'remove_meta_boxs' ) );
			add_action( 'admin_menu', array( &$this, 'autoresponder_menu' ), 20 );

			add_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );
			add_filter( 'wp_insert_post_data', array( &$this, 'wp_insert_post_data' ), 1, 2 );
			add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );

			add_filter( 'after_delete_post', array( &$this, 'delete_campaign' ) );

			add_filter( 'pre_post_content', array( &$this, 'remove_kses' ) );

			add_filter( 'heartbeat_received', array( &$this, 'heartbeat' ), 9, 2 );

			add_filter( 'admin_post_thumbnail_html', array( &$this, 'add_post_thumbnail_link' ), 10, 2 );
			add_filter( 'admin_post_thumbnail_size', array( &$this, 'admin_post_thumbnail_size' ), 10, 3 );

			global $pagenow;

			switch ( $pagenow ) {

				case 'edit.php':
					add_action( 'wp_loaded', array( &$this, 'edit_hook' ) );
					add_action( 'get_the_excerpt', '__return_empty_string' );
					add_action( 'admin_enqueue_scripts', array( &$this, 'edit_assets' ), 10, 1 );
				break;

				case 'post-new.php':
					add_action( 'wp_loaded', array( &$this, 'post_new_hook' ) );
					add_action( 'admin_enqueue_scripts', array( &$this, 'post_edit_assets' ), 10, 1 );
				break;

				case 'post.php':
					add_action( 'pre_get_posts', array( &$this, 'post_hook' ) );
					add_action( 'admin_enqueue_scripts', array( &$this, 'post_edit_assets' ), 10, 1 );
				break;

				case 'revision.php':
					add_filter( '_wp_post_revision_field_post_content', array( &$this, 'revision_field_post_content' ), 10, 2 );

				break;

			}
		}

	}


	/**
	 *
	 *
	 * @return unknown
	 * @param unknown $func
	 * @param unknown $args
	 */
	public function __call( $func, $args ) {

		if ( substr( $func, 0, 18 ) == 'autoresponder_hook' ) {

			$campaign_id = intval( substr( $func, 19 ) );

			$subscribers = isset( $args[0] ) ? $args[0] : null;

			do_action( 'mailster_autoresponder_hook', $campaign_id, $subscribers );
			do_action( 'mymail_autoresponder_hook', $campaign_id, $subscribers );
		}

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscriber_ids (optional)
	 */
	public function autoresponder_hook( $campaign_id, $subscriber_ids = null ) {

		$meta = $this->meta( $campaign_id );

		if ( ! $meta['active'] || $meta['autoresponder']['action'] != 'mailster_autoresponder_hook' ) {
			return;
		}

		$all_subscribers = $this->get_subscribers( $campaign_id, null, true, (bool) $meta['autoresponder']['once'] );

		$subscribers = empty( $subscriber_ids )
			? $all_subscribers
			: array_values( array_intersect( $all_subscribers, is_array( $subscriber_ids ) ? $subscriber_ids : array( $subscriber_ids ) ) );

		$timestamp = strtotime( '+ ' . $meta['autoresponder']['amount'] . ' ' . $meta['autoresponder']['unit'] );

		$priority = $meta['autoresponder']['priority'];

		// mailster('queue')->remove($campaign_id, $subscribers);
		mailster( 'queue' )->bulk_add( $campaign_id, $subscribers, $timestamp, $priority, false, false, true );

	}


	public function register_post_type() {

		$is_autoresponder = is_admin() && isset( $_GET['post_status'] ) && $_GET['post_status'] == 'autoresponder';
		$single = $is_autoresponder ? __( 'Autoresponder', 'mailster' ) : __( 'Campaign', 'mailster' );
		$plural = $is_autoresponder ? __( 'Autoresponders', 'mailster' ) : __( 'Campaigns', 'mailster' );

		register_post_type( 'newsletter', array(

				'labels' => array(
					'name' => $plural,
					'singular_name' => $single,
					'add_new' => sprintf( __( 'New %s', 'mailster' ), $single ),
					'add_new_item' => __( 'Create A New Campaign', 'mailster' ),
					'edit_item' => sprintf( __( 'Edit %s', 'mailster' ), $single ),
					'new_item' => sprintf( __( 'New %s', 'mailster' ), $single ),
					'all_items' => __( 'All Campaigns', 'mailster' ),
					'view_item' => __( 'View Newsletter', 'mailster' ),
					'search_items' => sprintf( __( 'Search %s', 'mailster' ), $plural ),
					'not_found' => sprintf( __( 'No %s found', 'mailster' ), $single ),
					'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'mailster' ), $single ),
					'parent_item_colon' => '',
					'menu_name' => __( 'Newsletter', 'mailster' ),
					'filter_items_list' => __( 'Filter Newsletter list', 'mailster' ),
					'items_list_navigation' => __( 'Newsletter list navigation', 'mailster' ),
					'items_list' => __( 'Newsletter list', 'mailster' ),
				),

				'public' => true,
				'can_export' => true,
				'menu_icon' => 'dashicons-mailster',
				'show_ui' => true,
				'show_in_nav_menus' => false,
				'show_in_menu' => true,
				'show_in_admin_bar' => true,
				'exclude_from_search' => true,
				'capability_type' => 'newsletter',
				'map_meta_cap' => true,
				// 'menu_position' => 30,
				'has_archive' => mailster_option( 'hasarchive', false ) ? mailster_option( 'archive_slug', false ) : false,
				'hierarchical' => $is_autoresponder,
				'rewrite' => array( 'with_front' => false, 'slug' => mailster_option( 'slug', 'newsletter' ) ),
				'supports' => array(
					'title',
					'thumbnail',
					'revisions',
					'author',
				),
				'register_meta_box_cb' => array( &$this, 'meta_boxes' ),

		) );

	}


	public function register_post_status() {

		register_post_status( 'paused', array(
				'label' => __( 'Paused', 'mailster' ),
				'public' => true,
				'label_count' => _n_noop( __( 'Paused', 'mailster' ) . ' <span class="count">(%s)</span>', __( 'Paused', 'mailster' ) . ' <span class="count">(%s)</span>' ),
		) );

		register_post_status( 'active', array(
				'label' => __( 'Active', 'mailster' ),
				'public' => true,
				'label_count' => _n_noop( __( 'Active', 'mailster' ) . ' <span class="count">(%s)</span>', __( 'Active', 'mailster' ) . ' <span class="count">(%s)</span>' ),
		) );

		register_post_status( 'queued', array(
				'label' => __( 'Queued', 'mailster' ),
				'public' => true,
				'label_count' => _n_noop( __( 'Queued', 'mailster' ) . ' <span class="count">(%s)</span>', __( 'Queued', 'mailster' ) . ' <span class="count">(%s)</span>' ),
		) );

		register_post_status( 'finished', array(
				'label' => __( 'Finished', 'mailster' ),
				'public' => true,
				'label_count' => _n_noop( __( 'Finished', 'mailster' ) . ' <span class="count">(%s)</span>', __( 'Finished', 'mailster' ) . ' <span class="count">(%s)</span>' ),
		) );

		register_post_status( 'autoresponder', array(
				'label' => __( 'Autoresponder', 'mailster' ),
				'public' => ! is_admin(),
				'exclude_from_search' => true,
				'show_in_admin_all_list' => false,
				'label_count' => _n_noop( __( 'Autoresponder', 'mailster' ) . ' <span class="count">(%s)</span>', __( 'Autoresponders', 'mailster' ) . ' <span class="count">(%s)</span>' ),
		) );

	}


	public function meta_boxes() {

		global $post;
		add_meta_box( 'mailster_details', __( 'Details', 'mailster' ), array( &$this, 'newsletter_details' ), 'newsletter', 'normal', 'high' );
		add_meta_box( 'mailster_template', ( ! in_array( $post->post_status, array( 'active', 'finished' ) ) && ! isset( $_GET['showstats'] ) ) ? __( 'Template', 'mailster' ) : __( 'Clickmap', 'mailster' ), array( &$this, 'newsletter_template' ), 'newsletter', 'normal', 'high' );
		add_meta_box( 'mailster_submitdiv', __( 'Save', 'mailster' ), array( &$this, 'newsletter_submit' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_delivery', __( 'Delivery', 'mailster' ), array( &$this, 'newsletter_delivery' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_receivers', __( 'Receivers', 'mailster' ), array( &$this, 'newsletter_receivers' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_options', __( 'Options', 'mailster' ), array( &$this, 'newsletter_options' ), 'newsletter', 'side', 'high' );
		add_meta_box( 'mailster_attachments', __( 'Attachment', 'mailster' ), array( &$this, 'newsletter_attachment' ), 'newsletter', 'side', 'low' );

	}


	public function remove_meta_boxs() {
		remove_meta_box( 'submitdiv', 'newsletter', 'core' );
	}


	public function autoresponder_menu() {

		global $submenu;

		if ( current_user_can( 'edit_newsletters' ) ) {
			$submenu['edit.php?post_type=newsletter'][] = array(
				__( 'Autoresponder', 'mailster' ),
				'mailster_edit_autoresponders',
				'edit.php?post_status=autoresponder&post_type=newsletter',
			);
		}

	}


	public function newsletter_details() {
		global $post;
		global $post_id;

		include MAILSTER_DIR . 'views/newsletter/details.php';
	}


	public function newsletter_template() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/template.php';
	}


	public function newsletter_delivery() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/delivery.php';
	}


	public function newsletter_receivers() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/receivers.php';
	}


	public function newsletter_options() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/options.php';
	}


	public function newsletter_attachment() {
		global $post;
		global $post_id;
		include MAILSTER_DIR . 'views/newsletter/attachment.php';
	}


	/**
	 *
	 *
	 * @param unknown $post
	 */
	public function newsletter_submit( $post ) {
		global $action;
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object( $post_type );
		$can_publish = current_user_can( $post_type_object->cap->publish_posts );
		include MAILSTER_DIR . 'views/newsletter/submit.php';
	}


	// HOOKS
	public function edit_hook() {

		if ( isset( $_GET['post_type'] ) && 'newsletter' == $_GET['post_type'] ) {

			// duplicate campaign
			if ( isset( $_GET['duplicate'] ) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_nonce' ) ) {
					$id = intval( $_GET['duplicate'] );
					$id = $this->duplicate( $id );
				}

				// pause campaign
			} elseif ( isset( $_GET['pause'] ) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_nonce' ) ) {
					$id = intval( $_GET['pause'] );
					$this->pause( $id );
				}

				// continue/start campaign
			} elseif ( isset( $_GET['start'] ) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_nonce' ) ) {
					$id = intval( $_GET['start'] );
					$this->start( $id );
				}
				// finish campaign
			} elseif ( isset( $_GET['finish'] ) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_nonce' ) ) {
					$id = intval( $_GET['finish'] );
					$this->finish( $id );
				}
				// activate autoresponder
			} elseif ( isset( $_GET['activate'] ) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_nonce' ) ) {
					$id = intval( $_GET['activate'] );
					$this->activate( $id );
				}

				// deactivate autoresponder
			} elseif ( isset( $_GET['deactivate'] ) ) {
				if ( wp_verify_nonce( $_GET['_wpnonce'], 'mailster_nonce' ) ) {
					$id = intval( $_GET['deactivate'] );
					$this->deactivate( $id );

				}
			}

			if ( isset( $id ) && ! isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
				$status = ( isset( $_GET['post_status'] ) ) ? '&post_status=' . $_GET['post_status'] : '';
				( isset( $_GET['edit'] ) )
					? wp_redirect( 'post.php?post=' . $id . '&action=edit' )
					: wp_redirect( 'edit.php?post_type=newsletter' . $status );
				exit;
			}

			add_filter( 'wp', array( &$this, 'preload' ) );
			add_filter( 'the_excerpt', '__return_false' );
			add_filter( 'post_row_actions', array( &$this, 'quick_edit_btns' ), 10, 2 );
			add_filter( 'page_row_actions', array( &$this, 'quick_edit_btns' ), 10, 2 );
			add_filter( 'bulk_actions-edit-newsletter', array( &$this, 'bulk_actions' ) );
			add_filter( 'manage_edit-newsletter_columns', array( &$this, 'columns' ) );
			add_filter( 'manage_newsletter_posts_custom_column', array( &$this, 'columns_content' ) );
			add_filter( 'manage_edit-newsletter_sortable_columns', array( &$this, 'columns_sortable' ) );
			add_filter( 'parse_query', array( &$this, 'columns_sortable_helper' ) );

		}

	}


	public function post_hook() {

		global $post;
		// only on edit old newsletter and save
		if ( isset( $post ) && 'newsletter' == $post->post_type ) {

			add_filter( 'enter_title_here', array( &$this, 'title' ) );

			add_action( 'dbx_post_sidebar', array( mailster( 'ajax' ), 'add_ajax_nonce' ) );

			$this->post_data = $this->meta( $post->ID );

			add_action( 'submitpost_box', array( &$this, 'notice' ) );

			if ( isset( $_GET['template'] ) ) {
				$file = ( isset( $_GET['file'] ) ) ? $_GET['file'] : 'index.html';
				if ( isset( $this->post_data['head'] ) ) {
					unset( $this->post_data['head'] );
				}

				// $this->templateobj = mailster('template', $_GET['template'], $file);
				$this->set_template( $_GET['template'], $file, true );
			} elseif ( isset( $this->post_data['template'] ) ) {

				// $this->templateobj = mailster('template', $this->post_data['template'], $this->post_data['file']);
				$this->set_template( $this->post_data['template'], $this->post_data['file'] );
			} else {

				// $this->templateobj = mailster('template', mailster_option('default_template'), $this->post_data['file']);
				$this->set_template( mailster_option( 'default_template' ), $this->post_data['file'] );

			}
		}
	}


	public function post_new_hook() {

		if ( isset( $_GET['post_type'] ) && 'newsletter' == $_GET['post_type'] ) {

			add_filter( 'enter_title_here', array( &$this, 'title' ) );

			add_action( 'dbx_post_sidebar', array( mailster( 'ajax' ), 'add_ajax_nonce' ) );

			$this->post_data = $this->empty_meta();

			if ( isset( $_GET['template'] ) ) {
				$file = ( isset( $_GET['file'] ) ) ? $_GET['file'] : 'index.html';
				if ( isset( $this->post_data['head'] ) ) {
					unset( $this->post_data['head'] );
				}

				// $this->templateobj = mailster('template', $file, $_GET['template']);
				$this->set_template( $_GET['template'], $file, true );
			} else {

				// $this->templateobj = mailster('template', $this->post_data['file'],  mailster_option('default_template'));
				$this->set_template( mailster_option( 'default_template' ) );
			}
		}
	}


	/**
	 *
	 *
	 * @param unknown $query
	 */
	public function preload( $query ) {

		global $wp_query;
		$ids = wp_list_pluck( $wp_query->posts, 'ID' );
		if ( empty( $ids ) ) {
			return;
		}

		// preload meta from the displayed campaigns
		$meta = $this->meta( $ids );
		mailster( 'actions' )->get_by_campaign( $ids );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function notice() {

		global $post;

		switch ( $post->post_status ) {
			case 'finished':
				$timeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
				$timeoffset = mailster( 'helper' )->gmt_offset( true );
				$msg = sprintf( __( 'This Campaign was sent on %s', 'mailster' ), '<span class="nowrap">' . date( $timeformat, $this->meta( $post->ID, 'finished' ) + $timeoffset ) . '</span>' );
			break;
			case 'queued':
				$msg = __( 'This Campaign is currently in the queue', 'mailster' );
			break;
			case 'active':
				$msg = __( 'This Campaign is currently progressing', 'mailster' );
			break;
			case 'paused':
				$msg = __( 'This Campaign has been paused', 'mailster' );
			break;
		}

		if ( ! isset( $msg ) ) {
			return false;
		}

		echo '<div class="updated inline"><p><strong>' . $msg . '</strong></p></div>';

	}


	/**
	 *
	 *
	 * @param unknown $messages
	 * @return unknown
	 */
	public function updated_messages( $messages ) {

		global $post_id, $post;

		if ( $post->post_type != 'newsletter' ) {
			return $messages;
		}

		$messages[] = 'No subject!';

		$messages['newsletter'] = array(
			0 => '',
			1 => sprintf( __( 'Campaign updated. %s', 'mailster' ), '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . __( 'View Newsletter', 'mailster' ) . '</a>' ),
			2 => sprintf( __( 'Template changed. %1$s', 'mailster' ), '<a href="' . remove_query_arg( 'message', wp_get_referer() ) . '">' . __( 'Go back', 'mailster' ) . '</a>' ),
			3 => __( 'Template saved', 'mailster' ),
			4 => __( 'Campaign updated.', 'mailster' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Campaign restored to revision from %s', 'mailster' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Campaign published. %s', 'mailster' ), '<a href="' . esc_url( get_permalink( $post_id ) ) . '">' . __( 'View Newsletter', 'mailster' ) . '</a>' ),
			7 => __( 'Campaign saved.', 'mailster' ),
			8 => sprintf( __( 'Campaign submitted. %s', 'mailster' ), '<a target="_blank" href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_id ) ) ) . '">' . __( 'Preview Newsletter', 'mailster' ) . '</a>' ),
			9 => __( 'Campaign scheduled.', 'mailster' ),
			10 => __( 'Campaign draft updated.', 'mailster' ),
		);

		return $messages;
	}


	/**
	 *
	 *
	 * @param unknown $columns
	 * @return unknown
	 */
	public function columns( $columns ) {

		global $post;
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Name', 'mailster' ),
			'status' => __( 'Status', 'mailster' ),
			'total' => __( 'Total', 'mailster' ),
			'open' => __( 'Open', 'mailster' ),
			'click' => __( 'Clicks', 'mailster' ),
			'unsubs' => __( 'Unsubscribes', 'mailster' ),
			'bounces' => __( 'Bounces', 'mailster' ),
			'date' => __( 'Date', 'mailster' ),
		);
		return $columns;
	}


	/**
	 *
	 *
	 * @param unknown $columns
	 * @return unknown
	 */
	public function columns_sortable( $columns ) {

		$columns['status'] = 'status';

		return $columns;

	}


	/**
	 *
	 *
	 * @param unknown $query
	 */
	public function columns_sortable_helper( $query ) {

		$qv = $query->query_vars;

		if ( isset( $qv['post_type'] ) && $qv['post_type'] == 'newsletter' && isset( $qv['orderby'] ) ) {

			switch ( $qv['orderby'] ) {

				case 'status':
					add_filter( 'posts_orderby', array( &$this, 'columns_orderby_status' ) );
				break;

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $orderby
	 * @return unknown
	 */
	public function columns_orderby_status( $orderby ) {

		return str_replace( 'posts.post_date', 'posts.post_status', $orderby );

	}


	/**
	 *
	 *
	 * @param unknown $column
	 * @return unknown
	 */
	public function get_columns_content( $column ) {

		ob_start();

		$this->columns_content( $column );

		$output = ob_get_contents();

		ob_end_clean();

		return $output;
	}


	/**
	 *
	 *
	 * @param unknown $column
	 */
	public function columns_content( $column ) {

		global $post, $wpdb, $wp_post_statuses;

		$error = ini_get( 'error_reporting' );
		error_reporting( E_ERROR );

		$now = time();
		$timeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );

		$meta = $this->meta( $post->ID );

		global $pagenow;

		$totals = $this->get_totals( $post->ID );
		$errors = $this->get_errors( $post->ID );
		$sent = $this->get_sent( $post->ID );
		$sent_total = $this->get_sent( $post->ID, true );

		$opens = $this->get_opens( $post->ID );
		$open_totals = $this->get_opens( $post->ID, true );
		$clicks = $this->get_clicks( $post->ID );
		$click_totals = $this->get_clicks( $post->ID, true );
		$bounces = $this->get_bounces( $post->ID );
		$unsubscribes = $this->get_unsubscribes( $post->ID );

		switch ( $column ) {

			case 'status':

				$timestamp = isset( $meta['timestamp'] ) ? $meta['timestamp'] : $now;
				$timeoffset = mailster( 'helper' )->gmt_offset( true );

				if ( ! in_array( $post->post_status, array( 'pending', 'auto-draft' ) ) ) {

					// status is finished if this isset, even if the campaign is running;
					$status = isset( $campaign['finished'] ) ? 'finished' : $post->post_status;

					switch ( $status ) {
						case 'paused':
							echo '<span class="mailster-icon paused"></span> ';
							echo ( ! $sent ) ? $wp_post_statuses['paused']->label : __( 'Paused', 'mailster' );
							if ( $totals ) {
								if ( $sent ) {
									$p = round( $sent / $totals * 100 );
									echo "<br><div class='campaign-progress'><span class='bar' style='width:" . $p . "%'></span><span>&nbsp;" . sprintf( __( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) ) . "</span><var>$p%</var></div>";
								}
							} elseif ( is_null( $totals ) ) {
							} else {
								echo '<br><span class="mailster-icon no-receiver"></span> ' . __( 'no receivers!', 'mailster' );
							}
						break;
						case 'active':
							if ( $totals ) {
								echo '<span class="mailster-icon progressing"></span> ' . ( $sent == $totals ? __( 'completing job', 'mailster' ) : __( 'progressing', 'mailster' ) ) . '&hellip;' . ( $meta['timezone'] ? ' <span class="timezonebased"  title="' . __( 'This campaign is based on subscribers timezone and problably will take up to 24 hours', 'mailster' ) . '">24h</span>' : '' );
								$p = $totals ? round( $sent / $totals * 100 ) : 0;
								echo "<br><div class='campaign-progress'><span class='bar' style='width:" . $p . "%'></span><span>&nbsp;" . sprintf( __( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) ) . "</span><var>$p%</var></div>";
							} elseif ( is_null( $totals ) ) {
							} else {
								echo '<span class="mailster-icon no-receiver"></span> ' . __( 'no receivers!', 'mailster' );
							}
						break;
						case 'queued':
							echo '<span class="mailster-icon queued"></span> ';
							if ( $meta['timezone'] && $timestamp - $now < 86400 ) :
								$sub = $this->get_unsent_subscribers( $post->ID, array( 1 ), true );
								$timestamp = min( mailster( 'subscribers' )->get_timeoffset_timestamps( $sub, $timestamp ) );
								endif;
							printf( __( 'starts in %s', 'mailster' ), ( $timestamp - $now > 60 ) ? human_time_diff( $timestamp ) : __( 'less than a minute', 'mailster' ) );
							echo $meta['timezone'] ? ' <span class="timezonebased"  title="' . __( 'This campaign is based on subscribers timezone and problably will take up to 24 hours', 'mailster' ) . '">24h</span>' : '';
							echo "<br><span class='nonessential'>(" . date( $timeformat, $timestamp + $timeoffset ) . ')</span>';
						break;
						case 'finished':
							echo '<span class="mailster-icon finished"></span> ' . __( 'Finished', 'mailster' );
							echo "<br><span class='nonessential'>(" . date( $timeformat, $meta['finished'] + $timeoffset ) . ')</span>';
						break;
						case 'draft':
							echo '<span class="mailster-icon draft"></span> ' . $wp_post_statuses['draft']->label;
						break;
						case 'trash':
							echo $wp_post_statuses['trash']->label;
						break;
						case 'autoresponder':

							$is_active = $meta['active'];
							$active = $is_active ? 'active' : 'inactive';

							include MAILSTER_DIR . 'includes/autoresponder.php';

							$autoresponder = $meta['autoresponder'];

							echo '<span class="mailster-icon ' . $active . '"></span> ' . ( $is_active ? __( 'active', 'mailster' ) : __( 'inactive', 'mailster' ) );
							echo '<br>';

							echo '<span class="autoresponder-' . $active . '">';

							$time_frame_names = array(
								'hour' => __( 'hour(s)', 'mailster' ),
								'day' => __( 'day(s)', 'mailster' ),
								'week' => __( 'week(s)', 'mailster' ),
								'month' => __( 'month(s)', 'mailster' ),
								'year' => __( 'year(s)', 'mailster' ),
								);

							if ( 'mailster_autoresponder_timebased' == $autoresponder['action'] ) {

								$pts = get_post_types( array( 'public' => true ), 'object' );

								if ( $meta['timestamp'] && $meta['timestamp'] - $now < 0 ) {
									mailster( 'queue' )->autoresponder_timebased( $post->ID );
								}

								printf( __( 'send every %1$s %2$s', 'mailster' ),
									'<strong>' . $autoresponder['interval'] . '</strong>',
									'<strong>' . $time_frame_names[ $autoresponder['time_frame'] ] . '</strong>'
								);

								if ( $meta['timestamp'] ) {
									echo '<br>';
									printf( __( 'next campaign in %s', 'mailster' ),
										'<strong title="' . date( $timeformat, $meta['timestamp'] + $timeoffset ) . '">' . human_time_diff( $meta['timestamp'] ) . '</strong>'
									);
									echo ' &ndash; ' . sprintf( '#%s', '<strong title="' . sprintf( __( 'Next issue: %s', 'mailster' ), '#' . $autoresponder['issue'] ) . '">' . $autoresponder['issue'] . '</strong>' );
									if ( isset( $autoresponder['time_conditions'] ) ) {
										if ( $posts_required = max( 0, ( $autoresponder['time_post_count'] - $autoresponder['post_count_status'] ) ) ) {
											echo '<br>' . sprintf( __( 'requires %1$s more %2$s', 'mailster' ), ' <strong>' . $posts_required . '</strong>', ' <strong>' . $pts[ $autoresponder['time_post_type'] ]->labels->name . '</strong>' );
										}
									}
								}

								if ( isset( $autoresponder['endtimestamp'] ) ) {
									echo '<br>';
									printf( __( 'until %s', 'mailster' ),
										' <strong>' . date( $timeformat, $autoresponder['endtimestamp'] + $timeoffset ) . '</strong>'
									);
								}

								if ( count( $autoresponder['weekdays'] ) < 7 ) {

									global $wp_locale;
									$start_at = get_option( 'start_of_week' );
									$days = array();
									for ( $i = $start_at; $i < 7 + $start_at; $i++ ) {
										$j = $i;
										if ( ! isset( $wp_locale->weekday[ $j ] ) ) {
											$j = $j - 7;
										}

										if ( in_array( $j, $autoresponder['weekdays'] ) ) {
											$days[] = '<span title="' . $wp_locale->weekday[ $j ] . '">' . substr( $wp_locale->weekday[ $j ], 0, 2 ) . '</span>';
										}
									}

									echo '<br>';
									printf( _x( 'only on %s', 'only one [weekdays]', 'mailster' ),
										' <strong>' . implode( ', ', $days ) . '</strong>'
									);
								}
							} elseif ( 'mailster_autoresponder_usertime' == $autoresponder['action'] ) {

								$datefields = mailster()->get_custom_date_fields();

								if ( $autoresponder['userexactdate'] ) :

									printf( __( 'send %1$s %2$s %3$s', 'mailster' ),
										'<strong>' . $autoresponder['amount'] . '</strong>',
										'<strong>' . $time_frame_names[ $autoresponder['unit'] ] . '</strong>',
										($autoresponder['before_after'] > 0 ? __( 'after', 'mailster' ) : __( 'before', 'mailster' ))
									);

									echo ' ' . sprintf( __( 'the users %1$s value', 'mailster' ), ' <strong>' . ( isset( $datefields[ $autoresponder['uservalue'] ] ) ? $datefields[ $autoresponder['uservalue'] ]['name'] : $autoresponder['uservalue'] ) . '</strong>' );
								else :
									printf( __( 'send every %1$s %2$s', 'mailster' ),
										'<strong>' . $autoresponder['useramount'] . '</strong>',
										'<strong>' . $time_frame_names[ $autoresponder['userunit'] ] . '</strong>'
									);
									echo ' ' . sprintf( __( 'based on the users %1$s value', 'mailster' ), ' <strong>' . ( isset( $datefields[ $autoresponder['uservalue'] ] ) ? $datefields[ $autoresponder['uservalue'] ]['name'] : $autoresponder['uservalue'] ) . '</strong>' );

								endif;

							} elseif ( 'mailster_autoresponder_followup' == $autoresponder['action'] ) {

								if ( $campaign = $this->get( $post->post_parent ) ) {
									$types = array(
										1 => __( 'has been sent', 'mailster' ),
										2 => __( 'has been opened', 'mailster' ),
										3 => __( 'has been clicked', 'mailster' ),
									);
									printf( __( 'send %1$s %2$s %3$s', 'mailster' ),
										( $autoresponder['amount'] ? '<strong>' . $autoresponder['amount'] . '</strong> ' . $mailster_autoresponder_info['units'][ $autoresponder['unit'] ] : __( 'immediately', 'mailster' ) ),
										__( 'after', 'mailster' ),
										' <strong><a href="post.php?post=' . $campaign->ID . '&action=edit">' . $campaign->post_title . '</a></strong> ' . $types[ $autoresponder['followup_action'] ]
									);

								} else {
									echo '<br><span class="mailster-icon warning"></span> ' . __( 'Campaign does not exist', 'mailster' );
								}
							} else {

								printf( __( 'send %1$s %2$s %3$s', 'mailster' ),
									( $autoresponder['amount'] ? '<strong>' . $autoresponder['amount'] . '</strong> ' . $mailster_autoresponder_info['units'][ $autoresponder['unit'] ] : __( 'immediately', 'mailster' ) ),
									__( 'after', 'mailster' ),
									' <strong>' . $mailster_autoresponder_info['actions'][ $autoresponder['action'] ]['label'] . '</strong>'
								);

							}

							if ( ! $meta['ignore_lists'] ) {

								$lists = $this->get_lists( $post->ID );

								if ( ! empty( $lists ) ) {
									echo '<br>' . __( 'assigned lists', 'mailster' ) . ':<br>';
									foreach ( $lists as $i => $list ) {
										echo '<strong class="nowrap"><a href="edit.php?post_type=newsletter&page=mailster_lists&ID=' . $list->ID . '">' . $list->name . '</a></strong>';
										if ( $i + 1 < count( $lists ) ) {
											echo ', ';
										}
									}
								} else {
									echo '<br><span class="mailster-icon warning"></span> ' . __( 'no lists selected', 'mailster' );
								}
							}

							if ( $meta['list_conditions'] ) {

								$fields = array(
									'email' => mailster_text( 'email' ),
									'firstname' => mailster_text( 'firstname' ),
									'lastname' => mailster_text( 'lastname' ),
									'ip' => __( 'IP Address', 'mailster' ),
									'signup' => __( 'Signup Date', 'mailster' ),
									'ip_signup' => __( 'Signup IP', 'mailster' ),
									'confirm' => __( 'Confirm Date', 'mailster' ),
									'ip_confirm' => __( 'Confirm IP', 'mailster' ),
									'rating' => __( 'Rating', 'mailster' ),
								);

								$wp_meta = wp_parse_args( mailster( 'helper' )->get_wpuser_meta_fields(), array(
									'wp_capabilities' => __( 'User Role', 'mailster' ),
									'wp_user_level' => __( 'User Level', 'mailster' ),
								) );

								$customfields = mailster()->get_custom_fields();

								foreach ( $customfields as $field => $data ) {
									$fields[ $field ] = $data['name'];
								}

								echo '<br>' . __( 'only if', 'mailster' ) . '<br>';

								$conditions = array();
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

								foreach ( $meta['list_conditions']['conditions'] as $i => $condition ) {
									if ( ( ! isset( $fields[ $condition['field'] ] ) && ( ! isset( $wp_meta[ $condition['field'] ] ) ) ) ) {
										echo '<span class="mailster-icon warning"></span> ' . sprintf( __( '%s is missing!', 'mailster' ), '"' . $condition['field'] . '"' ) . '<br>';
										continue;
									}
									$conditions[] = '<strong>' . $fields[ $condition['field'] ] . '</strong> ' . $operators[ $condition['operator'] ] . ' "<strong>' . $condition['value'] . '</strong>"';
								}

								echo implode( '<br>' . __( strtolower( $meta['list_conditions']['operator'] ), 'mailster' ) . ' ', $conditions );

							}

							echo '</span>';

							if ( ( current_user_can( 'mailster_edit_autoresponders' ) && ( get_current_user_id() == $post->post_author || current_user_can( 'mailster_edit_others_autoresponders' ) ) ) ) {
								echo '<div class="row-actions">';
								$actions = array();

								if ( $active != 'active' ) {
									$actions['activate'] = '<a class="start live-action" href="?post_type=newsletter&activate=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . __( 'activate', 'mailster' ) . '">' . __( 'activate', 'mailster' ) . '</a>&nbsp;';
								} else {
									$actions['deactivate'] = '<a class="start live-action" href="?post_type=newsletter&deactivate=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . __( 'deactivate', 'mailster' ) . '">' . __( 'deactivate', 'mailster' ) . '</a>&nbsp;';
								}
								echo implode( ' | ', $actions );
								echo '</div>';
							}

						break;
					}
				} else {
					$status = get_post_status_object( $post->post_status );
					echo $status->label;
				}
				if ( ( current_user_can( 'publish_newsletters' ) && get_current_user_id() == $post->post_author ) || current_user_can( 'edit_others_newsletters' ) ) {
					echo '<div class="row-actions">';
					$actions = array();
					if ( $post->post_status == 'queued' ) {
						$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . __( 'Start Campaign now', 'mailster' ) . '">' . __( 'Start now', 'mailster' ) . '</a>&nbsp;';
					}
					if ( in_array( $post->post_status, array( 'active', 'queued' ) ) && $status != 'finished' ) {
						$actions['pause'] = '<a class="pause live-action" href="?post_type=newsletter&pause=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . __( 'Pause Campaign', 'mailster' ) . '">' . __( 'Pause', 'mailster' ) . '</a>&nbsp;';
					} elseif ( $post->post_status == 'paused' && $totals ) {
						if ( ! empty( $meta['timestamp'] ) ) {
							$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . __( 'Resume Campaign', 'mailster' ) . '">' . __( 'Resume', 'mailster' ) . '</a>&nbsp;';
						} else {
							$actions['start'] = '<a class="start live-action" href="?post_type=newsletter&start=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . __( 'Start Campaign', 'mailster' ) . '">' . __( 'Start', 'mailster' ) . '</a>&nbsp;';
						}
					}
					if ( in_array( $post->post_status, array( 'active', 'paused' ) ) ) {
						$actions['finish'] = '<a class="finish live-action" href="?post_type=newsletter&finish=' . $post->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . __( 'Finish Campaign', 'mailster' ) . '">' . __( 'Finish', 'mailster' ) . '</a>&nbsp;';
					}
					echo implode( ' | ', $actions );
					echo '</div>';
				}
			break;

			case 'total':

				if ( 'finished' == $post->post_status ) {
					echo number_format_i18n( $sent );
				} elseif ( 'autoresponder' == $post->post_status ) {
					echo number_format_i18n( $sent_total );
				} else {
					echo number_format_i18n( $totals );
				}

				if ( ! empty( $errors ) ) {
					echo '&nbsp;(<a href="edit.php?post_type=newsletter&page=mailster_subscribers&status=4" class="errors" title="' . sprintf( __( '%d emails have not been sent', 'mailster' ), $errors ) . '">+' . $errors . '</a>)';
				}

			break;

			case 'open':
				if ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					echo '<span class="s-opens">' . number_format_i18n( $opens ) . '</span>/<span class="tiny s-sent">' . number_format_i18n( $sent ) . '</span>';
					$rate = round( mailster( 'campaigns' )->get_open_rate( $post->ID ) * 100, 2 );
					echo "<br><span title='" . sprintf( __( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
					echo ' (' . $rate . '%)';
					echo '</span>';
				} else {
					echo '&ndash;';
				}
			break;

			case 'click':
				if ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					$rate = round( mailster( 'campaigns' )->get_click_rate( $post->ID ) * 100, 2 );
					$rate_a = round( mailster( 'campaigns' )->get_adjusted_click_rate( $post->ID ) * 100, 2 );
					echo number_format_i18n( $clicks );
					if ( $rate ) {
						echo "<br><span class='nonessential'>(<span title='" . sprintf( __( '%s of sent', 'mailster' ), $rate . '%' ) . "'>";
						echo '' . $rate . '%';
						echo '</span>|';
						echo "<span title='" . sprintf( __( '%s of opens', 'mailster' ), $rate_a . '%' ) . "'>";
						echo '' . $rate_a . '%';
						echo '</span>)</span>';
					} else {
						echo "<br><span title='" . sprintf( __( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
						echo ' (' . $rate . '%)';
						echo '</span>';
					}
				} else {
					echo '&ndash;';
				}
			break;

			case 'unsubs':
				if ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					$rate = round( mailster( 'campaigns' )->get_unsubscribe_rate( $post->ID ) * 100, 2 );
					$rate_a = round( mailster( 'campaigns' )->get_adjusted_unsubscribe_rate( $post->ID ) * 100, 2 );
					echo number_format_i18n( $unsubscribes );
					if ( $rate ) {
						echo "<br><span class='nonessential'>(<span title='" . sprintf( __( '%s of sent', 'mailster' ), $rate . '%' ) . "'>";
						echo '' . $rate . '%';
						echo '</span>|';
						echo "<span title='" . sprintf( __( '%s of opens', 'mailster' ), $rate_a . '%' ) . "'>";
						echo '' . $rate_a . '%';
						echo '</span>)</span>';
					} else {
						echo "<br><span title='" . sprintf( __( '%s of sent', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
						echo ' (' . $rate . '%)';
						echo '</span>';
					}
				} else {
					echo '&ndash;';
				}
			break;

			case 'bounces':
				if ( in_array( $post->post_status, array( 'finished', 'active', 'paused', 'autoresponder' ) ) ) {
					$rate = round( mailster( 'campaigns' )->get_bounce_rate( $post->ID ) * 100, 2 );
					echo number_format_i18n( $bounces );
					echo "<br><span title='" . sprintf( __( '%s of totals', 'mailster' ), $rate . '%' ) . "' class='nonessential'>";
					echo ' (' . $rate . '%)';
					echo '</span>';
				} else {
					echo '&ndash;';
				}
			break;

		}
		error_reporting( $error );
	}


	/**
	 *
	 *
	 * @param unknown $actions
	 * @return unknown
	 */
	public function bulk_actions( $actions ) {

		unset( $actions['edit'] );

		$actions['resume'] = __( 'Resume', 'mailster' );
		return $actions;
	}


	/**
	 *
	 *
	 * @param unknown $actions
	 * @param unknown $campaign
	 * @return unknown
	 */
	public function quick_edit_btns( $actions, $campaign ) {

		if ( $campaign->post_type != 'newsletter' ) {
			return $actions;
		}

		if ( ! in_array( $campaign->post_status, array( 'pending', 'auto-draft', 'trash', 'draft' ) ) ) {

			if ( ( current_user_can( 'duplicate_newsletters' ) && get_current_user_id() == $campaign->post_author ) || current_user_can( 'duplicate_others_newsletters' ) ) {
				$actions['duplicate'] = '<a class="duplicate" href="?post_type=newsletter&duplicate=' . $campaign->ID . ( isset( $_GET['post_status'] ) ? '&post_status=' . $_GET['post_status'] : '' ) . '&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '" title="' . sprintf( __( 'Duplicate Campaign %s', 'mailster' ), '&quot;' . $campaign->post_title . '&quot;' ) . '">' . __( 'Duplicate', 'mailster' ) . '</a>';
			}

			if ( ( current_user_can( 'publish_newsletters' ) && get_current_user_id() == $campaign->post_author ) || current_user_can( 'edit_others_newsletters' ) ) {
				$actions['statistics'] = '<a class="statistics" href="post.php?post=' . $campaign->ID . '&action=edit&showstats=1" title="' . sprintf( __( 'See stats of Campaign %s', 'mailster' ), '&quot;' . $campaign->post_title . '&quot;' ) . '">' . __( 'Statistics', 'mailster' ) . '</a>';
			}

			if ( $parent_id = $this->meta( $campaign->ID, 'parent_id' ) ) {
				$actions['autoresponder_link'] = '<a class="edit_base" href="post.php?post=' . $parent_id . '&action=edit">' . __( 'Edit base', 'mailster' ) . '</a>';
			}
		}
		return array_intersect_key( $actions, array_flip( array( 'edit', 'trash', 'view', 'statistics', 'duplicate', 'autoresponder_link' ) ) );
	}


	/**
	 *
	 *
	 * @param unknown $title
	 * @return unknown
	 */
	public function title( $title ) {
		return __( 'Enter Campaign Title here', 'mailster' );
	}


	/**
	 *
	 *
	 * @param unknown $campaign
	 */
	public function paused_to_trash( $campaign ) {
		set_transient( 'mailster_before_trash_status_' . $campaign->ID, 'paused' );
	}


	/**
	 *
	 *
	 * @param unknown $campaign
	 */
	public function active_to_trash( $campaign ) {
		set_transient( 'mailster_before_trash_status_' . $campaign->ID, 'active' );
	}


	/**
	 *
	 *
	 * @param unknown $campaign
	 */
	public function queued_to_trash( $campaign ) {
		set_transient( 'mailster_before_trash_status_' . $campaign->ID, 'queued' );
	}


	/**
	 *
	 *
	 * @param unknown $campaign
	 */
	public function finished_to_trash( $campaign ) {
		set_transient( 'mailster_before_trash_status_' . $campaign->ID, 'finished' );
	}


	/**
	 *
	 *
	 * @param unknown $campaign
	 */
	public function trash_to_paused( $campaign ) {

		$oldstatus = get_transient( 'mailster_before_trash_status_' . $campaign->ID, 'paused' );

		if ( $campaign->post_status != $oldstatus ) {
			$this->change_status( $campaign, $oldstatus, true );
		}

	}


	public function edit_assets() {

		$screen = get_current_screen();

		if ( $screen->id != 'edit-newsletter' ) {
			return;
		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'mailster-overview', MAILSTER_URI . 'assets/js/overview-script' . $suffix . '.js', array(), MAILSTER_VERSION, true );

		wp_enqueue_style( 'mailster-overview', MAILSTER_URI . 'assets/css/overview-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_localize_script( 'mailster-overview', 'mailsterL10n', array(
			'finish_campaign' => __( 'Do you really like to finish this campaign?', 'mailster' ),
		) );
	}


	public function post_edit_assets() {

		global $post, $wp_locale;

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		if ( ! isset( $post ) || $post->post_type != 'newsletter' ) {
			return;
		}

		wp_enqueue_script( 'mailster-script', MAILSTER_URI . 'assets/js/newsletter-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );

		if ( in_array( $post->post_status, array( 'active', 'finished' ) ) || isset( $_GET['showstats'] ) ) {

			wp_enqueue_script( 'google-jsapi', 'https://www.google.com/jsapi' );

			wp_enqueue_script( 'easy-pie-chart', MAILSTER_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );

			wp_enqueue_style( 'easy-pie-chart', MAILSTER_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), MAILSTER_VERSION );

		} else {

			if ( $post->post_status == 'autoresponder' ) {
				wp_enqueue_script( 'google-jsapi', 'https://www.google.com/jsapi' );
				wp_enqueue_script( 'easy-pie-chart', MAILSTER_URI . 'assets/js/libs/easy-pie-chart' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION, true );
				wp_enqueue_style( 'easy-pie-chart', MAILSTER_URI . 'assets/css/libs/easy-pie-chart' . $suffix . '.css', array(), MAILSTER_VERSION );
			}

			wp_enqueue_style( 'mailster-codemirror', MAILSTER_URI . 'assets/css/libs/codemirror' . $suffix . '.css', array(), MAILSTER_VERSION );

			if ( user_can_richedit() ) {
				wp_enqueue_script( 'editor' );
			}

			wp_enqueue_style( 'jquery-ui-style', MAILSTER_URI . 'assets/css/libs/jquery-ui' . $suffix . '.css', array(), MAILSTER_VERSION );
			wp_enqueue_style( 'jquery-datepicker', MAILSTER_URI . 'assets/css/datepicker' . $suffix . '.css', array(), MAILSTER_VERSION );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-draggable' );

			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );

			wp_enqueue_media();

		}

		wp_enqueue_style( 'mailster-flags', MAILSTER_URI . 'assets/css/flags' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_style( 'mailster-editor-style', MAILSTER_URI . 'assets/css/editor-style' . $suffix . '.css', array(), MAILSTER_VERSION );

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_localize_script( 'mailster-script', 'mailsterL10n', array(
				'loading' => __( 'loading', 'mailster' ),
				'add' => __( 'add', 'mailster' ),
				'or' => __( 'or', 'mailster' ),
				'move_module_up' => __( 'Move module up', 'mailster' ),
				'move_module_down' => __( 'Move module down', 'mailster' ),
				'duplicate_module' => __( 'Duplicate module', 'mailster' ),
				'remove_module' => __( 'remove module', 'mailster' ),
				'remove_all_modules' => __( 'Do you really like to remove all modules?', 'mailster' ),
				'save_template' => __( 'Save Template File', 'mailster' ),
				'add_module' => __( 'Add Module', 'mailster' ),
				'codeview' => __( 'Codeview', 'mailster' ),
				'module_label' => __( 'Name of the module (click to edit)', 'mailster' ),
				'edit' => __( 'Edit', 'mailster' ),
				'click_to_edit' => __( 'Click to edit %s', 'mailster' ),
				'click_to_add' => __( 'Click to add %s', 'mailster' ),
				'auto' => _x( 'Auto', 'for the autoimporter', 'mailster' ),
				'add_button' => __( 'add button', 'mailster' ),
				'add_s' => __( 'add %s', 'mailster' ),
				'remove_s' => __( 'remove %s', 'mailster' ),
				'curr_selected' => __( 'Currently selected', 'mailster' ),
				'remove_btn' => __( 'An empty link will remove this button! Continue?', 'mailster' ),
				'preview_for' => __( 'Preview for %s', 'mailster' ),
				'preview' => __( 'Preview', 'mailster' ),
				'read_more' => __( 'Read more', 'mailster' ),
				'invalid_image' => __( '%s does not contain a valid image', 'mailster' ),
				'enter_list_name' => __( 'Enter name of the list', 'mailster' ),
				'create_list' => _x( '%1$s of %2$s', '[recipientstype] of [campaignname]', 'mailster' ),

				'next' => __( 'next', 'mailster' ),
				'prev' => __( 'prev', 'mailster' ),
				'start_of_week' => get_option( 'start_of_week' ),
				'day_names' => $wp_locale->weekday,
				'day_names_min' => array_values( $wp_locale->weekday_abbrev ),
				'month_names' => array_values( $wp_locale->month ),
				'delete_colorschema' => __( 'Delete this color schema?', 'mailster' ),
				'delete_colorschema_all' => __( 'Do you really like to delete all custom color schema for this template?', 'mailster' ),
				'yourscore' => __( '%s out of 10', 'mailster' ),
				'yourscores' => array(
					__( 'This mail will hardly see any inbox!', 'mailster' ),
					__( 'You have to make it better!', 'mailster' ),
					__( 'Many inboxes will refuse this mail!', 'mailster' ),
					__( 'Not bad at all. Improve it further!', 'mailster' ),
					__( 'Almost perfect!', 'mailster' ),
					__( 'Great! Your campaign is ready to send!', 'mailster' ),
				),
				'undosteps' => mailster_option( 'undosteps', 10 ),
				'statuschanged' => __( 'The status of this campaign has changed. Please reload the page or %s', 'mailster' ),
				'click_here' => __( 'click here', 'mailster' ),
				'check_console' => __( 'Check the JS console for more info!', 'mailster' ),
				'send_now' => __( 'Do you really like to send this campaign now?', 'mailster' ),
				'select_image' => __( 'Select Image', 'mailster' ),
				'add_attachment' => __( 'Add Attachment', 'mailster' ),
		) );

		wp_localize_script( 'mailster-script', 'mailsterdata', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'url' => MAILSTER_URI,
				'isrtl' => is_rtl(),
				'codeview' => current_user_can( 'mailster_see_codeview' ),
				'datefields' => array_merge( array( 'added', 'updated', 'signup', 'confirm' ), mailster()->get_custom_date_fields( true ) ),
		) );

		wp_enqueue_style( 'mailster-style', MAILSTER_URI . 'assets/css/newsletter-style' . $suffix . '.css', array(), MAILSTER_VERSION );

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $post_id
	 * @return unknown
	 */
	public function add_post_thumbnail_link( $content, $post_id ) {

		global $post;

		if ( isset( $post ) && $post->post_type == 'newsletter' ) {

			if ( $meta = $this->meta( $post_id, 'auto_post_thumbnail' ) ) {
				// don't cache auto post thumbnails
				$content = str_replace( '.jpg" class="attachment-post-thumbnail', '.jpg?c=' . time() . '" class="attachment-post-thumbnail', $content );
			}

			if ( in_array( $post->post_status, array( 'active', 'finished' ) ) || isset( $_GET['showstats'] ) ) {

			} else {
				$content .= '<p><label><input type="checkbox" name="auto_post_thumbnail" value="1" ' . checked( $meta, true, false ) . '> ' . __( 'Create Screenshot for Feature Image', 'mailster' ) . '</label></p>';

				$timestamp = wp_next_scheduled( 'mailster_auto_post_thumbnail', array( $post_id ) );
				if ( $timestamp + 2 >= time() ) {
					$content .= '<p class="description" title="' . __( 'Generating the screenshot may take a while. Please reload the page to update', 'mailster' ) . '"><span class="spinner"></span>' . __( 'Creating Screenshot', 'mailster' ) . '&hellip;</p>';
				}
			}
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $size
	 * @param unknown $thumbnail_id
	 * @param unknown $post
	 * @return unknown
	 */
	public function admin_post_thumbnail_size( $size, $thumbnail_id, $post ) {

		if ( isset( $post ) && $post->post_type == 'newsletter' ) {
			$size = array( 600, 800 );
		}

		return $size;

	}



	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	public function remove_kses( $content ) {

		global $post;

		if ( isset( $post ) && $post->post_type == 'newsletter' ) {
			kses_remove_filters();
		}

		return $content;

	}


	/**
	 *
	 *
	 * @param unknown $post
	 * @param unknown $postarr
	 * @return unknown
	 */
	public function wp_insert_post_data( $post, $postarr ) {

		if ( ! isset( $post ) ) {
			return $post;
		}

		// if it's an autosave
		$is_autosave = wp_is_post_autosave( $postarr['ID'] );
		// but it's parent isn't a newsletter
		if ( $is_autosave && 'newsletter' != get_post_type( $is_autosave ) ) {
			return $post;
		}

		// no autosave and no newsletter
		if ( ! $is_autosave && 'newsletter' != $post['post_type'] ) {
			return $post;
		}

		if ( $is_autosave && isset( $_POST['data']['mailsterdata'] ) ) {

			parse_str( $_POST['data']['mailsterdata'], $postdata );
			$postdata = $postdata['mailster_data'];

		} elseif ( isset( $_POST['mailster_data'] ) ) {

			$postdata = $_POST['mailster_data'];

		} else {

			$postdata = $this->meta( $postarr['ID'] );
		}

		// sanitize the content and remove all content filters
		$post['post_content'] = mailster()->sanitize_content( $post['post_content'], null, $postdata['head'] );

		$post['post_excerpt'] = ! empty( $postdata['autoplaintext'] )
			? mailster( 'helper' )->plain_text( $post['post_content'] )
			: $post['post_excerpt'];

		if ( ! in_array( $post['post_status'], array( 'pending', 'draft', 'auto-draft', 'trash' ) ) ) {

			if ( $post['post_status'] == 'publish' ) {
				$post['post_status'] = 'paused';
			}

			$post['post_status'] = isset( $_POST['mailster_data']['active'] ) ? 'queued' : $post['post_status'];

		}

		if ( $post['post_status'] == 'autoresponder' && $postdata['autoresponder']['action'] != 'mailster_autoresponder_followup' ) {
			$post['post_parent'] = 0;
		}

		return $post;

	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 * @param unknown $post
	 * @param unknown $update  (optional)
	 * @return unknown
	 */
	public function save_campaign( $post_id, $post, $update = null ) {

		if ( ! isset( $post ) || $post->post_type != 'newsletter' ) {
			return $post;
		}

		$is_autosave = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;

		if ( $is_autosave && isset( $_POST['data']['mailsterdata'] ) ) {

			parse_str( $_POST['data']['mailsterdata'], $postdata );
			$postdata = $postdata['mailster_data'];

		} elseif ( isset( $_POST['mailster_data'] ) ) {

			$postdata = $_POST['mailster_data'];

		} else {
			return $post;
		}

		$timeoffset = mailster( 'helper' )->gmt_offset( true );
		$now = time();

		// activate kses filter
		kses_init_filters();

		$meta = $this->meta( $post_id );

		if ( isset( $postdata ) ) {

			// if ( function_exists( 'wp_encode_emoji' ) ) {
			// $postdata['subject'] = wp_encode_emoji( $postdata['subject'] );
			// $postdata['preheader'] = wp_encode_emoji( $postdata['preheader'] );
			// $postdata['from_name'] = wp_encode_emoji( $postdata['from_name'] );
			// }
			$meta['subject'] = $postdata['subject'];
			$meta['preheader'] = $postdata['preheader'];
			$meta['template'] = $postdata['template'];
			$meta['file'] = $postdata['file'];
			$meta['lists'] = isset( $postdata['lists'] ) ? (array) $postdata['lists'] : array();
			$meta['ignore_lists'] = isset( $postdata['ignore_lists'] ) && $postdata['ignore_lists'];
			$meta['from_name'] = $postdata['from_name'];
			$meta['from_email'] = $postdata['from_email'];
			$meta['reply_to'] = $postdata['reply_to'];
			$meta['timezone'] = isset( $postdata['timezone'] ) && $postdata['timezone'];

			if ( isset( $postdata['newsletter_color'] ) ) {
				$meta['colors'] = $postdata['newsletter_color'];
			}

			$meta['attachments'] = array();
			if ( isset( $postdata['attachments'] ) ) {
				$total_size = 0;
				$max_size = apply_filters( 'mymail_attachments_max_filesize', apply_filters( 'mailster_attachments_max_filesize', 1024 * 1024 ) );
				foreach ( $postdata['attachments'] as $attachment_id ) {
					if ( ! $attachment_id ) { continue;
					}
					$file = get_attached_file( $attachment_id );
					if ( @is_file( $file ) ) {
						$total_size += filesize( $file );
						if ( $total_size <= $max_size ) {
							$meta['attachments'][] = $attachment_id;
						} else {
							mailster_notice( sprintf( __( 'Attachments must not exceed the file size limit of %s!', 'mailster' ), '<strong>' . esc_html( size_format( $max_size ) ) . '</strong>' ), 'error', true );
						}
					} else {
						mailster_notice( __( 'Attachment doesn\'t exist or isn\'t readable!', 'mailster' ), 'error', true );
					}
				}
			}

			$meta['background'] = $postdata['background'];

			$meta['embed_images'] = isset( $postdata['embed_images'] );

			$meta['head'] = $postdata['head'];

			$is_autoresponder = ! ! $postdata['is_autoresponder'] && ! isset( $_POST['draft'] );

			$autoresponder = $postdata['autoresponder'];

			$post->post_parent = 0;
			$post->post_password = isset( $_POST['use_pwd'] ) ? $_POST['post_password'] : '';

			if ( $meta['auto_post_thumbnail'] = isset( $_POST['auto_post_thumbnail'] ) ) {

				wp_schedule_single_event( time(), 'mailster_auto_post_thumbnail', array( $post_id ) );

			} else {

				if ( $timestamp = wp_next_scheduled( 'mailster_auto_post_thumbnail', array( $post_id ) ) ) {
					wp_unschedule_event( $timestamp, 'mailster_auto_post_thumbnail', array( $post_id ) );
				}
			}

			if ( $is_autoresponder ) {

				if ( $post->post_status != 'autoresponder' && ! $is_autosave ) {
					$this->change_status( $post, 'autoresponder' );
					$post->post_status = 'autoresponder';
				}

				$meta['active'] = isset( $postdata['active_autoresponder'] ) && current_user_can( 'publish_newsletters' );

				$autoresponder['amount'] = max( 0, floatval( $autoresponder['amount'] ) );

				if ( in_array( $autoresponder['action'], array( 'mailster_subscriber_insert', 'mailster_subscriber_unsubscribed' ) ) ) {
					unset( $autoresponder['terms'] );

					$localtime = strtotime( $postdata['autoresponder_signup_date'] . ' ' . $postdata['autoresponder_signup_time'] );
					$meta['timestamp'] = $localtime - $timeoffset;

				} elseif ( 'mailster_post_published' == $autoresponder['action'] ) {

				} else {
					unset( $autoresponder['terms'] );
				}

				if ( 'mailster_autoresponder_timebased' == $autoresponder['action'] ) {

					$autoresponder['interval'] = max( 1, intval( $autoresponder['interval'] ) );
					$meta['timezone'] = isset( $autoresponder['timebased_timezone'] );

					$localtime = strtotime( $postdata['autoresponder_date'] . ' ' . $postdata['autoresponder_time'] );

					$autoresponder['weekdays'] = ( isset( $autoresponder['weekdays'] )
						? $autoresponder['weekdays']
						: array( date( 'w', $localtime ) ) );

					$localtime = mailster( 'helper' )->get_next_date_in_future( $localtime - $timeoffset, 0, $autoresponder['time_frame'], $autoresponder['weekdays'] );

					$meta['timestamp'] = $localtime;

					if ( isset( $autoresponder['endschedule'] ) ) {

						$localtime = strtotime( $postdata['autoresponder_enddate'] . ' ' . $postdata['autoresponder_endtime'] );
						$autoresponder['endtimestamp'] = max( $meta['timestamp'], $localtime - $timeoffset );

					}
				} elseif ( 'mailster_autoresponder_followup' == $autoresponder['action'] ) {

				} elseif ( 'mailster_autoresponder_usertime' == $autoresponder['action'] ) {

					$meta['timezone'] = isset( $autoresponder['usertime_timezone'] );
					$autoresponder['once'] = isset( $autoresponder['usertime_once'] );

				} elseif ( 'mailster_autoresponder_hook' == $autoresponder['action'] ) {

					$hooks = get_option( 'mailster_hooks', array() );
					$hooks[ $post->ID ] = $autoresponder['hook'];
					if ( ! $meta['active'] ) {
						unset( $hooks[ $post->ID ] );
					}

					update_option( 'mailster_hooks', $hooks );
					$autoresponder['once'] = isset( $autoresponder['hook_once'] );

				} else {

					$meta['timezone'] = isset( $autoresponder['post_published_timezone'] );

				}

				if ( isset( $_POST['post_count_status_reset'] ) ) {
					$autoresponder['post_count_status'] = 0;
				}

				$meta['autoresponder'] = $autoresponder;

			} else {
				// no autoresponder
				if ( $post->post_status == 'autoresponder' && ! $is_autosave ) {
					$meta['active'] = false;
					$this->change_status( $post, 'paused' );
					$post->post_status = 'paused';
				} else {
					$meta['active'] = isset( $postdata['active'] );
				}

				if ( isset( $_POST['sendnow'] ) ) {
					$post->post_status = 'queued';
					$meta['timestamp'] = $now;
					$meta['active'] = true;

				} elseif ( isset( $_POST['resume'] ) ) {
					$post->post_status = 'queued';
					$meta['active'] = true;

				} elseif ( isset( $_POST['draft'] ) ) {
					$post->post_status = 'draft';
					$meta['active'] = false;

				} elseif ( ( isset( $postdata ) && empty( $meta['timestamp'] ) ) || $meta['active'] ) {
					// save in UTC
					if ( isset( $postdata['date'] ) && isset( $postdata['time'] ) ) {
						$localtime = strtotime( $postdata['date'] . ' ' . $postdata['time'] );
					} else {
						$localtime = $now;
					}
					$meta['timestamp'] = max( $now, $localtime - $timeoffset );
				}

				// set status to 'active' if time is in the past
				if ( ! $is_autosave && $post->post_status == 'queued' && $now - $meta['timestamp'] >= 0 ) {
					$this->change_status( $post, 'active' );
					$post->post_status = 'active';

					// set status to 'queued' if time is in the future
				} elseif ( ! $is_autosave && $post->post_status == 'active' && $now - $meta['timestamp'] < 0 ) {
					$this->change_status( $post, 'queued' );
					$post->post_status = 'queued';
				}

				$meta['autoresponder'] = null;

			}

			mailster_remove_notice( 'camp_error_' . $post_id );

		}

		if ( isset( $postdata['list_conditions'] ) ) {

			$meta['list_conditions'] = $postdata['list'];

		} else {

			$meta['list_conditions'] = '';

		}

		$meta['autoplaintext'] = isset( $postdata['autoplaintext'] );

		if ( isset( $meta['active_autoresponder'] ) && $meta['active_autoresponder'] ) {
			if ( isset( $postdata ) ) {
				if ( ! $meta['timestamp'] ) {
					$meta['timestamp'] = max( $now, strtotime( $postdata['date'] . ' ' . $postdata['time'] ) );
				}
			}
		}

		// always inactive if autosave
		if ( $is_autosave ) {
			$meta['active'] = false;
		}

		$this->update_meta( $post_id, $meta );

		if ( ! $is_autosave ) {

			mailster( 'queue' )->clear( $post_id );

			// if post is published, active or queued and campaign start within the next 60 minutes
			if ( in_array( $post->post_status, array( 'active', 'queued', 'autoresponder' ) ) && $now - $meta['timestamp'] > -3600 ) {

				mailster( 'cron' )->update();

			}
			if ( in_array( $post->post_status, array( 'autoresponder' ) ) ) {

				switch ( $autoresponder['action'] ) {
					case 'mailster_autoresponder_usertime':
						mailster( 'queue' )->autoresponder_usertime( $post_id );
					break;
					case 'mailster_autoresponder_timebased':
						mailster( 'queue' )->autoresponder_timebased( $post_id );
					break;
					default:
						mailster( 'queue' )->autoresponder( $post_id );

				}
			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	public function meta( $id, $key = null ) {

		global $wpdb;

		$cache_key = 'campaign_meta';

		$meta = mailster_cache_get( $cache_key );
		if ( ! $meta ) {
			$meta = array();
		}

		if ( is_numeric( $id ) ) {

			if ( isset( $meta[ $id ] ) ) {
				if ( is_null( $key ) ) {
					return $meta[ $id ];
				}

				return isset( $meta[ $id ][ $key ] ) ? $meta[ $id ][ $key ] : null;
			}

			$ids = array( $id );
		} elseif ( is_array( $id ) ) {

			$ids = $id;
		}

		$defaults = $this->empty_meta();

		if ( is_null( $id ) && is_null( $key ) ) {
			return $defaults;
		}

		$sql = "SELECT post_id AS ID, REPLACE(meta_key, '_mailster_', '') AS meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key LIKE '_mailster_%'";

		if ( isset( $ids ) ) {
			$sql .= ' AND post_id IN (' . implode( ',', array_filter( $ids, 'is_numeric' ) ) . ')';
		}

		$result = $wpdb->get_results( $sql );

		foreach ( $result as $metadata ) {
			if ( ! isset( $meta[ $metadata->ID ] ) ) {
				$meta[ $metadata->ID ] = $defaults;
			}

			$meta[ $metadata->ID ][ $metadata->meta_key ] = $metadata->meta_value;
			// $meta = $row;
			// $lists = explode('|', $row['lists'] );
			// array_shift($lists);
			// $meta[$metadata->ID]['lists'] = $lists;
			if ( ! empty( $meta[ $metadata->ID ]['lists'] ) ) {
				$meta[ $metadata->ID ]['lists'] = maybe_unserialize( $meta[ $metadata->ID ]['lists'] );
			}

			if ( ! empty( $meta[ $metadata->ID ]['colors'] ) ) {
				$meta[ $metadata->ID ]['colors'] = maybe_unserialize( $meta[ $metadata->ID ]['colors'] );
			}

			if ( ! empty( $meta[ $metadata->ID ]['autoresponder'] ) ) {
				$meta[ $metadata->ID ]['autoresponder'] = maybe_unserialize( $meta[ $metadata->ID ]['autoresponder'] );
			}

			if ( ! empty( $meta[ $metadata->ID ]['list_conditions'] ) ) {
				$meta[ $metadata->ID ]['list_conditions'] = maybe_unserialize( $meta[ $metadata->ID ]['list_conditions'] );
			}

			if ( ! empty( $meta[ $metadata->ID ]['attachments'] ) ) {
				$meta[ $metadata->ID ]['attachments'] = maybe_unserialize( $meta[ $metadata->ID ]['attachments'] );
			}
		}

		mailster_cache_set( $cache_key, $meta );

		if ( is_null( $id ) && is_null( $key ) ) {
			return $meta;
		}

		if ( is_array( $id ) && is_null( $key ) ) {
			return $meta;
		}

		if ( is_array( $id ) ) {
			return wp_list_pluck( $meta, $key );
		}

		if ( is_null( $key ) ) {
			return isset( $meta[ $id ] ) ? $meta[ $id ] : null;
		}

		if ( is_null( $id ) ) {
			return wp_list_pluck( $meta, $key );
		}

		return isset( $meta[ $id ] ) && isset( $meta[ $id ][ $key ] ) ? $meta[ $id ][ $key ] : null;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $key
	 * @param unknown $value (optional)
	 * @return unknown
	 */
	public function update_meta( $id, $key, $value = null ) {

		$cache_key = 'campaign_meta';
		$meta = mailster_cache_get( $cache_key );
		if ( ! $meta ) {
			$meta = array();
		}

		if ( is_array( $key ) ) {
			$_meta = (array) $key;
		} else {
			$_meta = array( $key => $value );
		}

		foreach ( $_meta as $k => $v ) {
			// allowed NULL values
			if ( $v == '' && ! in_array( $k, array( 'timezone', 'embed_images', 'ignore_lists', 'autoplaintext', 'auto_post_thumbnail' ) ) ) {
				delete_post_meta( $id, '_mailster_' . $k );
			} else {
				update_post_meta( $id, '_mailster_' . $k, $v );
			}
		}

		if ( isset( $meta[ $id ] ) ) {
			unset( $meta[ $id ] );
			mailster_cache_set( $cache_key, $meta );
		}

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $id  (optional)
	 * @param unknown $key (optional)
	 * @return unknown
	 */
	private function empty_meta( $id = null, $key = null ) {
		return array(
			'parent_id' => null,
			'timestamp' => null,
			'finished' => null,
			'active' => null,
			'timezone' => mailster_option( 'timezone' ),
			'sent' => null,
			'error' => null,
			'from_name' => mailster_option( 'from_name' ),
			'from_email' => mailster_option( 'from' ),
			'reply_to' => mailster_option( 'reply_to' ),
			'subject' => null,
			'preheader' => null,
			'template' => null,
			'file' => null,
			'lists' => null,
			'ignore_lists' => null,
			'autoresponder' => null,
			'list_conditions' => null,
			'head' => null,
			'background' => null,
			'colors' => null,
			'embed_images' => mailster_option( 'embed_images' ),
			'autoplaintext' => true,
		);
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function pause( $id ) {
		if ( ! current_user_can( 'publish_newsletters' ) ) {
			wp_die( __( 'You are not allowed to pause campaigns.', 'mailster' ) );
		}
		$post = get_post( $id );

		$meta = $this->meta( $id );

		$meta['active'] = false;

		$this->update_meta( $id, $meta );

		if ( $this->change_status( $post, 'paused' ) ) {
			do_action( 'mailster_campaign_pause', $id );
			do_action( 'mymail_campaign_pause', $id );
			return true;
		} else {
			return false;
		}
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function start( $id ) {
		if ( ! current_user_can( 'publish_newsletters' ) ) {
			wp_die( __( 'You are not allowed to start campaigns.', 'mailster' ) );
		}

		$now = time();

		$post = get_post( $id );
		$meta = $this->meta( $id );
		if ( ! $this->get_totals( $id ) ) {
			return false;
		}

		$meta['active'] = true;

		if ( empty( $meta['timestamp'] ) || $post->post_status == 'queued' ) {
			$meta['timestamp'] = $now;
		}

		$status = ( $now - $meta['timestamp'] < 0 ) ? 'queued' : 'active';

		$this->update_meta( $id, $meta );

		if ( $this->change_status( $post, $status ) ) {
			do_action( 'mailster_campaign_start', $id );
			do_action( 'mymail_campaign_start', $id );
			mailster_remove_notice( 'camp_error_' . $id );
			return true;

		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $check (optional)
	 * @return unknown
	 */
	public function finish( $id, $check = true ) {
		if ( $check && ! current_user_can( 'publish_newsletters' ) ) {
			wp_die( __( 'You are not allowed to finish campaigns.', 'mailster' ) );
		}

		$post = get_post( $id );

		if ( ! in_array( $post->post_status, array( 'active', 'queued', 'paused' ) ) ) {
			return;
		}

		$meta = $this->meta( $id );
		$meta['totals'] = $this->get_totals( $id );
		$meta['sent'] = $this->get_sent( $id );
		$meta['errors'] = $this->get_errors( $id );
		$meta['finished'] = time();

		$placeholder = mailster( 'placeholder' );

		$placeholder->do_conditions( false );

		$placeholder->clear_placeholder();

		$placeholder->set_content( $post->post_title );
		$post->post_title = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $post->post_content );
		$post->post_content = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['subject'] );
		$meta['subject'] = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['preheader'] );
		$meta['preheader'] = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['from_name'] );
		$meta['from_name'] = $placeholder->get_content( false, array(), true );

		remove_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );
		kses_remove_filters();

		wp_update_post( array(
			'ID' => $id,
			'post_title' => $post->post_title,
			'post_content' => $post->post_content,
		) );

		kses_init_filters();
		add_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );

		$this->update_meta( $id, $meta );

		$this->change_status( $post, 'finished' );

		$parent_id = $this->meta( $id, 'parent_id' );

		if ( $parent_id = $this->meta( $id, 'parent_id' ) ) {
			$parent_sent = $this->meta( $parent_id, 'sent' );
			$parent_errors = $this->meta( $parent_id, 'errors' );

			$this->update_meta( $parent_id, 'sent', $parent_sent + $sent );
			$this->update_meta( $parent_id, 'errors', $parent_errors + $errors );
		}

		do_action( 'mailster_finish_campaign', $id );
		do_action( 'mymail_finish_campaign', $id );

		mailster( 'queue' )->remove( $id );

		mailster_remove_notice( 'camp_error_' . $id );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function duplicate( $id ) {

		$post = get_post( $id );

		if ( ( current_user_can( 'duplicate_newsletters' ) && get_current_user_id() != $post->post_author ) && ! current_user_can( 'duplicate_others_newsletters' ) ) {
			wp_die( __( 'You are not allowed to duplicate campaigns.', 'mailster' ) );
		}

		$lists = $this->get_lists( $post->ID, true );
		$meta = $this->meta( $post->ID );

		$meta['active'] = $meta['date'] = $meta['time'] = $meta['timestamp'] = $meta['parent_id'] = $meta['finished'] = $meta['sent'] = $meta['error'] = null;

		unset( $post->ID );
		unset( $post->guid );
		unset( $post->post_name );
		unset( $post->post_author );
		unset( $post->post_date );
		unset( $post->post_date_gmt );
		unset( $post->post_modified );
		unset( $post->post_modified_gmt );

		if ( preg_match( '# \((\d+)\)$#', $post->post_title, $hits ) ) {
			$post->post_title = trim( preg_replace( '#(.*) \(\d+\)$#', '$1 (' . ( ++$hits[1] ) . ')', $post->post_title ) );
		} elseif ( $post->post_title ) {
			$post->post_title .= ' (2)';
		}
		if ( $post->post_status == 'autoresponder' ) {
			$meta['autoresponder']['issue'] = 1;
			$meta['autoresponder']['post_count_status'] = 0;
		} else {
			$post->post_status = 'draft';
		}

		kses_remove_filters();
		$new_id = wp_insert_post( $post );
		kses_init_filters();

		if ( $new_id ) {

			$this->update_meta( $new_id, $meta );
			$this->add_lists( $new_id, $lists );

			do_action( 'mailster_campaign_duplicate', $id, $new_id );
			do_action( 'mymail_campaign_duplicate', $id, $new_id );

			return $new_id;
		}

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function activate( $id ) {

		$this->update_meta( $id, 'active', true );

		return true;
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function deactivate( $id ) {

		$this->update_meta( $id, 'active', false );

		return true;
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $delay (optional)
	 * @param unknown $issue (optional)
	 * @return unknown
	 */
	public function autoresponder_to_campaign( $id, $delay = 0, $issue = '' ) {

		$post = get_post( $id );
		if ( $post->post_status != 'autoresponder' ) {
			return false;
		}

		$id = $post->ID;

		$now = time();
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$lists = $this->get_lists( $post->ID, true );
		$meta = $this->meta( $post->ID );

		$meta['autoresponder'] = $meta['sent'] = $meta['errors'] = $meta['finished'] = null;

		$meta['active'] = true;

		$meta['timestamp'] = max( $now, $now + $delay );

		unset( $post->ID );
		unset( $post->guid );
		unset( $post->post_name );
		unset( $post->post_date );
		unset( $post->post_date_gmt );
		unset( $post->post_modified );
		unset( $post->post_modified_gmt );

		$post->post_status = $meta['timestamp'] <= $now ? 'active' : 'queued';

		$placeholder = mailster( 'placeholder' );

		$placeholder->do_conditions( false );

		$placeholder->clear_placeholder();
		$placeholder->add( array( 'issue' => $issue ) );

		$placeholder->set_content( $post->post_title );
		$post->post_title = $placeholder->get_content( false );

		$placeholder->set_content( $post->post_content );
		$post->post_content = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['subject'] );
		$meta['subject'] = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['preheader'] );
		$meta['preheader'] = $placeholder->get_content( false, array(), true );

		$placeholder->set_content( $meta['from_name'] );
		$meta['from_name'] = $placeholder->get_content( false, array(), true );

		remove_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );
		kses_remove_filters();

		$new_id = wp_insert_post( $post );

		kses_init_filters();
		add_action( 'save_post', array( &$this, 'save_campaign' ), 10, 3 );

		$meta['parent_id'] = $id;

		if ( $new_id ) {

			$this->update_meta( $new_id, $meta );
			$this->add_lists( $new_id, $lists );

			return $new_id;
		}

		return false;
	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function delete_campaign( $id ) {

		global $wpdb;

		// remove actions, queue and subscriber meta
		$wpdb->query( $wpdb->prepare( "DELETE a FROM {$wpdb->prefix}mailster_actions AS a WHERE a.campaign_id = %d", $id ) );
		$wpdb->query( $wpdb->prepare( "DELETE a FROM {$wpdb->prefix}mailster_queue AS a WHERE a.campaign_id = %d", $id ) );
		$wpdb->query( $wpdb->prepare( "DELETE a FROM {$wpdb->prefix}mailster_subscriber_meta AS a WHERE a.campaign_id = %d", $id ) );

		// unassign existing parents
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_value = %d AND meta_key = '_mailster_parent_id'", $id ) );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get( $id = null ) {

		if ( is_null( $id ) || is_array( $id ) ) {
			return $this->get_campaigns( $id );
		}

		$campaign = get_post( $id );

		return ( $campaign && $campaign->post_type == 'newsletter' ) ? $campaign : false;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $ids_only (optional)
	 * @return unknown
	 */
	public function get_lists( $id, $ids_only = false ) {

		$list_ids = $this->meta( $id, 'lists' );

		if ( $ids_only ) {
			return $list_ids;
		}

		return mailster( 'lists' )->get( $list_ids );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $list_ids
	 * @param unknown $clear    (optional)
	 * @return unknown
	 */
	public function add_lists( $id, $list_ids, $clear = false ) {

		if ( ! is_array( $list_ids ) ) {
			$list_ids = array( $list_ids );
		}

		if ( ! $clear ) {
			$list_ids = wp_parse_args( $list_ids, $this->meta( $id, 'lists' ) );
		}

		return $this->update_meta( $id, 'lists', $list_ids );

	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_active( $args = '' ) {
		$defaults = array(
			'post_status' => 'active',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_paused( $args = '' ) {
		$defaults = array(
			'post_status' => 'paused',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_queued( $args = '' ) {
		$defaults = array(
			'post_status' => 'queued',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_drafted( $args = '' ) {
		$defaults = array(
			'post_status' => 'draft',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_finished( $args = '' ) {
		$defaults = array(
			'post_status' => 'finished',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_pending( $args = '' ) {
		$defaults = array(
			'post_status' => 'pending',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_autoresponder( $args = '' ) {
		$defaults = array(
			'post_status' => 'autoresponder',
		);
		$args = wp_parse_args( $args, $defaults );

		return $this->get_campaigns( $args );
	}


	/**
	 *
	 *
	 * @param unknown $args (optional)
	 * @return unknown
	 */
	public function get_campaigns( $args = '' ) {

		$defaults = array(
			'post_type' => 'newsletter',
			'post_status' => array( 'active', 'paused', 'queued', 'draft', 'finished', 'pending', 'autoresponder' ),
			'orderby' => 'modified',
			'order' => 'DESC',
			'posts_per_page' => -1,
			'no_found_rows' => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		);
		$args = wp_parse_args( $args, $defaults );

		$campaigns = get_posts( $args );

		return $campaigns;
	}


	/**
	 *
	 *
	 * @param unknown $id           (optional)
	 * @param unknown $statuses     (optional)
	 * @param unknown $return_ids   (optional)
	 * @param unknown $ignore_sent  (optional)
	 * @param unknown $ignore_queue (optional)
	 * @param unknown $limit        (optional)
	 * @param unknown $offset       (optional)
	 * @param unknown $returnsql    (optional)
	 * @return unknown
	 */
	public function get_subscribers( $id = null, $statuses = null, $return_ids = false, $ignore_sent = false, $ignore_queue = false, $limit = null, $offset = 0, $returnsql = false ) {

		if ( $this->meta( $id, 'ignore_lists' ) ) {
			$lists = false;
		} else {
			$lists = $this->meta( $id, 'lists' );

			if ( empty( $lists ) && ! $returnsql ) {
				return $return_ids ? array() : 0;
			}
		}

		$conditions = $this->meta( $id, 'list_conditions' );

		return $this->get_subscribers_by_lists( $lists, $conditions, $statuses, $return_ids, $ignore_sent ? $id : false, $ignore_queue ? $id : false, $limit, $offset, $returnsql );
	}


	/**
	 *
	 *
	 * @param unknown $lists        (optional)
	 * @param unknown $conditions   (optional)
	 * @param unknown $statuses     (optional)
	 * @param unknown $return_ids   (optional)
	 * @param unknown $ignore_sent  (optional)
	 * @param unknown $ignore_queue (optional)
	 * @param unknown $limit        (optional)
	 * @param unknown $offset       (optional)
	 * @param unknown $returnsql    (optional)
	 * @return unknown
	 */
	public function get_subscribers_by_lists( $lists = false, $conditions = null, $statuses = null, $return_ids = false, $ignore_sent = false, $ignore_queue = false, $limit = null, $offset = 0, $returnsql = false ) {

		global $wpdb;

		$cache_key = 'get_subscriber_by_lists';
		$key = md5( serialize( array( $lists, $conditions, $statuses, $return_ids, $ignore_sent, $ignore_queue, $limit, $offset, $returnsql ) ) );

		$subscribers = mailster_cache_get( $cache_key );

		if ( ! $subscribers ) {
			$subscribers = array();
		}

		if ( isset( $subscribers[ $key ] ) ) {
			return $subscribers[ $key ];
		}

		if ( is_null( $statuses ) ) {
			$statuses = array( 1 );
		}

		$sql = 'SELECT ' . ( $return_ids ? 'a.ID' : 'COUNT(DISTINCT a.ID)' ) . " FROM {$wpdb->prefix}mailster_subscribers AS a";

		if ( $lists !== false ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_lists_subscribers AS ab ON a.ID = ab.subscriber_id";
		}

		if ( $ignore_sent ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = " . intval( $ignore_sent ) . ' AND b.type = 1';
		}

		if ( $ignore_queue ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_queue AS c ON a.ID = c.subscriber_id AND c.campaign_id = " . intval( $ignore_queue );
		}

		$sql .= $this->get_sql_join_by_condition( $conditions );

		$sql .= ' WHERE 1';

		if ( $lists !== false ) {
			// unassigned members if NULL
			if ( is_array( $lists ) ) {
				$lists = array_filter( $lists, 'is_numeric' );
			}

			$sql .= ( is_null( $lists ) ) ? ' AND ab.list_id IS NULL' : ( empty( $lists ) ? ' AND ab.list_id = 0' : ' AND ab.list_id IN(' . implode( ',', $lists ) . ')' );
		}

		if ( is_array( $statuses ) ) {
			$sql .= ' AND a.status IN (' . implode( ',', array_filter( $statuses, 'is_numeric' ) ) . ')';
		}

		if ( $ignore_sent ) {
			$sql .= ' AND b.subscriber_id IS NULL';
		}

		if ( $ignore_queue ) {
			$sql .= ' AND c.subscriber_id IS NULL';
		}

		$sql .= $this->get_sql_by_condition( $conditions );

		if ( $return_ids ) {
			$sql .= ' GROUP BY a.ID ORDER BY a.ID ASC';

			if ( ! is_null( $limit ) ) {
				$sql .= ' LIMIT ' . intval( $offset ) . ', ' . intval( $limit );
			}
		}

		$sql = apply_filters( 'mailster_campaign_get_subscribers_by_list_sql', $sql );

		if ( $returnsql ) {
			return $sql;
		}

		$subscribers[ $key ] = $return_ids ? $wpdb->get_col( $sql ) : $wpdb->get_var( $sql );

		mailster_cache_set( $cache_key, $subscribers );

		return $subscribers[ $key ];

	}


	/**
	 *
	 *
	 * @param unknown $id           (optional)
	 * @param unknown $statuses     (optional)
	 * @param unknown $return_ids   (optional)
	 * @param unknown $ignore_queue (optional)
	 * @param unknown $limit        (optional)
	 * @param unknown $offset       (optional)
	 * @return unknown
	 */
	public function get_unsent_subscribers( $id = null, $statuses = null, $return_ids = false, $ignore_queue = false, $limit = null, $offset = 0 ) {
		return $this->get_subscribers( $id, $statuses, $return_ids, true, $ignore_queue, $limit, $offset );
	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_sent_subscribers( $id = null ) {

		global $wpdb;

		if ( false === ( $sent_subscribers = mailster_cache_get( 'sent_subscribers' ) ) ) {

			$sql = "SELECT a.campaign_id, a.subscriber_id FROM {$wpdb->prefix}mailster_actions AS a WHERE type = 1 ORDER BY a.timestamp ASC";

			$result = $wpdb->get_results( $sql );

			$sent_subscribers = array();

			foreach ( $result as $row ) {
				if ( ! isset( $sent_subscribers[ $row->campaign_id ] ) ) {
					$sent_subscribers[ $row->campaign_id ] = array();
				}

				$sent_subscribers[ $row->campaign_id ][] = $row->subscriber_id;
			}

			mailster_cache_set( 'sent_subscribers', $sent_subscribers );

		}

		return ( is_null( $id ) ) ? $sent_subscribers : ( isset( $sent_subscribers[ $id ] ) ? $sent_subscribers[ $id ] : 0 );

	}


	/**
	 *
	 *
	 * @param unknown $id     (optional)
	 * @param unknown $unique (optional)
	 * @return unknown
	 */
	public function get_links( $id = null, $unique = true ) {

		global $wpdb;

		$campaign = $this->get( $id );
		if ( ! $campaign ) {
			return array();
		}

		$content = $campaign->post_content;

		preg_match_all( "/(href)=[\"'](.*)[\"']/Ui", $content, $urls );
		// preg_match_all('@((https?://)([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@',$content,$urls);
		$urls = ! empty( $urls[2] ) ? ( $urls[2] ) : array();

		return $unique ? array_values( array_unique( $urls ) ) : $urls;

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_excerpt( $id = null ) {

		global $wpdb;

		$campaign = $this->get( $id );
		if ( ! $campaign ) {
			return '';
		}

		$content = $campaign->post_content;

		$placeholder = mailster( 'placeholder', $content );

		$placeholder->set_campaign( $campaign->ID );

		$placeholder->add( array(
			'preheader' => '',
			'subject' => '',
			'can-spam' => '',
			'copyright' => '',
		) );

		$placeholder->share_service( get_permalink( $campaign->ID ), $campaign->post_title );

		$content = $placeholder->get_content();
		$content = preg_replace( '#<script[^>]*?>.*?</script>#si', '', $content );
		$content = preg_replace( '#<style[^>]*?>.*?</style>#si', '', $content );

		$allowed_tags = array( 'address', 'a', 'big', 'blockquote', 'br', 'b', 'center', 'cite', 'code', 'dd', 'dfn', 'dl', 'dt', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr', 'i', 'kbd', 'li', 'ol', 'pre', 'p', 'span', 'small', 'strike', 'strong', 'sub', 'sup', 'tt', 'ul', 'u' );
		$allowed_tags = '<' . implode( '><', $allowed_tags ) . '>';

		$content = strip_tags( $content, $allowed_tags );
		$content = str_replace( array( '&nbsp;', ' editable=""', '<p></p>', ' | ' ), '', $content );
		$content = preg_replace( '/(?:(?:\r\n|\r|\n)\s*){2}/s', "\n", $content );

		return trim( $content );

	}


	/**
	 *
	 *
	 * @param unknown $id           (optional)
	 * @param unknown $unsubscribes (optional)
	 * @param unknown $bounces      (optional)
	 * @return unknown
	 */
	public function get_totals( $id = null, $unsubscribes = true, $bounces = false ) {

		$campaign = $this->get( $id );
		if ( ! $campaign ) {
			return 0;
		}

		if ( in_array( $campaign->post_status, array( 'finished' ) ) ) {
			return $this->get_sent( $id, false );
		}
		$subscribers_count = $this->get_subscribers( $id );

		if ( $unsubscribes ) {
			$subscribers_count += $this->get_unsubscribes( $id );
		}

		if ( $bounces ) {
			$subscribers_count += $this->get_bounces( $id );
		}

		return $subscribers_count;

	}


	/**
	 *
	 *
	 * @param unknown $lists      (optional)
	 * @param unknown $conditions (optional)
	 * @param unknown $statuses   (optional)
	 * @return unknown
	 */
	public function get_totals_by_lists( $lists = false, $conditions = null, $statuses = null ) {

		$subscribers_count = $this->get_subscribers_by_lists( $lists, $conditions, $statuses );

		return $subscribers_count;

		return count( $subscribers );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_sent( $id = null, $total = false ) {

		return $this->get_action( 'sent', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_sent_rate( $id = null, $total = false ) {

		$totals = $this->get_totals( $id, $total );
		if ( ! $totals ) {
			return 0;
		}

		$sent = $this->get_sent( $id, $total );

		return $sent / $totals;

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_errors( $id = null, $total = false ) {

		return $this->get_action( 'errors', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_error_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$errors = $this->get_errors( $id, $total );

		return $errors / $sent;

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_opens( $id = null, $total = false ) {

		return $this->get_action( 'opens', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_open_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$opens = $this->get_opens( $id, $total );

		return $opens / $sent;

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_clicks( $id = null, $total = false ) {

		return $this->get_action( 'clicks', $id, $total );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_click_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$clicks = $this->get_clicks( $id, $total );

		return $clicks / $sent;

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_adjusted_click_rate( $id = null, $total = false ) {

		$open = $this->get_opens( $id, $total );
		if ( ! $open ) {
			return 0;
		}

		$clicks = $this->get_clicks( $id, $total );

		return $clicks / $open;

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_unsubscribes( $id = null ) {

		return $this->get_action( 'unsubscribes', $id );

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_unsubscribe_rate( $id = null, $total = false ) {

		$sent = $this->get_sent( $id, $total );
		if ( ! $sent ) {
			return 0;
		}

		$unsubscribes = $this->get_unsubscribes( $id, $total );

		return $unsubscribes / $sent;

	}


	/**
	 *
	 *
	 * @param unknown $id    (optional)
	 * @param unknown $total (optional)
	 * @return unknown
	 */
	public function get_adjusted_unsubscribe_rate( $id = null, $total = false ) {

		$open = $this->get_opens( $id, $total );
		if ( ! $open ) {
			return 0;
		}

		$unsubscribes = $this->get_unsubscribes( $id, $total );

		return $unsubscribes / $open;

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_bounces( $id = null ) {

		return $this->get_action( 'bounces', $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_bounce_rate( $id = null ) {

		$totals = $this->get_totals( $id );
		if ( ! $totals ) {
			return 0;
		}

		$bounces = $this->get_bounces( $id );

		return $bounces / ( $totals + $bounces );

	}


	/**
	 *
	 *
	 * @param unknown $action
	 * @param unknown $id     (optional)
	 * @param unknown $total  (optional)
	 * @return unknown
	 */
	private function get_action( $action, $id = null, $total = false ) {

		return mailster( 'actions' )->get_by_campaign( $id, $action . ( $total ? '_total' : '' ) );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_clicked_links( $id = null ) {

		return mailster( 'actions' )->get_clicked_links( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_error_list( $id = null ) {

		return mailster( 'actions' )->get_error_list( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_clients( $id = null ) {

		return mailster( 'actions' )->get_clients( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id (optional)
	 * @return unknown
	 */
	public function get_environment( $id = null ) {

		return mailster( 'actions' )->get_environment( $id );

	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function get_geo_data_country( $id ) {
	}


	/**
	 *
	 *
	 * @param unknown $id
	 */
	public function get_geo_data_city( $id ) {
	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @return unknown
	 */
	public function get_geo_data( $id ) {

		global $wpdb;

		$sql = "SELECT COUNT(DISTINCT a.subscriber_id) AS count, a.meta_value AS geo, b.meta_value AS coords FROM {$wpdb->prefix}mailster_subscriber_meta AS a LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS b ON a.subscriber_id = b.subscriber_id AND a.campaign_id = b.campaign_id AND b.meta_key = 'coords' WHERE a.meta_key = 'geo' AND a.campaign_id = %d AND a.meta_value != '|' GROUP BY a.meta_value ORDER BY count DESC";

		$result = $wpdb->get_results( $wpdb->prepare( $sql, $id ) );

		$geo_data = array();

		foreach ( $result as $row ) {
			$geo = explode( '|', $row->geo );

			if ( ! isset( $geo_data[ $geo[0] ] ) ) {
				$geo_data[ $geo[0] ] = array( 0 => array( 0, 0, '', 0, '' ) );
			}

			if ( ! $row->coords ) {
				$geo_data[ $geo[0] ][0][3]++;

			} else {
				$coords = $row->coords ? explode( ',', $row->coords ) : array( 0, 0 );

				$geo_data[ $geo[0] ][] = array(
					floatval( $coords[0] ),
					floatval( $coords[1] ),
					$geo[1],
					intval( $row->count ),
					$row->count . ' ' . _n( 'opened', 'opens', $row->count, 'mailster' ),
				);
			}
		}

		return $geo_data;

	}


	/**
	 *
	 *
	 * @param unknown $conditions
	 * @return unknown
	 */
	public function get_sql_join_by_condition( $conditions ) {

		global $wpdb;

		$joins = array();
		$sql = '';

		if ( empty( $conditions['conditions'] ) || ! is_array( $conditions ) ) {
			return $sql;
		}

		$custom_fields = mailster()->get_custom_fields( true );
		$custom_fields = wp_parse_args( array( 'firstname', 'lastname' ), (array) $custom_fields );
		$meta_fields = array( 'form', 'referer' );

		$wp_user_meta = wp_parse_args( array( 'wp_user_level', 'wp_capabilities' ), mailster( 'helper' )->get_wpuser_meta_fields() );
		// removing custom fields from wp user meta to prevent conflicts
		$wp_user_meta = array_diff( $wp_user_meta, array_merge( array( 'email' ), $custom_fields ) );

		foreach ( $conditions['conditions'] as $options ) {

			$field = esc_sql( $options['field'] );

			if ( in_array( $field, $custom_fields ) ) {

				$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS `field_$field` ON `field_$field`.subscriber_id = a.ID AND `field_$field`.meta_key = '$field'";

			} elseif ( in_array( $field, $wp_user_meta ) ) {
				$joins[] = "LEFT JOIN {$wpdb->usermeta} AS `meta_wp_$field` ON `meta_wp_$field`.user_id = a.wp_id AND `meta_wp_$field`.meta_key = '" . str_replace( 'wp_', $wpdb->prefix, $field ) . "'";

			} elseif ( in_array( $field, $meta_fields ) ) {

				$joins[] = "LEFT JOIN {$wpdb->prefix}mailster_subscriber_meta AS `meta_$field` ON `meta_$field`.subscriber_id = a.ID AND `meta_$field`.meta_key = '$field'";
			}
		}

		if ( ! empty( $joins ) ) {
			$sql = ' ' . implode( ' ', array_unique( $joins ) );
		}

		return $sql;
	}


	/**
	 *
	 *
	 * @param unknown $conditions
	 * @param unknown $tablealias (optional)
	 * @return unknown
	 */
	public function get_sql_by_condition( $conditions, $tablealias = 'a' ) {

		$cond = array();
		$sql = '';

		if ( empty( $conditions['conditions'] ) || ! is_array( $conditions ) ) {
			return $sql;
		}

		$custom_fields = mailster()->get_custom_fields( true );
		$custom_fields = array_merge( array( 'firstname', 'lastname' ), $custom_fields );

		$wp_user_meta = array_merge( array( 'wp_user_level', 'wp_capabilities' ), mailster( 'helper' )->get_wpuser_meta_fields() );
		// removing custom fields from wp user meta to prevent conflicts
		$wp_user_meta = array_diff( $wp_user_meta, array_merge( array( 'email' ), $custom_fields ) );

		$meta_fields = array( 'form', 'referer' );
		$custom_date_fields = mailster()->get_custom_date_fields( true );
		$timefields = array( 'added', 'updated', 'signup', 'confirm' );

		foreach ( $conditions['conditions'] as $options ) {

			$field = esc_sql( $options['field'] );
			$value = esc_sql( stripslashes( $options['value'] ) );
			$is_empty = '' == $value;
			$extra = '';
			$positive = false;

			switch ( $field ) {
				case 'rating':
					$value = str_replace( ',', '.', $value );
					if ( strpos( $value, '%' ) !== false || $value > 5 ) {
						$value = floatval( $value ) / 100;
					} elseif ( $value >= 1 ) {
						$value = floatval( $value ) * 0.2;
					}
				break;
			}

			switch ( $options['operator'] ) {
				case 'is':
					$positive = true;
				case 'is_not':

					if ( in_array( $field, $custom_date_fields ) ) {
						$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
					} elseif ( in_array( $field, $timefields ) ) {
						$f = "STR_TO_DATE(FROM_UNIXTIME($tablealias.$field),'%Y-%m-%d')";
					} elseif ( in_array( $field, $custom_fields ) ) {
						$f = "`field_$field`.meta_value";
					} elseif ( in_array( $field, $meta_fields ) ) {
						$f = "`meta_$field`.meta_value";
					} elseif ( in_array( $field, $wp_user_meta ) ) {
						$f = "`meta_wp_$field`.meta_value";
						if ( $field == 'wp_capabilities' ) {
							$value = 's:' . strlen( $value ) . ':"' . strtolower( $value ) . '";';
							$cond[] = "`meta_wp_$field`.meta_value " . ( $options['operator'] == 'is' ? 'LIKE' : 'NOT LIKE' ) . " '%$value%'";
							break;
						}
					} else {
						$f = "$tablealias.$field";
					}

					$c = $f . ' ' . ( $positive ? '=' : '!=' ) . " '$value'";
					if ( $is_empty && $positive ) {
						$c .= ' OR ' . $f . ' IS NULL';
					}

					$cond[] = $c;
				break;

				case 'contains':
					$positive = true;
				case 'contains_not':
					if ( $field == 'wp_capabilities' ) {
						$value = "'a:%" . strtolower( $value ) . "%'";
					} else {
						$value = "'%$value%'";
					}
					if ( in_array( $field, $custom_fields ) ) {
						$f = "`field_$field`.meta_value";
					} elseif ( in_array( $field, $meta_fields ) ) {
						$f = "`meta_$field`.meta_value";
					} elseif ( in_array( $field, $wp_user_meta ) ) {
						$f = "`meta_wp_$field`.meta_value";
					} else {
						$f = "$tablealias.$field";
					}

					$c = $f . ' ' . ( $positive ? 'LIKE' : 'NOT LIKE' ) . " $value";
					if ( $is_empty && $positive ) {
						$c .= ' OR ' . $f . ' IS NULL';
					}

					$cond[] = $c;
				break;

				case 'begin_with':
					if ( $field == 'wp_capabilities' ) {
						$value = "'%\"" . strtolower( $value ) . "%'";
					} else {
						$value = "'$value%'";
					}
					if ( in_array( $field, $custom_fields ) ) {
						$f = "`field_$field`.meta_value";
					} elseif ( in_array( $field, $meta_fields ) ) {
						$f = "`meta_$field`.meta_value";
					} elseif ( in_array( $field, $wp_user_meta ) ) {
						$f = "`meta_wp_$field`.meta_value";
					} else {
						$f = "$tablealias.$field";
					}

					$c = $f . " LIKE $value";

					$cond[] = $c;
				break;

				case 'end_with':
					if ( $field == 'wp_capabilities' ) {
						$value = "'%" . strtolower( $value ) . "\"%'";
					} else {
						$value = "'%$value'";
					}

					if ( in_array( $field, $custom_fields ) ) {
						$f = "`field_$field`.meta_value";
					} elseif ( in_array( $field, $meta_fields ) ) {
						$f = "`meta_$field`.meta_value";
					} elseif ( in_array( $field, $wp_user_meta ) ) {
						$f = "`meta_wp_$field`.meta_value";
					} else {
						$f = "$tablealias.$field";
					}

					$c = $f . " LIKE $value";

					$cond[] = $c;
				break;

				case 'is_greater_equal':
				case 'is_smaller_equal':
					$extra = '=';
				case 'is_greater':
				case 'is_smaller':

					if ( in_array( $field, $custom_date_fields ) ) {
						$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
						$value = "'$value'";
					} elseif ( in_array( $field, $timefields ) ) {
						$f = "STR_TO_DATE(FROM_UNIXTIME($tablealias.$field),'%Y-%m-%d')";
						$value = "'$value'";
					} elseif ( in_array( $field, $custom_fields ) ) {
						$f = "`field_$field`.meta_value";
						$value = is_numeric( $value ) ? floatval( $value ) : "'$value'";
					} elseif ( in_array( $field, $meta_fields ) ) {
						$f = "`meta_$field`.meta_value";
						$value = is_numeric( $value ) ? floatval( $value ) : "'$value'";
					} elseif ( in_array( $field, $wp_user_meta ) ) {
						$f = "`meta_wp_$field`.meta_value";
						if ( $field == 'wp_capabilities' ) {
							$value = "'NOTPOSSIBLE'";
						}
					} else {
						$f = "$tablealias.$field";
						$value = floatval( $value );
					}

					$c = $f . ' ' . ( $options['operator'] == 'is_greater' || $options['operator'] == 'is_greater_equal' ? '>' . $extra : '<' . $extra ) . " $value";

					$cond[] = $c;
				break;

				case 'pattern':
					$positive = true;
				case 'not_pattern':
					if ( in_array( $field, $custom_date_fields ) ) {
						$f = "STR_TO_DATE(`field_$field`.meta_value,'%Y-%m-%d')";
					} elseif ( in_array( $field, $timefields ) ) {
						$f = "STR_TO_DATE(FROM_UNIXTIME($tablealias.$field),'%Y-%m-%d')";
					} elseif ( in_array( $field, $custom_fields ) ) {
						$f = "`field_$field`.meta_value";
					} elseif ( in_array( $field, $meta_fields ) ) {
						$f = "`meta_$field`.meta_value";
					} elseif ( in_array( $field, $wp_user_meta ) ) {
						$f = "`meta_wp_$field`.meta_value";
					} else {
						$f = "$tablealias.$field";
						if ( $field == 'wp_capabilities' ) {
							$value = "'NOTPOSSIBLE'";
							break;
						}
					}
					if ( $is_empty ) {
						$value = '.';
					}

					$is_empty = '.' == $value;

					if ( ! $positive ) {
						$extra = 'NOT ';
					}

					$c = $f . ' ' . $extra . "REGEXP '$value'";
					if ( $is_empty && $positive ) {
						$c .= ' OR ' . $f . ' IS NULL';
					}

					$cond[] = $c;
				break;

			}
		}

		if ( ! empty( $cond ) ) {
			$sql .= ' AND (' . implode( ' ' . $conditions['operator'] . ' ', $cond ) . ')';
		}

		return $sql;
	}


	/**
	 *
	 *
	 * @param unknown $name
	 * @param unknown $campaign_id
	 * @param unknown $option      (optional)
	 * @param unknown $countonly   (optional)
	 * @return unknown
	 */
	public function create_list_from_option( $name, $campaign_id, $option = 'open', $countonly = false ) {

		global $wpdb;

		if ( ! current_user_can( 'mailster_edit_lists' ) ) {
			return false;
		}

		$campaign = $this->get( $campaign_id );

		if ( ! $campaign || $campaign->post_status == 'autoresponder' ) {
			return false;
		}

		switch ( $option ) {
			case 'sent';
			case 'not_sent';

				$sql = $wpdb->prepare( "SELECT a.ID FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 1 GROUP BY a.ID", $campaign->ID );

			break;
			case 'open':
			case 'not_open':

				$sql = $wpdb->prepare( "SELECT a.ID FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 2 GROUP BY a.ID", $campaign->ID );

			break;
			case 'click':

				$sql = $wpdb->prepare( "SELECT a.ID FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 3 GROUP BY a.ID", $campaign->ID );

			break;
			case 'open_not_click':

				$sql = $wpdb->prepare( "SELECT a.ID, c.type FROM {$wpdb->prefix}mailster_subscribers AS a LEFT JOIN {$wpdb->prefix}mailster_actions AS b ON a.ID = b.subscriber_id AND b.campaign_id = %d LEFT JOIN {$wpdb->prefix}mailster_actions AS c ON a.ID = c.subscriber_id AND c.campaign_id = %d WHERE b.campaign_id IS NOT NULL AND b.type = 2 OR c.type = 3 GROUP BY a.ID HAVING c.type != 3", $campaign->ID, $campaign->ID );

			break;
			default:
				$sql .= ' WHERE 1';
			break;
		}

		$subscribers = $wpdb->get_col( $sql );

		if ( in_array( $option, array( 'not_sent', 'not_open' ) ) ) {
			$all = $this->get_subscribers( $campaign->ID, null, true );
			$subscribers = array_values( array_diff( $all, $subscribers ) );
		}

		if ( $countonly ) {
			return count( $subscribers );
		}

		$options = array(
			'sent' => __( 'who have received', 'mailster' ),
			'not_sent' => __( 'who have not received', 'mailster' ),
			'open' => __( 'who have opened', 'mailster' ),
			'open_not_click' => __( 'who have opened but not clicked', 'mailster' ),
			'click' => __( 'who have opened and clicked', 'mailster' ),
			'not_open' => __( 'who have not opened', 'mailster' ),
		);

		$list = mailster( 'lists' )->add_segment( array(
			'name' => $name,
			'description' => sprintf( _x( 'A segment of all %1$s of %2$s', 'segment of all [recipients] from campaign [campaign]', 'mailster' ), $options[ $option ], '"' . $campaign->post_title . '"' ),
			'slug' => 'segment-' . $option . '-of-' . $campaign->ID,
		), true, $subscribers );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $parts       (optional)
	 * @param unknown $page        (optional)
	 * @param unknown $orderby     (optional)
	 * @param unknown $order       (optional)
	 * @return unknown
	 */
	public function get_recipients_part( $campaign_id, $parts = array( 'unopen', 'opens', 'clicks', 'unsubs', 'bounces' ), $page = 0, $orderby = 'sent', $order = 'ASC' ) {

		global $wpdb;

		$return = '';

		$limit = apply_filters( 'mailster_get_recipients_part', 1000 );
		$offset = intval( $page ) * $limit;

		$fields = array(
			'ID' => __( 'ID', 'mailster' ),
			'email' => mailster_text( 'email' ),
			'status' => __( 'Status', 'mailster' ),
			'firstname' => mailster_text( 'firstname' ),
			'lastname' => mailster_text( 'lastname' ),
			'sent' => __( 'Sent Date', 'mailster' ),
			'open' => __( 'Open Date', 'mailster' ),
			'open_count' => __( 'Open Count', 'mailster' ),
			'clicks' => __( 'Click Date', 'mailster' ),
			'click_count' => __( 'Click Count', 'mailster' ),
			'unsubs' => __( 'Unsubscribes', 'mailster' ),
			'bounces' => __( 'Bounces', 'mailster' ),
		);

		if ( ! in_array( $orderby, array_keys( $fields ) ) ) {
			$orderby = 'sent';
		}

		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) {
			$order = 'ASC';
		}

		$sql = $this->get_recipients_part_sql( $campaign_id, $parts );
		$sql .= " ORDER BY $orderby $order";
		$sql .= " LIMIT $offset, $limit";

		$subscribers = $wpdb->get_results( $sql );

		$count = 0;

		$timeformat = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
		$timeoffset = mailster( 'helper' )->gmt_offset( true );

		$subscribers_count = count( $subscribers );

		$unopen = in_array( 'unopen', $parts );
		$opens = in_array( 'opens', $parts );
		$clicks = in_array( 'clicks', $parts );
		$unsubs = in_array( 'unsubs', $parts );
		$bounces = in_array( 'bounces', $parts );

		if ( ! $offset ) {
			$return .= '<div class="ajax-list-header filter-list"><label>' . __( 'Filter', 'mailster' ) . ': </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-unopen" value="unopen" ' . checked( $unopen, true, false ) . '> ' . __( 'unopens', 'mailster' ) . ' </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-open" value="opens" ' . checked( $opens, true, false ) . '> ' . __( 'opens', 'mailster' ) . ' </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-click" value="clicks"' . checked( $clicks, true, false ) . '> ' . __( 'clicks', 'mailster' ) . ' </label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-unsubscribes" value="unsubs"' . checked( $unsubs, true, false ) . '> ' . __( 'unsubscribes', 'mailster' ) . '</label> ';
			$return .= '<label><input type="checkbox" class="recipients-limit show-bounces" value="bounces"' . checked( $bounces, true, false ) . '> ' . __( 'bounces', 'mailster' ) . ' </label> ';
			$return .= '<label>' . __( 'order by', 'mailster' ) . ' ';
			$return .= '<select class="recipients-order">';
			foreach ( $fields as $field => $name ) {
				$return .= '<option value="' . $field . '" ' . selected( $field, $orderby, false ) . '>' . $name . '</option>';
			}
			$return .= '</select></label>';
			$return .= '<a title="' . __( 'order direction', 'mailster' ) . '" class="recipients-order mailster-icon ' . ( $order == 'ASC' ? 'asc' : 'desc' ) . '"></a>';
			$return .= '</div>';
		}

		if ( ! $offset ) {
			$return .= '<table class="wp-list-table widefat recipients-list"><tbody>';
		}

		foreach ( $subscribers as $i => $subscriber ) {

			$name = trim( $subscriber->firstname . ' ' . $subscriber->lastname );

			$return .= '<tr ' . ( ! ( $i % 2 ) ? ' class="alternate" ' : '' ) . '>';
			$return .= '<td class="textright">' . ( $count + $offset + 1 ) . '</td><td><a class="show-receiver-detail" data-id="' . $subscriber->ID . '">' . ( $name ? $name . ' &ndash; ' : '' ) . $subscriber->email . '</a></td>';
			$return .= '<td title="' . __( 'sent', 'mailster' ) . '">' . ( $subscriber->sent ? str_replace( ' ', '&nbsp;', date( $timeformat, $subscriber->sent + $timeoffset ) ) : '&ndash;' ) . '</td>';
			$return .= '<td>' . ( isset( $subscriber->open_count ) && $subscriber->open_count ? '<span title="' . __( 'has opened', 'mailster' ) . '" class="mailster-icon mailster-icon-open"></span>' : '<span title="' . __( 'has not opened yet', 'mailster' ) . '" class="mailster-icon mailster-icon-unopen"></span>' ) . '</td>';
			$return .= '<td>' . ( isset( $subscriber->click_count_total ) && $subscriber->click_count_total ? sprintf( _n( '%s click', '%s clicks', $subscriber->click_count_total, 'mailster' ), $subscriber->click_count_total ) : '' ) . '</td>';
			$return .= '<td>' . ( isset( $subscriber->unsubs ) && $subscriber->unsubs ? '<span title="' . __( 'has unsubscribed', 'mailster' ) . '" class="mailster-icon mailster-icon-unsubscribe"></span>' : '' ) . '</td>';
			$return .= '<td>';
			$return .= ( isset( $subscriber->bounce_count ) ? '<span class="bounce-indicator mailster-icon mailster-icon-bounce ' . ( $subscriber->status == 3 ? 'hard' : 'soft' ) . '" title="' . sprintf( _n( '%s bounce', '%s bounces', $subscriber->bounce_count, 'mailster' ), $subscriber->bounce_count ) . '"></span>' : '' );
			$return .= ( $subscriber->status == 4 ) ? '<span class="bounce-indicator mailster-icon mailster-icon-bounce" title="' . __( 'an error occurred while sending to this receiver', 'mailster' ) . '">E</span>' : '';
			$return .= '</td>';
			$return .= '</tr>';
			$return .= '<tr id="receiver-detail-' . $subscriber->ID . '" class="receiver-detail' . ( ! ( $i % 2 ) ? '  alternate' : '' ) . '">';
			$return .= '<td></td><td colspan="6">';
			$return .= '<div class="receiver-detail-body"></div>';
			$return .= '</td>';
			$return .= '</tr>';

			$count++;

		}

		if ( $count && $limit == $subscribers_count ) {
			$return .= '<tr ' . ( $i % 2 ? ' class="alternate" ' : '' ) . '><td colspan="7"><a class="load-more-receivers button aligncenter" data-page="' . ( $page + 1 ) . '" data-types="' . implode( ',', $parts ) . '" data-order="' . $order . '" data-orderby="' . $orderby . '">' . __( 'load more recipients from this campaign', 'mailster' ) . '</a>' . '<span class="spinner"></span></td></tr>';
		}

		if ( ! $offset ) {
			$return .= '</tbody></table>';
		}

		return $return;

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $parts       (optional)
	 * @return unknown
	 */
	public function get_recipients_part_sql( $campaign_id, $parts = array( 'unopen', 'opens', 'clicks', 'unsubs', 'bounces' ) ) {

		global $wpdb;

		$unopen = in_array( 'unopen', $parts );
		$opens = in_array( 'opens', $parts );
		$clicks = in_array( 'clicks', $parts );
		$unsubs = in_array( 'unsubs', $parts );
		$bounces = in_array( 'bounces', $parts );

		$sql = 'SELECT a.ID, a.email, a.hash, a.status, firstname.meta_value AS firstname, lastname.meta_value AS lastname';
		$sql .= ', sent.timestamp AS sent, sent.count AS sent_count';

		if ( $unopen || $opens ) {
			$sql .= ', open.timestamp AS open, COUNT(open.count) AS open_count';
		}
		if ( $clicks ) {
			$sql .= ', click.timestamp AS clicks, COUNT(click.count) AS click_count, SUM(click.count) AS click_count_total';
		}
		if ( $unsubs ) {
			$sql .= ', unsub.timestamp AS unsubs, unsub.count AS unsub_count';
		}
		if ( $bounces ) {
			$sql .= ', bounce.timestamp AS bounces, bounce.count AS bounce_count';
		}
		$sql .= " FROM {$wpdb->prefix}mailster_subscribers AS a";

		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS firstname ON a.ID = firstname.subscriber_id AND firstname.meta_key = 'firstname'";
		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_subscriber_fields AS lastname ON a.ID = lastname.subscriber_id AND lastname.meta_key = 'lastname'";

		$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS sent ON a.ID = sent.subscriber_id AND sent.type = 1";

		if ( $unopen || $opens ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS open ON a.ID = open.subscriber_id AND open.type = 2 AND open.campaign_id = sent.campaign_id";
		}
		if ( $clicks ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS click ON a.ID = click.subscriber_id AND click.type = 3 AND click.campaign_id = sent.campaign_id";
		}
		if ( $unsubs ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS unsub ON a.ID = unsub.subscriber_id AND unsub.type = 4 AND unsub.campaign_id = sent.campaign_id";
		}
		if ( $bounces ) {
			$sql .= " LEFT JOIN {$wpdb->prefix}mailster_actions AS bounce ON a.ID = bounce.subscriber_id AND bounce.type IN (5,6) AND bounce.campaign_id = sent.campaign_id";
		}

		$sql .= ' WHERE sent.campaign_id = %d';

		$extra = array();

		if ( $unopen ) {
			$extra[] = 'open.timestamp IS NULL';
		}

		if ( $opens ) {
			$extra[] = 'open.timestamp IS NOT NULL';
		}

		if ( $clicks ) {
			$extra[] = 'click.timestamp IS NOT NULL';
		}

		if ( $unsubs ) {
			$extra[] = 'unsub.timestamp IS NOT NULL';
		}

		if ( $bounces ) {
			$extra[] = 'bounce.timestamp IS NOT NULL';
		}

		if ( ! empty( $extra ) ) {
			$sql .= ' AND (' . implode( ' OR ', $extra ) . ')';
		}

		$sql .= ' GROUP BY a.ID';

		$wpdb->query( 'SET SQL_BIG_SELECTS=1' );
		return $wpdb->prepare( $sql, $campaign_id );

	}


	/**
	 *
	 *
	 * @param unknown $response
	 * @param unknown $data
	 * @return unknown
	 */
	public function heartbeat( $response, $data ) {

		global $post;

		if ( isset( $data['wp_autosave'] ) && $data['wp_autosave']['post_type'] == 'newsletter' ) {
			kses_remove_filters();
		}

		if ( ! isset( $data['mailster'] ) ) {
			return $response;
		}

		$return = array();

		switch ( $data['mailster']['page'] ) {

			case 'overview':

				if ( ! isset( $_POST['data']['mailster']['ids'] ) ) {
					break;
				}

				$ids = array_filter( $_POST['data']['mailster']['ids'], 'is_numeric' );
				$return = array_fill_keys( $ids, null );

				foreach ( $ids as $id ) {

					$post = $this->get( $id );
					if ( ! $post ) {
						continue;
					}

					$meta = $this->meta( $id );
					$totals = $this->get_totals( $id );
					$sent = $this->get_sent( $id );

					$return[ $id ] = array(
						'status' => $post->post_status,
						'total' => $totals,
						'sent' => $sent,
						'sent_formatted' => '&nbsp;' . sprintf( __( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) ),
						'column-status' => $this->get_columns_content( 'status' ),
						'column-total' => $this->get_columns_content( 'total' ),
						'column-open' => $this->get_columns_content( 'open' ),
						'column-click' => $this->get_columns_content( 'click' ),
						'column-unsubs' => $this->get_columns_content( 'unsubs' ),
						'column-bounces' => $this->get_columns_content( 'bounces' ),
					);

				}
			break;

			case 'edit':

				$id = intval( $_POST['data']['mailster']['id'] );

				$post = $this->get( $id );
				if ( ! $post ) {
					break;
				}

				$meta = $this->meta( $id );
				$totals = $this->get_totals( $id );
				$sent = $this->get_sent( $id );
				$opens = $this->get_opens( $id );
				$clicks = $this->get_clicks( $id );
				$clicks_total = $this->get_clicks( $id, true );
				$unsubs = $this->get_unsubscribes( $id );
				$bounces = $this->get_bounces( $id );
				$open_rate = round( $this->get_open_rate( $id ) * 100, 2 );
				$click_rate = round( $this->get_click_rate( $id ) * 100, 2 );
				$bounce_rate = round( $this->get_bounce_rate( $id ) * 100, 2 );
				$unsubscribe_rate = round( $this->get_unsubscribe_rate( $id ) * 100, 2 );

				$environment = $this->get_environment( $id );

				$geolocation = '';

				if ( $geo_data = $this->get_geo_data( $post->ID ) ) :

					$unknown_cities = array();
					$countrycodes = array();

					foreach ( $geo_data as $countrycode => $data ) {
						$x = wp_list_pluck( $data, 3 );
						if ( $x ) {
							$countrycodes[ $countrycode ] = array_sum( $x );
						}

						if ( $data[0][3] ) {
							$unknown_cities[ $countrycode ] = $data[0][3];
						}
					}

					arsort( $countrycodes );
					$total = array_sum( $countrycodes );

					$i = 0;
					$geolocation = '';

					foreach ( $countrycodes as $countrycode => $count ) {

						$geolocation .= '<label title="' . mailster( 'geo' )->code2Country( $countrycode ) . '"><span class="big"><span class="mailster-flag-24 flag-' . strtolower( $countrycode ) . '"></span> ' . round( $count / $opens * 100, 2 ) . '%</span></label> ';
						if ( ++$i >= 5 ) {
							break;
						}
					}

					endif;

				$return[ $id ] = array(
					'status' => $post->post_status,
					'total' => $post->post_type == 'autoresponder' ? $sent : $totals,
					'sent' => $sent,
					'opens' => $opens,
					'clicks' => $clicks,
					'clicks_total' => $clicks_total,
					'unsubs' => $unsubs,
					'bounces' => $bounces,
					'open_rate' => $open_rate,
					'click_rate' => $click_rate,
					'unsub_rate' => $unsubscribe_rate,
					'bounce_rate' => $bounce_rate,
					'total_f' => number_format_i18n( $totals ),
					'sent_f' => number_format_i18n( $sent ),
					'opens_f' => number_format_i18n( $opens ),
					'clicks_f' => number_format_i18n( $clicks ),
					'clicks_total_f' => number_format_i18n( $clicks_total ),
					'unsubs_f' => number_format_i18n( $unsubs ),
					'bounces_f' => number_format_i18n( $bounces ),
					'environment' => $environment,
					'clickbadges' => array(
						'total' => $this->get_clicks( $id, true ),
						'clicks' => $this->get_clicked_links( $id ),
					),
					'sent_formatted' => '&nbsp;' . sprintf( __( '%1$s of %2$s sent', 'mailster' ), number_format_i18n( $sent ), number_format_i18n( $totals ) ),
					'geo_location' => $geolocation,
				);

			break;
		}

		$response['mailster'] = $return;

		// check for missing cron
		mailster( 'cron' )->check();
		// maybe change status
		mailster( 'queue' )->update_status();

		return $response;
	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscriber_id
	 * @param unknown $track         (optional)
	 * @param unknown $force         (optional)
	 * @param unknown $log           (optional)
	 * @return unknown
	 */
	public function send_to_subscriber( $campaign_id, $subscriber_id, $track = true, $force = false, $log = false ) {

		_deprecated_function( __FUNCTION__, '2.2', "mailster('campaigns')->send()" );

		return $this->send( $campaign_id, $subscriber_id, $track, $force, $log );

	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @param unknown $subscriber_id
	 * @param unknown $track         (optional)
	 * @param unknown $force         (optional)
	 * @param unknown $log           (optional)
	 * @return unknown
	 */
	public function send( $campaign_id, $subscriber_id, $track = true, $force = false, $log = true ) {

		global $wpdb;

		$campaign = $this->get( $campaign_id );

		if ( ! $campaign || $campaign->post_type != 'newsletter' ) {
			return new WP_Error( 'wrong_post_type', __( 'wrong post type', 'mailster' ) );
		}

		$subscriber = mailster( 'subscribers' )->get( $subscriber_id, true );

		if ( ! $subscriber ) {
			return new WP_Error( 'no_subscriber', __( 'No subscriber found', 'mailster' ) );
		}

		if ( ! in_array( $subscriber->status, array( 0, 1, 2 ) ) && ! $force ) {
			return new WP_Error( 'user_unsubscribed', __( 'User has not subscribed', 'mailster' ) );
		}

		$campaign_meta = $this->meta( $campaign->ID );

		$mail = mailster( 'mail' );

		// stop if send limit is reached
		if ( $mail->sentlimitreached ) {
			return new WP_Error( 'sendlimit_reached', sprintf( __( 'Sent limit of %1$s reached! You have to wait %2$s before you can send more mails!', 'mailster' ), mailster_option( 'send_limit' ), human_time_diff( get_option( '_transient_timeout__mailster_send_period_timeout' ) ) ) );
		}

		// $subscriber->hash = str_repeat('1', 32);
		$mail->to = $subscriber->email;
		$mail->to_name = $subscriber->fullname;
		$mail->subject = $campaign_meta['subject'];
		$mail->from = $campaign_meta['from_email'];
		$mail->from_name = $campaign_meta['from_name'];
		$mail->reply_to = $campaign_meta['reply_to'];
		$mail->bouncemail = mailster_option( 'bounce' );
		$mail->preheader = $campaign_meta['preheader'];
		$mail->embed_images = $campaign_meta['embed_images'];

		$mail->add_tracking_image = $track;
		$mail->hash = $subscriber->hash;
		$mail->set_subscriber( $subscriber->ID );

		$placeholder = mailster( 'placeholder' );

		$unsubscribelink = mailster()->get_unsubscribe_link( $campaign->ID );

		$mail->set_campaign( $campaign->ID );
		$placeholder->set_campaign( $campaign->ID );
		$placeholder->replace_custom_tags( false );

		if ( ! empty( $campaign_meta['attachments'] ) ) {
			foreach ( (array) $campaign_meta['attachments'] as $attachment_id ) {
				if ( ! $attachment_id ) {
					continue;
				}
				$file = get_attached_file( $attachment_id );
				if ( ! @is_file( $file ) ) {
					continue;
				}
				$mail->attachments[ basename( $file ) ] = $file;
			}
		}

		// campaign specific stuff (cache it)
		if ( ! ( $content = mailster_cache_get( 'campaign_send_' . $campaign->ID ) ) ) {

			$content = mailster()->sanitize_content( $campaign->post_content, null, $campaign_meta['head'] );

			$placeholder->set_content( $content );

			$placeholder->add( array(
				'preheader' => $campaign_meta['preheader'],
				'subject' => $campaign_meta['subject'],
				'webversion' => '<a href="{webversionlink}">' . mailster_text( 'webversion' ) . '</a>',
				'webversionlink' => get_permalink( $campaign->ID ),
				'unsub' => '<a href="{unsublink}">' . mailster_text( 'unsubscribelink' ) . '</a>',
				'unsublink' => $unsubscribelink,
				'forward' => '<a href="{forwardlink}">' . mailster_text( 'forward' ) . '</a>',
				'profile' => '<a href="{profilelink}">' . mailster_text( 'profile' ) . '</a>',
				'email' => '<a href="">{emailaddress}</a>',
			) );

			$placeholder->share_service( get_permalink( $campaign->ID ), $campaign->post_title );
			$content = $placeholder->get_content( false );
			$content = mailster( 'helper' )->prepare_content( $content );

			mailster_cache_set( 'campaign_send_' . $campaign->ID, $content );

		}

		$placeholder->set_content( $content );

		// user specific stuff
		$placeholder->replace_custom_tags( true );
		$placeholder->set_subscriber( $subscriber->ID );

		$forwardlink = mailster()->get_forward_link( $campaign->ID, $subscriber->email );
		$profilelink = mailster()->get_profile_link( $campaign->ID, $subscriber->hash );

		$placeholder->add( wp_parse_args( array(
			'emailaddress' => $subscriber->email,
			'forwardlink' => $forwardlink,
			'profilelink' => $profilelink,
		), (array) $subscriber ) );

		$content = $placeholder->get_content();

		if ( $track ) {

			// replace links
			$content = mailster()->replace_links( $content, $subscriber->hash, $campaign->ID );

		}

		$mail->content = $content;

		if ( ! $campaign_meta['autoplaintext'] ) {
			$placeholder->set_content( $campaign->post_excerpt );
			$mail->plaintext = mailster( 'helper' )->plain_text( $placeholder->get_content(), true );
		}

		$MID = mailster_option( 'ID' );

		$mail->add_header( 'X-Mailster', $subscriber->hash );
		$mail->add_header( 'X-Mailster-Campaign', $campaign->ID );
		$mail->add_header( 'X-Mailster-ID', $MID );

		$listunsubscribe = '<' . $unsubscribelink . '>';

		// $listunsubscribebody = implode("\n", array(
		// 'X-Mailster: '.$subscriber->hash,
		// 'X-Mailster-Campaign: '.$campaign->ID,
		// 'X-Mailster-ID: '.$MID,
		// ));
		$mail->add_header( 'List-Unsubscribe', $listunsubscribe );

		$placeholder->set_content( $mail->subject );
		$mail->subject = $placeholder->get_content();

		$result = $mail->send();

		if ( $result && ! is_wp_error( $result ) ) {
			if ( $log ) {
				do_action( 'mailster_send', $subscriber->ID, $campaign->ID );
				do_action( 'mymail_send', $subscriber->ID, $campaign->ID );
			}

			return true;
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		if ( $mail->is_user_error() ) {
			if ( $log ) {
				do_action( 'mailster_subscriber_error', $subscriber->ID, $campaign->ID, $mail->last_error->getMessage() );
				do_action( 'mymail_subscriber_error', $subscriber->ID, $campaign->ID, $mail->last_error->getMessage() );
			}

			return new WP_Error( 'user_error', $mail->last_error->getMessage() );
		}

		if ( $mail->last_error ) {
			if ( $log ) {
				do_action( 'mailster_campaign_error', $subscriber->ID, $campaign->ID, $mail->last_error->getMessage() );
				do_action( 'mymail_campaign_error', $subscriber->ID, $campaign->ID, $mail->last_error->getMessage() );
			}

			return new WP_Error( 'error', $mail->last_error->getMessage() );
		}

		return new WP_Error( 'unknown', __( 'unknown', 'mailster' ) );

	}


	/**
	 *
	 *
	 * @param unknown $post
	 * @param unknown $new_status
	 * @param unknown $silent     (optional)
	 * @return unknown
	 */
	public function change_status( $post, $new_status, $silent = false ) {
		if ( ! $post ) {
			return false;
		}

		if ( $post->post_status == $new_status ) {
			return true;
		}

		$old_status = $post->post_status;

		global $wpdb;

		if ( $wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $post->ID ) ) ) {
			if ( ! $silent ) {
				wp_transition_post_status( $new_status, $old_status, $post );
			}

			return true;
		}

		return false;

	}


	/**
	 *
	 *
	 * @param unknown $new_status
	 * @param unknown $old_status
	 * @param unknown $post
	 */
	public function check_for_autoresponder( $new_status, $old_status, $post ) {

		if ( $new_status == $old_status ) {
			return;
		}

		if ( 'publish' != $new_status ) {
			return;
		}

		if ( 'newsletter' == $post->post_type ) {
			return;
		}

		if ( get_post_meta( $post->ID, 'mailster_ignore', true ) ) {
			return;
		}

		$now = time();

		$campaigns = $this->get_autoresponder();

		if ( empty( $campaigns ) ) {
			return;
		}

		// delete cache;
		mailster_cache_delete( 'get_last_post' );

		foreach ( $campaigns as $campaign ) {

			if ( ! $this->meta( $campaign->ID, 'active' ) ) {
				continue;
			}

			$meta = $this->meta( $campaign->ID, 'autoresponder' );

			if ( 'mailster_post_published' == $meta['action'] ) {

				if ( $meta['post_type'] != $post->post_type ) {
					continue;
				}

				// if post count is reached
				if ( ! ( ++$meta['post_count_status'] % ( $meta['post_count'] + 1 ) ) ) {

					if ( isset( $meta['terms'] ) ) {

						$pass = true;

						foreach ( $meta['terms'] as $taxonomy => $term_ids ) {
							// ignore "any taxonomy"
							if ( $term_ids[0] == '-1' ) {
								continue;
							}

							$post_terms = get_the_terms( $post->ID, $taxonomy );

							// no post_terms set but required => give up (not passed)
							if ( ! $post_terms ) {
								$pass = false;
								break;
							}

							$pass = $pass && ! ! count( array_intersect( wp_list_pluck( $post_terms, 'term_id' ), $term_ids ) );

						}

						if ( ! $pass ) {
							continue;
						}
					}

					$integer = floor( $meta['amount'] );
					$decimal = $meta['amount'] - $integer;

					$send_offset = ( strtotime( '+' . $integer . ' ' . $meta['unit'], 0 ) + ( strtotime( '+1 ' . $meta['unit'], 0 ) * $decimal ) );

					if ( $new_id = $this->autoresponder_to_campaign( $campaign->ID, $send_offset, $meta['issue']++ ) ) {

						$new_campaign = $this->get( $new_id );

						mailster_notice( sprintf( __( 'New campaign %1$s has been created and is going to be sent in %2$s.', 'mailster' ), '<strong>"<a href="post.php?post=' . $new_campaign->ID . '&action=edit">' . $new_campaign->post_title . '</a>"</strong>', '<strong>' . human_time_diff( $now + $send_offset ) . '</strong>' ), 'info', true );

						do_action( 'mailster_autoresponder_post_published', $campaign->ID, $new_id );
						do_action( 'mymail_autoresponder_post_published', $campaign->ID, $new_id );

					}
				}

				$this->update_meta( $campaign->ID, 'autoresponder', $meta );

			} elseif ( 'mailster_autoresponder_timebased' == $meta['action'] ) {

				if ( $meta['time_post_type'] != $post->post_type ) {
					continue;
				}

				if ( ! isset( $meta['time_conditions'] ) ) {
					continue;
				}

				$meta['post_count_status']++;

				$this->update_meta( $campaign->ID, 'autoresponder', $meta );

				mailster( 'queue' )->autoresponder_timebased( $campaign->ID, true );

			}
		}

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file   (optional)
	 * @param unknown $verify (optional)
	 */
	public function set_template( $slug, $file = 'index.html', $verify = false ) {

		if ( $verify ) {

			if ( ! is_dir( mailster( 'templates' )->path . '/' . $slug ) ) {
				$slug = mailster_option( 'default_template', $this->defaultTemplate );
			}
			if ( ! file_exists( mailster( 'templates' )->path . '/' . $slug . '/' . $file ) ) {
				$file = 'index.html';
			}
		}

		$this->template = $slug;
		$this->templatefile = $file;

		$this->templateobj = mailster( 'template', $slug, $file );

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_template() {
		return $this->template;
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	public function get_file() {
		return ( ! empty( $this->templatefile ) ) ? $this->templatefile : 'index.html';
	}


	/**
	 *
	 *
	 * @param unknown $campaign_id
	 * @return unknown
	 */
	public function get_post_thumbnail( $campaign_id ) {

		// not on localhost
		if ( mailster_is_local() ) {
			return;
		}

		$campaign = $this->get( $campaign_id );

		if ( ! $campaign ) {
			return;
		}

		$campaign_url = add_query_arg( 'frame', 0, get_permalink( $campaign_id ) );

		if ( $campaign->post_password ) {
			$campaign_url = add_query_arg( 'pwd', md5( $campaign->post_password . AUTH_KEY ), $campaign_url );
		}

		$hash = md5( $campaign->post_content );

		$url = 'https://s.wordpress.com/mshots/v1/' . ( rawurlencode( add_query_arg( 'c', $hash, $campaign_url ) ) ) . '?w=600&h=800';

		$response = wp_remote_get( $url, array( 'redirection' => 0, 'method' => 'HEAD' ) );
		$code = wp_remote_retrieve_response_code( $response );

		if ( 307 == $code ) {
			wp_schedule_single_event( time() + 10, 'mailster_auto_post_thumbnail', array( $campaign_id ) );
		}

		if ( 200 != $code ) {
			return false;
		}

		if ( ! function_exists( 'download_url' ) ) {
			include ABSPATH . 'wp-admin/includes/file.php';
		}

		$tmp_file = download_url( $url );

		if ( is_wp_error( $tmp_file ) ) {
			return false;
		}

		$time_string = date( 'Y/m', strtotime( $campaign->post_date ) );

		$wp_upload_dir = wp_upload_dir( $time_string );

		$filename = apply_filters( 'mymail_post_thumbnail_filename', apply_filters( 'mailster_post_thumbnail_filename', 'newsletter-' . $campaign_id, $campaign ), $campaign ) . '.jpg';

		if ( $file_exits = file_exists( $wp_upload_dir['path'] . '/' . $filename ) ) {
			@unlink( $wp_upload_dir['path'] . '/' . $filename );
		}

		$file = array(
			'name' => $filename,
			'type' => 'image/jpeg',
			'tmp_name' => $tmp_file,
			'error' => 0,
			'size' => filesize( $tmp_file ),
		);

		$overrides = array(
			'test_form' => false,
			'test_size' => true,
			'test_upload' => false,
		);

		$results = wp_handle_sideload( $file, $overrides, $time_string );
		$file = $results['file'];

		if ( isset( $results['error'] ) ) {
			return false;
		}

		$filetype = wp_check_filetype( $file, null );

		$attachment = array(
			'guid' => $wp_upload_dir['url'] . '/' . basename( $file ),
			'post_mime_type' => $filetype['type'],
			'post_title' => apply_filters( 'mymail_post_thumbnail_title', apply_filters( 'mailster_post_thumbnail_title', $campaign->post_title, $campaign ), $campaign ),
			'post_content' => '',
			'post_status' => 'inherit',
		);

		if ( ( $post_thumbnail_id = get_post_thumbnail_id( $campaign_id ) ) && $file_exits ) {
			$attachment['ID'] = $post_thumbnail_id;
		}

		$attach_id = wp_insert_attachment( $attachment, $file, $campaign_id );

		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		set_post_thumbnail( $campaign_id, $attach_id );

		return true;

	}


	/**
	 *
	 *
	 * @param unknown $id
	 * @param unknown $file
	 * @param unknown $modules     (optional)
	 * @param unknown $editorstyle (optional)
	 * @return unknown
	 */
	public function get_template_by_id( $id, $file, $modules = true, $editorstyle = false ) {

		$post = get_post( $id );
		// must be a newsletter and have a content
		if ( 'newsletter' == $post->post_type && ! empty( $post->post_content ) ) {
			$html = $post->post_content;

			if ( $editorstyle ) {
				$html = str_replace( '</head>', $this->iframe_script_styles() . '</head>', $html );
			}

			$html = str_replace( ' !DOCTYPE', '!DOCTYPE', $html );

			if ( strpos( $html, 'data-editable' ) ) {

				$templateobj = mailster( 'template' );
				$x = $templateobj->new_template_language( $html );
				$html = $x->saveHTML();

			}
		} elseif ( $post->post_type == 'newsletter' ) {

			$html = $this->get_template_by_slug( $this->get_template(), $file, $modules, $editorstyle );

		} else {

			$html = '';

		}

		return $html;

	}


	/**
	 *
	 *
	 * @param unknown $slug
	 * @param unknown $file        (optional)
	 * @param unknown $modules     (optional)
	 * @param unknown $editorstyle (optional)
	 * @return unknown
	 */
	public function get_template_by_slug( $slug, $file = 'index.html', $modules = true, $editorstyle = false ) {

		$template = mailster( 'template', $slug, $file );
		$html = $template->get( $modules, true );

		if ( $editorstyle ) {
			$html = str_replace( '</head>', $this->iframe_script_styles() . '</head>', $html );
		}

		return $html;

	}


	/**
	 *
	 *
	 * @return unknown
	 */
	private function iframe_script_styles() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'mailster-icons', MAILSTER_URI . 'assets/css/icons' . $suffix . '.css', array(), MAILSTER_VERSION );
		wp_register_style( 'mailster-editor-style', MAILSTER_URI . 'assets/css/editor-style' . $suffix . '.css', array( 'mailster-icons' ), MAILSTER_VERSION );
		wp_register_script( 'mailster-editor-script', MAILSTER_URI . 'assets/js/editor-script' . $suffix . '.js', array( 'jquery' ), MAILSTER_VERSION );

		wp_localize_script( 'mailster-editor-script', 'mailsterdata', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'url' => MAILSTER_URI,
			'_wpnonce' => wp_create_nonce( 'mailster_nonce' ),
			'plupload' => array(
				'runtimes' => 'html5,flash',
				'browse_button' => 'mailster-editorimage-upload-button',
				// 'container' => 'plupload-upload-ui',
				// 'drop_element' => 'drag-drop-area',
				'file_data_name' => 'async-upload',
				'multiple_queues' => true,
				'max_file_size' => wp_max_upload_size() . 'b',
				'url' => admin_url( 'admin-ajax.php' ),
				'flash_swf_url' => includes_url( 'js/plupload/plupload.flash.swf' ),
				'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
				'filters' => array( array( 'title' => __( 'Image files', 'mailster' ), 'extensions' => 'jpg,gif,png' ) ),
				'multipart' => true,
				'urlstream_upload' => true,
				'multipart_params' => array(
					'action' => 'mailster_editor_image_upload_handler',
					'ID' => isset( $_GET['id'] ) ? intval( $_GET['id'] ) : null,
					'_wpnonce' => wp_create_nonce( 'mailster_nonce' ),
				),
				'multi_selection' => false,
			),
		) );
		wp_localize_script( 'mailster-editor-script', 'mailsterL10n', array(
			'ready' => __( 'ready!', 'mailster' ),
			'error' => __( 'error!', 'mailster' ),
			'error_occurs' => __( 'An error occurs while uploading', 'mailster' ),
			'unsupported_format' => __( 'Unsupported file format', 'mailster' ),
		) );

		ob_start();

		wp_print_styles( 'mailster-icons' );
		wp_print_styles( 'mailster-editor-style' );
		wp_print_scripts( 'jquery' );
		wp_print_scripts( 'jquery-ui-draggable' );
		wp_print_scripts( 'jquery-ui-droppable' );
		wp_print_scripts( 'jquery-ui-sortable' );
		wp_print_scripts( 'jquery-touch-punch' );
		wp_print_scripts( 'plupload-all' );
		wp_print_scripts( 'mailster-editor-script' );

		$script_styles = ob_get_contents();

		ob_end_clean();

		return $script_styles;

	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @param unknown $field
	 * @return unknown
	 */
	public function revision_field_post_content( $content, $field ) {

		global $post, $mailster_revisionnow;

		if ( $post->post_type != 'newsletter' ) {
			return $content;
		}

		$data = get_post_meta( $post->ID, 'mailster-data', true );
		$ids = ( isset( $_REQUEST['revision'] ) ) ? array(
			(int) $_REQUEST['revision'],
		) : array(
			(int) $_REQUEST['left'],
			(int) $_REQUEST['right'],
		);

?>
		<tr id="revision-field-<?php echo $field; ?>-preview">
		<th scope="row"><h2>
<?php

if ( ! $mailster_revisionnow && isset( $_REQUEST['left'] ) ) {
	printf( __( 'Older: %s', 'mailster' ), wp_post_revision_title( get_post( $_REQUEST['left'] ) ) );
} elseif ( $mailster_revisionnow && isset( $_REQUEST['right'] ) ) {
	printf( __( 'Newer: %s', 'mailster' ), wp_post_revision_title( get_post( $_REQUEST['left'] ) ) );
} else {
	esc_html_e( 'Preview', 'mailster' );
}
		$mailster_revisionnow = ( ! $mailster_revisionnow ) ? $ids[0] : ( isset( $ids[1] ) ? $ids[1] : $mailster_revisionnow );

?>
		</h2></th>
		<td><iframe id="mailster_iframe" src="<?php echo admin_url( 'admin-ajax.php?action=mailster_get_template&id=' . $post->ID . '&revision=' . $mailster_revisionnow . '&template=&_wpnonce=' . wp_create_nonce( 'mailster_nonce' ) . '&editorstyle=0&nocache=' . time() ); ?>" width="50%" height="640" scrolling="auto" frameborder="0" data-no-lazy=""></iframe></td>
		</tr>
		<?php

		$head = isset( $data['head'] ) ? $data['head'] : null;

		return mailster()->sanitize_content( $content, null, $head );
	}


	/**
	 *
	 *
	 * @param unknown $post_id
	 * @return unknown
	 */
	public function remove_revisions( $post_id ) {

		if ( ! $post_id ) {
			return false;
		}

		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE a FROM $wpdb->posts AS a WHERE a.post_type = '%s' AND a.post_parent = %d", 'revision', $post_id ) );
	}


	/**
	 *
	 *
	 * @param unknown $content
	 * @return unknown
	 */
	private function replace_colors( $content ) {
		// replace the colors
		global $post_id;
		global $post;

		$html = $this->templateobj->get( true );
		$colors = array();
		preg_match_all( '/#[a-fA-F0-9]{6}/', $html, $hits );
		$original_colors = array_unique( $hits[0] );
		$html = $post->post_content;

		if ( ! empty( $html ) && isset( $this->post_data['template'] ) && $this->post_data['template'] == $this->get_template() && $this->post_data['file'] == $this->get_file() ) {
			preg_match_all( '/#[a-fA-F0-9]{6}/', $html, $hits );
			$current_colors = array_unique( $hits[0] );
		} else {
			$current_colors = $original_colors;
		}

		if ( isset( $this->post_data ) && isset( $this->post_data['newsletter_color'] ) ) {

			$search = $replace = array();
			foreach ( $this->post_data['newsletter_color'] as $from => $to ) {

				$to = array_shift( $current_colors );
				if ( $from == $to ) {
					continue;
				}

				$search[] = $from;
				$replace[] = $to;
			}
			$content = str_replace( $search, $replace, $content );
		}

		return $content;

	}


}
