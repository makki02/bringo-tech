<?php
/*
 * The7 elements product add to cart widget for Elementor.
 *
 * @package The7
 */

namespace The7\Mods\Compatibility\Elementor\Widgets\Woocommerce;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\WC_Widget_Nav;
use The7\Mods\Compatibility\Elementor\Pro\Modules\Woocommerce\Woocommerce_Support;
use The7\Mods\Compatibility\Elementor\The7_Elementor_Widget_Base;
use WC_Query;

defined( 'ABSPATH' ) || exit;

class Filter_Attribute extends The7_Elementor_Widget_Base {

	public function get_name() {
		return 'the7-woocommerce-filter-attribute';
	}

	public function get_categories() {
		return [ 'woocommerce-elements-single', 'woocommerce-elements-archive' ];
	}

	public function get_style_depends() {
		return $this->getDepends();
	}

	private function getDepends() {
		// css and js use the same names
		$ret = [ 'the7-woocommerce-filter-attribute', 'the7-custom-scrollbar' ];
		if ( ! Plugin::$instance->preview->is_preview_mode() ) {
			$settings = $this->get_settings_for_display();
			if ( $settings['navigation'] !== 'scroll' ) {
				unset( $ret['the7-custom-scrollbar'] );
			}
		}

		return $ret;
	}

	public function get_script_depends() {
		return $this->getDepends();
	}

	protected function the7_title() {
		return esc_html__( 'Filter By Attribute', 'the7mk2' );
	}

	protected function the7_icon() {
		return 'eicon-table-of-contents';
	}

	protected function the7_keywords() {
		return [ 'woocommerce', 'shop', 'store', 'cart', 'product', 'filter', 'attribute' ];
	}

	protected function register_controls() {
		// Content Tab.
		$this->add_title_area_content_controls();
		$this->add_attributes_content_controls();

		// styles tab
		$this->add_title_styles();
		$this->add_box_attributes_styles();
		$this->add_filter_indicator_styles_controls();
		$this->add_item_count_styles_controls();
		$this->add_box_styles_controls();
		$this->add_more_button_styles_controls();
	}

