<?php
/**
 * Add Out of Stock Badge section of Product Page > Styles.
 * Prefixes: s -> shop, pp -> product-page, s -> styles
 *
 * @package WordPress
 * @subpackage Jupiter
 * @since 5.9.4
 */

// Out of Stock Badge dialog.
$wp_customize->add_section(
	new MK_Dialog(
		$wp_customize,
		'mk_s_pp_s_outofstock_badge',
		array(
			'mk_belong' => 'mk_s_pp_dialog',
			'mk_tab' => array(
				'id' => 'sh_pp_sty',
				'text' => __( 'Styles', 'mk_framework' ),
			),
			'title' => __( 'Out of Stock Badge', 'mk_framework' ),
			'mk_reset' => 'sh_pp_sty_oos_bdg',
			'priority' => 150,
			'active_callback' => 'mk_cz_hide_section',
		)
	)
);

// Typography.
$wp_customize->add_setting( 'mk_cz[sh_pp_sty_oos_bdg_typography]', array(
	'type' => 'option',
	'default' => array(
		'family' => 'inherit',
		'size' => 14,
		'weight' => 400,
		'style' => 'normal',
		'color' => '#aaaaaa',
	),
	'transport' => 'postMessage',
) );

$wp_customize->add_control(
	new MK_Typography_Control(
		$wp_customize,
		'mk_cz[sh_pp_sty_oos_bdg_typography]',
		array(
			'section' => 'mk_s_pp_s_outofstock_badge',
			'column'  => 'mk-col-12',
		)
	)
);

// Box Model.
// Background color.
$wp_customize->add_setting( 'mk_cz[sh_pp_sty_oos_bdg_background_color]', array(
	'type' => 'option',
	'default'   => 'rgba(0, 0, 0, 0)',
	'transport' => 'postMessage',
) );

$wp_customize->add_control(
	new MK_Color_Control(
		$wp_customize,
		'mk_cz[sh_pp_sty_oos_bdg_background_color]',
		array(
			'section'  => 'mk_s_pp_s_outofstock_badge',
			'column'   => 'mk-col-2-alt',
			'icon'     => 'mk-background-color',
		)
	)
);

// Border radius.
$wp_customize->add_setting( 'mk_cz[sh_pp_sty_oos_bdg_border_radius]', array(
	'type' => 'option',
	'default'   => 0,
	'transport' => 'postMessage',
) );

$wp_customize->add_control(
	new MK_Input_Control(
		$wp_customize,
		'mk_cz[sh_pp_sty_oos_bdg_border_radius]',
		array(
			'section' => 'mk_s_pp_s_outofstock_badge',
			'column'  => 'mk-col-3-alt',
			'icon' => 'mk-corner-radius',
			'unit' => __( 'px', 'mk_framework' ),
			'input_type' => 'number',
			'input_attrs' => array(
				'min' => 0,
			),
		)
	)
);

// Border width.
$wp_customize->add_setting( 'mk_cz[sh_pp_sty_oos_bdg_border_width]', array(
	'type' => 'option',
	'default'   => 2,
	'transport' => 'postMessage',
) );

$wp_customize->add_control(
	new MK_Input_Control(
		$wp_customize,
		'mk_cz[sh_pp_sty_oos_bdg_border_width]',
		array(
			'section' => 'mk_s_pp_s_outofstock_badge',
			'column'  => 'mk-col-3-alt',
			'icon' => 'mk-border',
			'unit' => __( 'px', 'mk_framework' ),
			'input_type' => 'number',
			'input_attrs' => array(
				'min' => 0,
			),
		)
	)
);

// Border color.
$wp_customize->add_setting( 'mk_cz[sh_pp_sty_oos_bdg_border_color]', array(
	'type' => 'option',
	'default'   => '#aaaaaa',
	'transport' => 'postMessage',
) );

$wp_customize->add_control(
	new MK_Color_Control(
		$wp_customize,
		'mk_cz[sh_pp_sty_oos_bdg_border_color]',
		array(
			'section'  => 'mk_s_pp_s_outofstock_badge',
			'column'   => 'mk-col-2-alt mk-col-last',
			'icon'     => 'mk-border-color',
		)
	)
);

// Divider.
$wp_customize->add_setting( 'mk_cz[sh_pp_sty_oos_bdg_divider]', array(
	'type' => 'option',
) );

$wp_customize->add_control(
	new MK_Divider_Control(
		$wp_customize,
		'mk_cz[sh_pp_sty_oos_bdg_divider]',
		array(
			'section' => 'mk_s_pp_s_outofstock_badge',
		)
	)
);

// Box Model.
$wp_customize->add_setting( 'mk_cz[sh_pp_sty_oos_bdg_box_model]', array(
	'type' => 'option',
	'default' => array(
		'margin_top'     => 0,
		'margin_right'   => 0,
		'margin_bottom'  => 0,
		'margin_left'    => 0,
		'padding_top'    => 12,
		'padding_right'  => 20,
		'padding_bottom' => 12,
		'padding_left'   => 20,
	),
	'transport' => 'postMessage',
) );

$wp_customize->add_control(
	new MK_Box_Model_Control(
		$wp_customize,
		'mk_cz[sh_pp_sty_oos_bdg_box_model]',
		array(
			'section' => 'mk_s_pp_s_outofstock_badge',
			'column'  => 'mk-col-12',
		)
	)
);
