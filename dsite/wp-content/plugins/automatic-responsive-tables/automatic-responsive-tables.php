<?php
/*
Plugin Name: Automatic Responsive Tables 
Plugin URI:  plugins.rockwellgrowth.com/responsive-tables
Description: Automatically or Manually convert all tables on your site to be responsive
Version:     1.2
Author:      Andrew Rockwell
Author URI:  http://www.rockwellgrowth.com/
License:     GPL2v2
*/

defined( 'ABSPATH' ) or die( 'Plugin file cannot be accessed directly.' );

if( !class_exists('responsive_tables') ) {
	class responsive_tables {

		protected $tag = 'responsive_tables_opt';
		protected $main_settings = array(
			'activate_all' => array(
				'title' => 'Activate for all Tables',
				'description' => 'Checking this option will automatically convert every table on your site to be responsive. <br /> Unchecked will give the ability to specify which tables you\'d like converted by class or id',
				'type' => 'checkbox'
			),
			'classes' => array(
				'title' => 'Classes &amp; IDs',
				'description' => 'Add the Classes or IDs of the tables that you would like to be converted to be responsive. <br /> ( separated by commas )',
				'type' => 'text',
				'placeholder' => '.class, #id'
			),
			'breakpoint' => array(
				'title' => 'Add breakpoint for the responsive table',
				'description' => 'This number denotes the responsive breakpoint for the tables ( in pixels )',
				'placeholder' => '320',
				'type' => 'number'
			),
			'default_styling' => array(
				'title' => 'Add styling',
				'description' => 'This is recommended to make your table easily readable, unless you intend on adding CSS yourself.',
				'type' => 'checkbox'
			),

			'table_border_color' => array(
				'title' => 'Table Border Color',
				'type' => 'color',
				'id_name' => 'table_border_color',
				'placeholder' => '#cccccc'
			),
			'cell_border_color' => array(
				'title' => 'Cell Border Color',
				'type' => 'color',
				'id_name' => 'cell_border_color',
				'placeholder' => '#dddddd'
			),
			'odd_row_color' => array(
				'title' => 'Odd Row Color',
				'type' => 'color',
				'id_name' => 'odd_row_color',
				'placeholder' => '#efefef'
			),
			'even_row_color' => array(
				'title' => 'Even Row Color',
				'type' => 'color',
				'id_name' => 'even_row_color',
				'placeholder' => '#ffffff'
			),
			'header_value_divider' => array(
				'title' => 'Left/ Right Divider',
				'type' => 'checkbox'
			),

			'header_font' => array(
				'title' => 'Header Font Size',
				'placeholder' => '16',
				'type' => 'font'
			),
			'value_font' => array(
				'title' => 'Value Font Size',
				'placeholder' => '16',
				'type' => 'font'
			),

		);

		public function __construct() {
			if ( $options = get_option( $this->tag ) ) {
				$this->options = $options;
			}
			if( is_admin() ) {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			}
		}

		public function admin_init() {
			$section = 'main_settings';
			$section_id = $this->tag . '_' . $section;
			$section_title = 'Main Settings';

			add_settings_section(
				$section_id,
				$section_title,
				null,
				$section
			);

			foreach ( $this->main_settings as $id => $options ) {
				$options['id'] = $id;
				add_settings_field(
					$this->tag . '_' . $id . '_' . $section,
					$options['title'],
					array( &$this, 'settings_field' ),
					$section,
					$section_id,
					$options
				);
			}

			register_setting(
				$section,
				$this->tag,
				array( &$this, 'settings_validate' )
			);
		}

		public function settings_field( $field_opts ) {
			$main_options = get_option( 'responsive_tables_opt' );

			$placeholder = '';
			if( isset( $field_opts['placeholder'] ) && $field_opts['placeholder'] != '' ) {
				$placeholder = ' placeholder="' . $field_opts['placeholder'] . '"';
			}

			$value = '';
			if( isset( $main_options[$field_opts['id']] ) ) {
				$value = ' value="' . $main_options[$field_opts['id']] . '"';
			}
			if( $field_opts['type'] == 'color' && $main_options[$field_opts['id']] == '' ) {
				$value = ' value="' . $field_opts['placeholder'] . '"';
			}

			if( $field_opts['type'] == 'checkbox' ) {
				$checked = '';
				if( isset( $main_options[ $field_opts['id'] ] ) ) {
					$checked = ' checked="checked"';
				}
				echo '<input type="checkbox" id="' . $field_opts['id'] . '" name="' . $this->tag . '[' . $field_opts['id'] . ']' . '"' . $checked . ' >';
				if( isset($field_opts['description']) ) {
					echo '<p class="description">' . $field_opts['description'] . '</p>';
				}
			} elseif( $field_opts['type'] == 'number' ) {
				echo '<input type="number"  name="' . $this->tag . '[' . $field_opts['id'] . ']"' . $value . $placeholder . ' >';
				echo '<p class="description">' . $field_opts['description'] . '</p>';
			} elseif( $field_opts['type'] == 'font' ) {
				echo '<input type="number" id="' . $field_opts['id'] . '" name="' . $this->tag . '[' . $field_opts['id'] . ']"' . $value . $placeholder . ' >';
			} elseif( $field_opts['type'] == 'color' ) {
				// echo '<input type="color" class="my-color-field" name="' . $this->tag . '[' . $field_opts['id'] . ']"' . $value . ' >';
				echo '<input type="text" id="' . $field_opts['id_name'] . '"  name="' . $this->tag . '[' . $field_opts['id'] . ']"' . $value . ' class="cpa-color-picker" >';
			} else {
				echo '<input type="text" name="' . $this->tag . '[' . $field_opts['id'] . ']"' . $value . $placeholder . ' >';
				if( isset($field_opts['description']) ) {
					echo '<p class="description">' . $field_opts['description'] . '</p>';
				}
			}
		}

		public function settings_validate( $input ) {
			return $input;
		}


		public function settings_page() {

			//---- If the options are saved, export the updates to the css & js files
			if( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] == true ) {
				
				//---- Define variables
				$main_options = get_option( 'responsive_tables_opt' );
				$breakpoint = $main_options['breakpoint'];

				$frontend_output = '';
				$jquery_selector = '';
				$admin_output = '.art-table { max-width: 700px; }' . PHP_EOL;
				$output = file_get_contents( __DIR__ . '/css/default.css' ) . PHP_EOL;
				$media_max = '@media (max-width: ' . $breakpoint . 'px) {' . PHP_EOL;
				$media_min = '@media (min-width: ' . ( $breakpoint + 1 ) . 'px) {' . PHP_EOL;

				//---- Set the default styles
				$style_arr = array(
						'.art-table' 													=> array( 'border' => '1px solid #ccc' ),
						'.art-table .art-tbody .art-tr' 								=> array( 'border-bottom' => '1px solid #ccc' ),
						'.art-table .art-tbody .art-tr:last-child' 						=> array( 'border-bottom' => 'none' ),
						'.art-table .art-tbody .art-tr:nth-child(2n)' 					=> array( 'background' => '#ffffff' ),
						'.art-table .art-tbody .art-tr.even-rows' 						=> array( 'background' => '#ffffff' ),
						'.art-table .art-tbody .art-tr:nth-child(2n+1)' 				=> array( 'background' => '#efefef' ),
						'.art-table .art-tbody .art-tr.odd-rows' 						=> array( 'background' => '#efefef' ),
						'.art-table .art-tbody .art-tr .art-td' 						=> array( 'border-bottom' => '1px solid #ddd' ),
						'.art-table .art-tbody .art-tr .art-td:last-child' 				=> array( 'border-bottom' => 'none' ),
						'.art-table .art-tbody .art-tr .art-td .art-td-first' 			=> array( 'font-size' => '16px', 'font-weight' => 'bold', 'line-height' => '1em' ),
						'.art-table .art-tbody .art-tr .art-td .art-td-last' 			=> array( 'border-left' => '0 dashed #ddd', 'font-size' => '16px', 'font-weight' => '300', 'line-height' => '1em' ),
						'.art-table.no-headers .art-tbody .art-tr .art-td .art-td-last' => array( 'border-left' => 'none !important' ),
					);

				//---- Add user defined style
				if( isset( $main_options['table_border_color'] ) && ( $table_border_color = $main_options['table_border_color'] ) != '' ) {
					$style_arr['.art-table .art-tbody .art-tr']['border-color'] = $style_arr['.art-table .art-tbody .art-tr .art-td .art-td-last']['border-color'] = $style_arr['.art-table']['border-color'] = $table_border_color;
				}
				if( isset( $main_options['cell_border_color'] ) && ( $cell_border_color = $main_options['cell_border_color'] ) != '' ) {
					$style_arr['.art-table .art-tbody .art-tr .art-td']['border-color'] = $cell_border_color;
				}
				if( isset( $main_options['odd_row_color'] ) && ( $odd_row_color = $main_options['odd_row_color'] ) != '' ) {
					$style_arr['.art-table .art-tbody .art-tr:nth-child(2n+1)']['background'] = $odd_row_color;
					$style_arr['.art-table .art-tbody .art-tr.odd-rows']['background'] = $odd_row_color;
				}
				if( isset( $main_options['even_row_color'] ) && ( $even_row_color = $main_options['even_row_color'] ) != '' ) {
					$style_arr['.art-table .art-tbody .art-tr:nth-child(2n)']['background'] = $even_row_color;
					$style_arr['.art-table .art-tbody .art-tr.even-rows']['background'] = $even_row_color;
				}
				if( isset( $main_options['header_value_divider'] ) && ( $header_value_divider = $main_options['header_value_divider'] ) != '' ) {
					$style_arr['.art-table .art-tbody .art-tr .art-td .art-td-last']['border-width'] = '1px';
				}
				if( isset( $main_options['header_font'] ) && ( $header_font = $main_options['header_font'] ) != '' ) {
					$style_arr['.art-table .art-tbody .art-tr .art-td .art-td-first']['font-size'] = $header_font.'px';
				}
				if( isset( $main_options['value_font'] ) && ( $value_font = $main_options['value_font'] ) != '' ) {
					$style_arr['.art-table .art-tbody .art-tr .art-td .art-td-last']['font-size'] = $value_font.'px';
				}

				//---- This handles whether to apply everything to all tables or only to select ids & classes
				if( isset( $main_options['activate_all'] ) ) {
					$frontend_output .= 'table { display: none !important; }' . PHP_EOL;
					$jquery_selector = 'table';
				} elseif( isset( $main_options['classes'] ) && $main_options['classes'] != '' ) {
					$classes = explode( ',', $main_options['classes'] );
					$i = 0;
					foreach ($classes as $class) {
						$i++;
						if( $i != 1 ) {
							$jquery_selector .= ',';
						}
						$jquery_selector .= 'table' . str_replace(' ', '', $class);
						$frontend_output .= 'table' . str_replace(' ', '', $class) . ' { display: none !important; }' . PHP_EOL;
					}
				}

				$output .= PHP_EOL;

				//---- Apply the css to the output if the user selects it to be so
				if( isset( $main_options['default_styling'] ) ) {
					foreach ($style_arr as $key => $value) {
						$line = $key . ' { ';
						foreach ($value as $prop => $val) {
							$line .= $prop . ': ' . $val . '; ';
						}
						$line .= '}';
						$output .= $line . PHP_EOL;
					}
				}

				//---- Create the separate outputes for each stylesheet
				$ie_output = $output . '.art-table .art-tbody .art-tr .art-td .art-td-first, .art-table .art-tbody .art-tr .art-td .art-td-last { width: 45%; padding: 10px 2%; }';

				$admin_output .= $output;

				$frontend_output = $media_max . $frontend_output . $output . '}' . PHP_EOL;
				$frontend_output .= $media_min;
				$frontend_output .= '.art-table { display: none; }' . PHP_EOL;
				$frontend_output .= '}';

				//---- Output the stylesheet to style.css which is loaded on the frontend
				$output_path = __DIR__ . '/css/style.css';
				$admin_output_path = __DIR__ . '/css/style-admin.css';
				$ie_output_path = __DIR__ . '/css/style-ie.css';
				file_put_contents($ie_output_path, $ie_output);
				file_put_contents($output_path, $frontend_output);
				file_put_contents($admin_output_path, $admin_output);

				//---- Now adjust the js file
				$js_path = __DIR__ . '/js/script.js';
				$js_file = file( $js_path );
				if( strpos($js_file[2],'tableObjects') !== false ) {
					$js_file[2] = 'tableObjects = $("' . $jquery_selector . '");' . PHP_EOL;
				}
				if( strpos($js_file[3],'breakpoint') !== false ) {
					$js_file[3] = 'breakpoint = ' . $breakpoint . ';' . PHP_EOL;
				}
				file_put_contents($js_path, $js_file);
			}




			//---- Project get some feedback . . . GO!
			echo '<div id="rockwellgrowth-loves-feedback" style="position: relative; margin-top: 20px;">';
			echo '	<form action="http://plugins.rockwellgrowth.com/feedback/grabber.php" method="GET">';
			echo '		<textarea name="feedback_text" style="display: block; width: 100%; width: calc(100% - 20px); max-width: 600px; box-sizing: border-box; margin-bottom: 10px;" id="" cols="30" rows="2">Got feedback? I\'d love to hear from you!</textarea>';
			echo '		<input name="regarding" type="hidden" value="responsive tables">';
			echo '		<input style="display: block; width: 100%; width: calc(100% - 20px); max-width: 600px; box-sizing: border-box; margin-bottom: 10px;" type="submit" class="submit">';
			echo '	</form>';
			echo '</div>';

			//---- Now that everything else is out of the way, do the regular settings page stuff
			echo '<div class="wrap">';
			echo '	<h2>Responsive Tables</h2>';
			echo '	<form method="post" action="options.php">';

        	settings_fields( 'main_settings' );
        	do_settings_sections( 'main_settings' );

			echo '    	<p class="submit">';
			echo '        	<input name="submit" type="submit" id="submit" class="button-primary" value="Save Changes" />';
			echo '    	</p>';
			echo '	</form>';
			echo '</div>';

			echo '<div class="wrap"><div class="art-table"><div class="art-tbody"><div class="art-tr"><div class="art-td">	<div class="art-td-first">Show</div>	<div class="art-td-last">South Park</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first">Genre</div>	<div class="art-td-last">Animated Sitcom</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first">Network</div>	<div class="art-td-last">Comedy Central</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first"># of Episodes</div>	<div class="art-td-last">267</div>	<div class="art-clearfix"></div></div></div><div class="art-tr"><div class="art-td">	<div class="art-td-first">Show</div>	<div class="art-td-last">Always Sunny</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first">Genre</div>	<div class="art-td-last">Sitcom</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first">Network</div>	<div class="art-td-last">FX</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first"># of Episodes</div>	<div class="art-td-last">115</div>	<div class="art-clearfix"></div></div></div><div class="art-tr"><div class="art-td">	<div class="art-td-first">Show</div>	<div class="art-td-last">American Dad</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first">Genre</div>	<div class="art-td-last">Animated Sitcom</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first">Network</div>	<div class="art-td-last">FOX</div>	<div class="art-clearfix"></div></div><div class="art-td">	<div class="art-td-first"># of Episodes</div>	<div class="art-td-last">190</div>	<div class="art-clearfix"></div></div></div></div></div></div>';

		}

		public function admin_menu() {
			add_options_page( 'Responsive Tables', 'Responsive Tables', 'manage_options', 'responsive-tables', array( $this, 'settings_page' ) );
		}

	}

	new responsive_tables;


	//---- Add settings link to plugins page
	function add_rt_settings_link ( $links ) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=responsive-tables' ) . '">Settings</a>';
		return $links;
	}
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_rt_settings_link' );


	//---- Add admin script
	function add_rt_admin_script () {
		$screen = get_current_screen();
		if( $screen->id == 'settings_page_responsive-tables' ) {
			// wp_enqueue_script( 'rt_admin_script', plugins_url( 'js/script-admin.js', __FILE__ ) );
			wp_enqueue_style( 'rt_admin_style', plugins_url( 'css/style-admin.css', __FILE__ ) );
	        wp_enqueue_style( 'wp-color-picker' ); 
	        wp_enqueue_script( 'rt_color_picker', plugins_url( 'js/script-admin.js', __FILE__ ), array( 'wp-color-picker' ), false, true ); 
		}
	}
	add_action( 'admin_enqueue_scripts', 'add_rt_admin_script' );


	//---- Add frontend script & style
	function add_rt_frontend_script () {
		wp_enqueue_script( 'rt_frontend_script', plugins_url( 'js/script.js', __FILE__ ) );
		wp_enqueue_style( 'rt_frontend_style', plugins_url( 'css/style.css', __FILE__ ) );

        //---- IE compatibility
        global $wp_styles;
        wp_enqueue_style( 'art_ie_stylesheet', plugins_url( 'css/style-ie.css', __FILE__ ) );
		$wp_styles->add_data( 'art_ie_stylesheet', 'conditional', 'lt IE 9' );
	}
	add_action( 'wp_enqueue_scripts', 'add_rt_frontend_script', 999 );


}


?>