	protected function add_title_area_content_controls() {
		$this->start_controls_section(
			'title_area_section',
			[
				'label' => esc_html__( 'Title Area', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'   => esc_html__( 'Widget Title', 'the7mk2' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Widget Title', 'the7mk2' ),
			]
		);

		$this->add_control(
			'toggle',
			[
				'label'        => esc_html__( 'Widget Toggle', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'On', 'the7mk2' ),
				'label_off'    => esc_html__( 'Off', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_closed_by_default',
			[
				'label'        => esc_html__( 'Closed By Default', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'closed',
				'default'      => '',
				'condition'    => [
					'toggle!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'            => esc_html__( 'Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-chevron-down',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-down',
						'angle-down',
						'angle-double-down',
						'caret-down',
						'caret-square-down',
					],
					'fa-regular' => [
						'caret-square-down',
					],
				],
				'label_block'      => false,
				'skin'             => 'inline',
				'condition'        => [
					'toggle!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->add_control(
			'toggle_active_icon',
			[
				'label'            => esc_html__( 'Active Icon', 'the7mk2' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default'          => [
					'value'   => 'fas fa-chevron-up',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid'   => [
						'chevron-up',
						'angle-up',
						'angle-double-up',
						'caret-up',
						'caret-square-up',
					],
					'fa-regular' => [
						'caret-square-up',
					],
				],
				'skin'             => 'inline',
				'label_block'      => false,
				'condition'        => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
					'title_text!' => '',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function add_attributes_content_controls() {
		$this->start_controls_section(
			'attributes_section',
			[
				'label' => esc_html__( 'Attributes', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$attr    = $this->get_attributes();
		$default = '';
		if ( ! empty( array_keys( $attr )[0] ) ) {
			$default = array_keys( $attr )[0];
		}
		$this->add_control(
			'attr_name',
			[
				'label'   => esc_html__( 'Attributes', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $attr,
				'default' => $default,
			]
		);

		$this->add_control(
			'attr_query_type',
			[
				'label'   => esc_html__( 'Query Type', 'the7mk2' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'and' => esc_html__( 'AND', 'the7mk2' ),
					'or'  => esc_html__( 'OR', 'the7mk2' ),
				],
				'default' => 'and',
			]
		);

		$this->add_control(
			'normal_filter_indicator_icon_show',
			[
				'label'        => esc_html__( 'Inactive Filter Indicator', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'active_filter_indicator_icon_show',
			[
				'label'        => esc_html__( 'Active Filter Indicator', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'items_count',
			[
				'label'        => esc_html__( 'Items Count', 'the7mk2' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'the7mk2' ),
				'label_off'    => esc_html__( 'No', 'the7mk2' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'layout',
			[
				'label'                => esc_html__( 'Layout', 'the7mk2' ),
				'type'                 => Controls_Manager::SELECT,
				'options'              => [
					'grid'   => esc_html__( 'Grid', 'the7mk2' ),
					'inline' => esc_html__( 'Inline', 'the7mk2' ),
				],
				'separator'            => 'before',
				'default'              => 'grid',
				'prefix_class'         => 'filter-layout-',
				'selectors'            => [
					'{{WRAPPER}} .filter-nav' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'grid'   => 'display: grid',
					'inline' => 'display: flex; flex-wrap: wrap;',
				],
			]
		);

		$this->add_basic_responsive_control(
			'grid_columns',
			[
				'label'          => esc_html__( 'Number Of Columns', 'the7mk2' ),
				'type'           => Controls_Manager::NUMBER,
				'default'        => 1,
				'mobile_default' => 1,
				'min'            => 1,
				'max'            => 6,
				'condition'      => [
					'layout' => 'grid',
				],
				'selectors'      => [
					'{{WRAPPER}} .filter-nav' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_margin',
			[
				'label'      => esc_html__( 'Margins', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'top'      => '0',
					'right'    => '10',
					'bottom'   => '10',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .filter-nav-item-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition'  => [
					'layout!' => 'grid',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_row_space',
			[
				'label'     => esc_html__( 'Row Gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}}  .filter-nav' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'layout' => 'grid',
				],
			]
		);

		$this->add_responsive_control(
			'box_column_space',
			[
				'label'     => esc_html__( 'Column Gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .filter-nav' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				],
				'default'   => [
					'size' => 10,
				],
				'condition' => [
					'layout' => 'grid',
				],
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'     => esc_html__( 'Widget Navigation', 'the7mk2' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'disabled'    => esc_html__( 'Disabled', 'the7mk2' ),
					'scroll'      => esc_html__( 'Scroll', 'the7mk2' ),
					'more_button' => esc_html__( 'Show more items', 'the7mk2' ),
				],
				'separator' => 'before',
				'default'   => 'disabled',
			]
		);

		$this->add_responsive_control(
			'navigation_max_height',
			[
				'label'     => esc_html__( 'Maximum Height', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'condition' => [
					'navigation' => 'scroll',
				],
				'default'   => [
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .filter-container' => 'max-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'navigation_items',
			[
				'label'     => esc_html__( 'Visible Number Of Attributes', 'the7mk2' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5,
				'min'       => 1,
				'max'       => 50,
				'condition' => [
					'navigation' => 'more_button',
				],
			]
		);

		$this->add_control(
			'navigation_items_more_button_text',
			[
				'label'     => esc_html__( 'Show More Items Text', 'the7mk2' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( '+%s more', 'the7mk2' ),
				'condition' => [
					'navigation' => 'more_button',
				],
			]
		);

		$this->add_control(
			'navigation_items_more_button_text_description',
			[
				'raw'             => esc_html__( 'Use "%s" to display the number of items. Example: +%s more', 'the7mk2' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
				'condition'       => [
					'navigation' => 'more_button',
				],
			]
		);
		$this->end_controls_section();
	}

	public function get_attributes() {
		$attribute_array      = array();
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
					$attribute_array[ $tax->attribute_name ] = $tax->attribute_name;
				}
			}
		}

		return $attribute_array;
	}

	protected function add_title_styles() {
		$this->start_controls_section(
			'title_section',
			[
				'label'     => esc_html__( 'Widget Title Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title_text!' => '',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-title';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => $selector,
			]
		);


		$this->add_responsive_control(
			'title_arrow_size',
			[
				'label'     => esc_html__( 'Toggle Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'   => [
					'size' => 16,
				],
				'condition' => [
					'toggle!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .filter-toggle-icon .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-header';

		$this->add_responsive_control( 'title_padding', [
			'label'      => esc_html__( 'Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'title_margin', [
			'label'      => esc_html__( 'Margins', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'title_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'title_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector,
			'exclude'  => [ 'color' ],
		] );

		$this->start_controls_tabs(
			'title_arrow_tabs_style'
		);

		$this->start_controls_tab(
			'normal_title_arrow_style',
			[
				'label' => esc_html__( 'Closed', 'the7mk2' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_arrow_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-closed svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control( 'title_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .filter-header'     => 'background: {{VALUE}};',
			],
		] );

		$this->add_control(
			'title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .filter-header' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_title_arrow_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.fix) .filter-header:hover .filter-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_title_arrow_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header:hover .filter-toggle-icon .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control( 'hover_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .the7-product-filter:not(.fix) .filter-header:hover'  => 'background: {{VALUE}};',
			],
		] );

		$this->add_control(
			'hover_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.fix) .filter-header:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_title_arrow_style',
			[
				'label' => esc_html__( 'Open', 'the7mk2' ),
			]
		);

		$this->add_control(
			'active_title_color',
			[
				'label'     => esc_html__( 'Title Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.closed) .filter-header .filter-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_title_arrow_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active i'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-header .filter-toggle-icon .filter-toggle-active svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
				'condition' => [
					'toggle!'             => '',
					'toggle_icon[value]!' => '',
				],
			]
		);

		$this->add_control( 'active_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .the7-product-filter:not(.closed) .filter-header'     => 'background: {{VALUE}};',
			],
		] );

		$this->add_control(
			'active_title_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .the7-product-filter:not(.closed) .filter-header' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();
	}

	protected function add_filter_indicator_styles_controls() {
		$this->start_controls_section(
			'filter_indicator_section',
			[
				'label'      => esc_html__( 'Filter Indicator', 'the7mk2' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'  => 'normal_filter_indicator_icon_show',
							'value' => 'yes',
						],
						[
							'name'  => 'active_filter_indicator_icon_show',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$icon_selector = '{{WRAPPER}} .filter-nav-item-container .indicator';

		$this->add_control(
			'filter_indicator_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'toggle'               => false,
				'selectors'            => [
					'{{WRAPPER}} .filter-nav-item-container .indicator' => 'order: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'left'  => 0,
					'right' => 1,
				],
				'prefix_class'         => 'filter-indicator-align-',
			]
		);

		$this->add_responsive_control(
			'filter_indicator_icon_size',
			[
				'label'     => esc_html__( 'Icon Size', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					$icon_selector . ' .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filter_indicator_padding',
			[
				'label'      => esc_html__( 'Icon Padding', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					$icon_selector => 'padding: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'filter_indicator_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					$icon_selector => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'filter_indicator_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$icon_selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);


		$this->add_control(
			'normal_indicator_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Inactive State', 'the7mk2' ),
				'separator' => 'before',
				'condition' => [
					'normal_filter_indicator_icon_show' => 'yes',
				],
			]
		);

		$this->add_filter_indicator_tabs_controls( 'normal_' );

		$this->add_control(
			'active_indicator_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Active State', 'the7mk2' ),
				'separator' => 'before',
				'condition' => [
					'active_filter_indicator_icon_show' => 'yes',
				],
			]
		);

		$this->add_filter_indicator_tabs_controls( 'active_' );
		$this->add_responsive_control(
			'filter_indicator_space',
			[
				'label'     => esc_html__( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}.filter-indicator-align-left  .filter-nav-item-container .indicator' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.filter-indicator-align-right .filter-nav-item-container .indicator' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_filter_indicator_tabs_controls( $prefix ) {
		$active_class = ':not(.active)';
		if ( $prefix === 'active_' ) {
			$active_class = '.active';
		}

		$selector = '{{WRAPPER}} .filter-nav-item' . $active_class . ' .filter-nav-item-container .indicator';

		$this->start_controls_tabs(
			$prefix . 'indicator_tabs',
			[
				'condition' => [
					$prefix . 'filter_indicator_icon_show!' => '',
				],
			]
		);

		$this->start_controls_tab(
			$prefix . 'filter_indicator_tab',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$def_icon = [];
		if ( $prefix === 'active_' ) {
			$def_icon['default'] = [
				'value'   => 'fas fa-check',
				'library' => 'fa-solid',
			];
		}

		$this->add_control(
			$prefix . 'filter_indicator_icon',
			array_merge(
				[
					'label'       => esc_html__( 'Icon', 'the7mk2' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => false,
					'skin'        => 'inline',
				],
				$def_icon
			)
		);

		$this->add_control(
			$prefix . 'filter_indicator_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector . ' .elementor-icon'     => 'color: {{VALUE}};',
					$selector . ' .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'filter_indicator_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'filter_indicator_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			$prefix . 'filter_indicator_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			$prefix . 'filter_indicator_hover_icon',
			array_merge(
				[
					'label'       => esc_html__( 'Icon', 'the7mk2' ),
					'type'        => Controls_Manager::ICONS,
					'label_block' => false,
					'skin'        => 'inline',
				],
				$def_icon
			)
		);

		$helper_indicator_class = '.the7-product-attr-filter.anim-disp-normal-indicator';
		if ( $prefix === 'active_' ) {
			$helper_indicator_class = '.the7-product-attr-filter.anim-disp-active-indicator';
		}

		$hov_selector = '{{WRAPPER}} ' . $helper_indicator_class . ' .filter-nav-item' . $active_class . ' .filter-nav-item-container:hover .indicator';
		$this->add_control(
			$prefix . 'filter_indicator_hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector . ' .elementor-icon.indicator-hover' => 'color: {{VALUE}};',
					$selector . ' .elementor-icon.indicator-hover svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
					$hov_selector . ' .elementor-icon'     => 'color: {{VALUE}};',
					$hov_selector . ' .elementor-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}};',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-nav-item' . $active_class . ' .filter-nav-item-container:hover .indicator';

		$this->add_control(
			$prefix . 'filter_indicator_hover_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix . 'filter_indicator_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	protected function add_item_count_styles_controls() {
		$this->start_controls_section(
			'item_count_section',
			[
				'label'     => esc_html__( 'Items Count', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'items_count!' => '',
				],
			]
		);

		$selector = '{{WRAPPER}} .the7-product-attr-filter .filter-nav-item-container .count';

		$this->add_control(
			'item_count_align',
			[
				'label'                => esc_html__( 'Alignment', 'the7mk2' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'  => [
						'title' => esc_html__( 'Start', 'the7mk2' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'End', 'the7mk2' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'              => is_rtl() ? 'right' : 'left',
				'toggle'               => false,
				'selectors'            => [
					'{{WRAPPER}} .filter-nav-item-container' => '{{VALUE}}',
				],
				'selectors_dictionary' => [
					'right' => 'justify-content: space-between',
				],
				'prefix_class'         => 'filter-count-align-',
				'condition'            => [
					'layout!' => 'inline',
				],
			]
		);

		$this->add_control(
			'item_count_align_hidden',
			[
				'label'        => esc_html__( 'Alignment', 'the7mk2' ),
				'type'         => Controls_Manager::HIDDEN,
				'default'      => 'left',
				'prefix_class' => 'filter-count-align-',
				'condition'    => [
					'layout' => 'inline',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_count_typography',
				'selector' => $selector,
			]
		);

		$this->add_control(
			'item_count_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'selectors'  => [
					$selector => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_count_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'item_count_min_width',
			[
				'label'      => esc_html__( 'Min Width', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'item_count_min_height',
			[
				'label'      => esc_html__( 'Min Height', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'  => [
					$selector     => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'item_count_tabs_style' );
		$this->add_items_count_tab_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_items_count_tab_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->add_items_count_tab_controls( 'active_', esc_html__( 'Active', 'the7mk2' ) );
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'item_count_space',
			[
				'label'     => esc_html__( 'Spacing', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'separator'  => 'before',
				'selectors' => [
					$selector => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_items_count_tab_controls( $prefix_name, $box_name ) {
		$extra_class = '';
		if ( $prefix_name === 'active_' ) {
			$extra_class .= '.active';
		}/*
		else {
			$extra_class .= ':not(.active)';
		}*/

		$isHover = '';
		if ( $prefix_name === 'hover_' ) {
			$isHover = ':hover';
		}

		$selector = '{{WRAPPER}} .filter-nav-item:not(.fix)' . $extra_class . ' .filter-nav-item-container' . $isHover . ' .count';

		$this->start_controls_tab(
			$prefix_name . 'item_count_style',
			[
				'label' => $box_name,
			]
		);

		$this->add_control(
			$prefix_name . 'item_count_color',
			[
				'label'     => esc_html__( 'Text  Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'item_count_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'item_count_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
	}

	protected function add_box_attributes_styles() {
		$this->start_controls_section(
			'container_section',
			[
				'label'     => esc_html__( 'Widget Content Area', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '{{WRAPPER}} .filter-container';

		$this->add_responsive_control( 'container_padding', [
			'label'      => esc_html__( 'Padding', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'container_margin', [
			'label'      => esc_html__( 'Margins', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'range'      => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'top'      => '15',
				'right'    => '0',
				'bottom'   => '0',
				'left'     => '0',
				'unit'     => 'px',
				'isLinked' => false,
			],
			'selectors'  => [
				$selector => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
			],
		] );

		$this->add_responsive_control( 'container_border_radius', [
			'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => 'container_border',
			'label'    => esc_html__( 'Border', 'the7mk2' ),
			'selector' => $selector,
		] );

		$this->add_control( 'container_bg_color', [
			'label'     => esc_html__( 'Background Color', 'the7mk2' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$selector => 'background: {{VALUE}};',
			],
		] );

		$this->end_controls_section();
	}

	protected function add_box_styles_controls() {
		$this->start_controls_section(
			'box_section',
			[
				'label' => esc_html__( 'Box', 'the7mk2' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$selector = '{{WRAPPER}}  .filter-nav-item-container';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'box_text_typography',
				'selector' => $selector . ' .name',
			]
		);

		$this->start_controls_tabs( 'box_tabs_style' );
		$this->add_box_tab_controls( 'normal_', esc_html__( 'Normal', 'the7mk2' ) );
		$this->add_box_tab_controls( 'hover_', esc_html__( 'Hover', 'the7mk2' ) );
		$this->add_box_tab_controls( 'active_', esc_html__( 'Active', 'the7mk2' ) );
		$this->end_controls_tabs();

		$this->add_control(
			'box_border_width',
			[
				'label'      => esc_html__( 'Border Width', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					$selector => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'the7mk2' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_basic_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Paddings', 'the7mk2' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);
		$this->end_controls_section();
	}

	protected function add_box_tab_controls( $prefix_name, $box_name ) {
		$extra_class = '';

		$isHover = '';
		if ( $prefix_name === 'hover_' ) {
			$extra_class .= ':not(.fix)';
			$isHover      = ':hover';
		} elseif ( $prefix_name === 'active_' ) {
			$extra_class .= '.active';
		} else {
			$extra_class .= ':not(.active)';
		}
		$selector = '{{WRAPPER}} .filter-nav-item' . $extra_class . ' .filter-nav-item-container' . $isHover;

		$this->start_controls_tab(
			$prefix_name . 'box_style',
			[
				'label' => $box_name,
			]
		);
		$this->add_control(
			$prefix_name . 'box_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector . ' .name' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'box_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$prefix_name . 'box_border_color',
			[
				'label'     => esc_html__( 'Border color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
	}

	protected function add_more_button_styles_controls() {
		$this->start_controls_section(
			'more_button_section',
			[
				'label'     => esc_html__( 'Show More Items', 'the7mk2' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => 'more_button',
				],
			]
		);

		$selector = '{{WRAPPER}} .filter-container .filter-show-more';

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'more_button_typography',
				'selector' => $selector,
			]
		);

		$this->start_controls_tabs( 'more_button_tabs_style' );

		$this->start_controls_tab(
			'normal_more_button_style',
			[
				'label' => esc_html__( 'Normal', 'the7mk2' ),
			]
		);

		$this->add_control(
			'more_button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector . ' span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover_more_button_style',
			[
				'label' => esc_html__( 'Hover', 'the7mk2' ),
			]
		);

		$this->add_control(
			'hover_more_button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'the7mk2' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => true,
				'default'   => '',
				'selectors' => [
					$selector . ':hover span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'more_button_space',
			[
				'label'     => esc_html__( 'Gap', 'the7mk2' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					$selector => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! $this->isPreview() && ! is_shop() && ! is_product_taxonomy() ) {
			return;
		}
		Woocommerce_Support::add_fake_wc_query();

		$widgetNav = new WC_Widget_Nav();
		$settings  = $this->get_settings_for_display();

		$instance['attribute'] = $settings['attr_name'];
		$taxonomy              = $widgetNav->get_instance_taxonomy( $instance );
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return;
		}
		$terms = get_terms( $taxonomy, array( 'hide_empty' => '1' ) );



		if ( 0 === count( $terms ) ) {
			return;
		}

		$this->add_render_attribute( 'product-attr-filter', 'class', 'the7-product-attr-filter the7-product-filter' );
		$this->add_render_attribute( 'product-attr-filter', 'class', 'filter-navigation-' . $settings['navigation'] );
		if ( $settings['toggle'] == 'yes' ) {
			$this->add_render_attribute( 'product-attr-filter', 'class', 'collapsible' );
			$this->add_render_attribute( 'product-attr-filter', 'class', $settings['toggle_closed_by_default'] );
			if ( $settings['toggle_closed_by_default'] ) {
				$this->add_render_attribute( 'filter-container', 'style', 'display:none' );
			}
		}

		if ( $settings['navigation'] === 'scroll' ) {
			$this->add_render_attribute( 'product-attr-filter', 'class', 'the7-scrollbar-style' );
		}

		$this->add_indicator_anim_attribute( $settings, 'normal' );
		$this->add_indicator_anim_attribute( $settings, 'active' );

		$this->add_render_attribute( 'filter-title', 'class', 'filter-title' );
		if ( empty( $settings['title_text'] ) ) {
			$this->add_render_attribute( 'filter-title', 'class', 'empty' );
		}

		$this->add_render_attribute( 'filter-container', 'class', 'filter-container' );
		ob_start();
		?>
		<div <?php echo $this->get_render_attribute_string( 'product-attr-filter' ); ?>>
			<div class="filter-header widget-title">
				<div <?php echo $this->get_render_attribute_string( 'filter-title' ); ?>>
					<?php echo esc_html( $settings['title_text'] ); ?>
				</div>
				<?php if ( ! empty( $settings['toggle_icon']['value'] ) ) : ?>
					<div class="filter-toggle-icon">
						<span class="elementor-icon filter-toggle-closed">
							<?php Icons_Manager::render_icon( $settings['toggle_icon'] ); ?>
						</span>
						<?php if ( ! empty( $settings['toggle_active_icon']['value'] ) ) : ?>
							<span class="elementor-icon filter-toggle-active">
								<?php Icons_Manager::render_icon( $settings['toggle_active_icon'] ); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			<div <?php echo $this->get_render_attribute_string( 'filter-container' ); ?> >
				<?php $found = $this->display_items( $widgetNav, $terms, $taxonomy, $settings ); ?>
			</div>
		</div>
		<?php
		if ( ! $found ) {
			ob_end_clean();
		} else {
			echo ob_get_clean();
		}
	}

	protected function add_indicator_anim_attribute( $settings, $prefix ) {
		if ( $settings[ $prefix . '_filter_indicator_icon_show' ] === 'yes' ) {
			$normal_icon   = $settings[ $prefix . '_filter_indicator_icon' ] ['value'];
			$hover_icon    = $settings[ $prefix . '_filter_indicator_hover_icon' ] ['value'];
			$add_animate   = false;
			$has_animation = false;
			if ( ! empty( $normal_icon ) && ! empty( $hover_icon ) && $normal_icon == $hover_icon ) {
				$this->add_render_attribute( 'product-attr-filter', 'class', 'anim-disp-' . $prefix . '-indicator' );
				$has_animation = true;
			} elseif ( empty( $normal_icon ) && ! empty( $hover_icon ) ) {
				$add_animate = true;
			} elseif ( ! empty( $normal_icon ) && empty( $hover_icon ) ) {
				$add_animate = true;
			}
			if ( $add_animate ) {
				$has_animation = true;
				$this->add_render_attribute( 'product-attr-filter', 'class', 'anim-trans-' . $prefix . '-indicator' );
			}
			if ( ! $has_animation ) {
				$this->add_render_attribute( 'product-attr-filter', 'class', 'anim-off-' . $prefix . '-indicator' );
			}
		}
	}

	protected function display_items( WC_Widget_Nav $widgetNav, $terms, $taxonomy, $settings ) {
		?>
		<ul class="filter-nav">
			<?php
			$query_type         = $settings['attr_query_type'];
			$term_counts        = $widgetNav->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), $taxonomy, $query_type );
			$_chosen_attributes = WC_Query::get_layered_nav_chosen_attributes();
			$found              = false;
			$base_link          = $widgetNav->get_current_page_url();

			if ( is_wp_error( $base_link ) ) {
				$base_link = (string) get_permalink( wc_get_page_id( 'shop' ) );
			}

            if ( isset( $_GET['taxonomy'] ) ) {
                $base_link = add_query_arg('taxonomy', wc_clean(wp_unslash($_GET['taxonomy'])), $base_link);
            }

            if ( isset( $_GET['term'] ) ) {
                $base_link = add_query_arg('term', wc_clean(wp_unslash($_GET['term'])), $base_link);
            }

            $term_items = 0;
			foreach ( $terms as $term ) {
				$current_values = isset( $_chosen_attributes[ $taxonomy ]['terms'] ) ? $_chosen_attributes[ $taxonomy ]['terms'] : array();
				$option_is_set  = in_array( $term->slug, $current_values, true );
				$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

				// Skip the term for the current archive.
				if ( $widgetNav->get_current_term_id() === $term->term_id ) {
					continue;
				}

				// Only show options with count > 0.
				if ( 0 < $count ) {
					$found = true;
				} elseif ( 0 === $count && ! $option_is_set ) {
					continue;
				}

				$term_items ++;
				$filter_name    = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
				$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( wp_unslash( $_GET[ $filter_name ] ) ) ) : array();
				$current_filter = array_map( 'sanitize_title', $current_filter );

				if ( ! in_array( $term->slug, $current_filter, true ) ) {
					$current_filter[] = $term->slug;
				}

				$link = remove_query_arg( $filter_name, $base_link );

				// Add current filters to URL.
				foreach ( $current_filter as $key => $value ) {
					// Exclude query arg for current term archive term.
					if ( $value === $widgetNav->get_current_term_slug() ) {
						unset( $current_filter[ $key ] );
					}

					// Exclude self so filter can be unset on click.
					if ( $option_is_set && $value === $term->slug ) {
						unset( $current_filter[ $key ] );
					}
				}

				if ( ! empty( $current_filter ) ) {
					asort( $current_filter );
					$link = add_query_arg( $filter_name, implode( ',', $current_filter ), $link );

					// Add Query type Arg to URL.
					if ( 'or' === $query_type && ! ( 1 === count( $current_filter ) && $option_is_set ) ) {
						$link = add_query_arg( 'query_type_' . wc_attribute_taxonomy_slug( $taxonomy ), 'or', $link );
					}
					$link = str_replace( '%2C', ',', $link );
				}
				ob_start();
				if ( $count > 0 || $option_is_set ) {
					$link = apply_filters( 'the7_filter_widget_nav_link', $link, $term, $taxonomy );

					?>
					<a href="<?php echo esc_url( $link ); ?>">
						<?php
						$prefix = 'normal';
						if ( $option_is_set ) {
							$prefix = 'active';
						}
						$this->displayFilterIndicator( $settings, $prefix );
						?>
						<span class="name"><?php echo esc_html( $term->name ); ?></span>
					</a>
					<?php
				} else {
					$link = false;
					?>
					<span class="name"><?php echo esc_html( $term->name ); ?></span>
					<?php
				}

				if ( $settings['items_count'] == 'yes' ) {
					echo apply_filters( 'woocommerce_layered_nav_count', '<span class="count">' . absint( $count ) . '</span>', $count, $term );
				}
				$term_html = ob_get_clean();

				$this->add_render_attribute( 'filter-nav-item' . $term_items, 'class', 'filter-nav-item' );

				if ( $settings['navigation'] == 'more_button' ) {
					if ( $term_items <= $settings['navigation_items'] ) {
						$this->add_render_attribute( 'filter-nav-item' . $term_items, 'class', 'show' );
					} else {
						$this->add_render_attribute( 'filter-nav-item' . $term_items, 'style', 'display:none' );
					}
				} else {
					$this->add_render_attribute( 'filter-nav-item' . $term_items, 'class', 'show' );
				}
				if ( $option_is_set ) {
					$this->add_render_attribute( 'filter-nav-item' . $term_items, 'class', 'active' );
				}
				?>
				<li <?php echo $this->get_render_attribute_string( 'filter-nav-item' . $term_items ); ?>>
					<div class="filter-nav-item-container">
						<?php echo apply_filters( 'the7_filter_nav_term_html', $term_html, $term, $link, $count ); ?>
					</div>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
		if ( $settings['navigation'] == 'more_button' ) {
			if ( $term_items > $settings['navigation_items'] ) {
				$items_lasts = $term_items - $settings['navigation_items'];
				?>
				<div class="filter-show-more">
					<span>
						<?php
						$more_button_text = $settings['navigation_items_more_button_text'];

						// Sanitize format string.
						$placeholders = [
							'%s',
							'%1$s',
						];
						foreach ( $placeholders as $placeholder ) {
							if ( strpos( $more_button_text, $placeholder ) !== false ) {
								$more_button_text = sprintf( $more_button_text, $items_lasts );
								break;
							}
						}

						echo esc_html( $more_button_text );
						?>
					</span>
				</div>
				<?php
			}
		}

		return $found;
	}

	protected function displayFilterIndicator( $settings, $prefix ) {
		if ( $settings[ $prefix . '_filter_indicator_icon_show' ] === 'yes' ) {
			?>
			<div class="indicator">
				<span class="elementor-icon indicator-normal">
					<?php
					if ( empty( $settings[ $prefix . '_filter_indicator_icon' ] ['value'] ) ) {
						?>
						 <i class="empty-icon"></i>
						<?php
					} else {
						Icons_Manager::render_icon( $settings[ $prefix . '_filter_indicator_icon' ] );
					}
					?>
				</span>
				<span class="elementor-icon indicator-hover">
					<?php
					if ( empty( $settings[ $prefix . '_filter_indicator_hover_icon' ] ['value'] ) ) {
						?>
						<i class="empty-icon"></i>
						<?php
					} else {
						Icons_Manager::render_icon( $settings[ $prefix . '_filter_indicator_hover_icon' ] );
					}
					?>
				</span>
			</div>
			<?php
		}
	}

	private function isPreview() {
		return $this->is_preview_mode() || Plugin::$instance->editor->is_edit_mode();
	}
}
