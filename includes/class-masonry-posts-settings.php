<?php

if (!defined('ABSPATH'))
{
	exit;
}

class Masonry_Posts_Settings
{
	private static $_instance = null;
	
	// The main plugin object.
	public $parent = null;
	// Prefix for plugin settings.
	public $base = '';
	//  Available settings for plugin.
	public $settings = array();

	public function __construct($parent)
	{
		$this->parent = $parent;
		
		$this->base = 'masp_';
		
		// Initialise settings
		add_action('admin_init', array($this, 'init_settings'));
		// Register plugin settings
		add_action('admin_init', array($this, 'register_settings'));
		// Add settings page to menu
		add_action('admin_menu', array($this, 'add_menu_item'));

		// Add settings link to plugins page
		add_filter('plugin_action_links_' . plugin_basename($this->parent->file), array($this, 'add_settings_link'));
	}
	
	// Initialise settings
	public function init_settings()
	{
		$this->settings = $this->settings_fields();

		if (isset($_POST['reset']))
		{
			foreach ($this->settings as $section => $data)
			{
				foreach ($data['fields'] as $field)
				{
					$_POST[$this->base . $field['id']] = $field['default'];
				}
			}
			
			add_settings_error('general', 'settings_restored', __('Default settings restored.', 'masonry-posts'), 'updated');
		}
	}
	
	private function settings_fields()
	{
		$settings['posts'] = array(
			'title'	=> __('Posts options', 'masonry-posts'),
			'fields' => array(
				array(
					'id' => 'posts_per_page',
					'label'	=> __('Blog pages show at most', 'masonry-posts'),
					'description' => __('posts', 'masonry-posts'),
					'type' => 'number',
					'default' => '6',
					'min' => '1',
					'class' => 'small-text',
					'placeholder' => ''
				),
				array(
					'id' => 'thumbnail_size_w',
					'label'	=> __('Thumbnail width', 'masonry-posts'),
					'description' => __('px', 'masonry-posts'),
					'type' => 'number',
					'default' => '290',
					'min' => '0',
					'class' => 'small-text',
					'placeholder' => ''
				),
				array(
					'id' => 'infinite_scroll',
					'label' => __('Infinite scroll', 'masonry-posts'),
					'description' => __('Enable infinite scroll', 'masonry-posts'),
					'type' => 'checkbox',
					'default' => 'on'
				),
				array(
					'id' => 'max_scrolls_before_manual',
					'label'	=> __('Change automatic infinite scroll to manual after', 'masonry-posts'),
					'description' => __('scrolls <em>(0 disable this behaviour)</em>', 'masonry-posts'),
					'type' => 'number',
					'default' => '0',
					'min' => '0',
					'class' => 'small-text',
					'placeholder' => ''
				),
				array(
					'id' => 'native_in_feed_ads_freq',
					'label'	=> __('Show native In-feed ads after every', 'masonry-posts'),
					'description' => __('posts <em>(0 disable native In-feed ads)</em>', 'masonry-posts'),
					'type' => 'number',
					'default' => '0',
					'min' => '0',
					'class' => 'small-text',
					'placeholder' => ''
				),
				array(
					'id' => 'native_in_feed_ads_code',
					'label'	=> __('Native In-feed ads code', 'masonry-posts'),
					'description' => __('', 'masonry-posts'),
					'type' => 'textarea',
					'default' => '',
					'class' => 'small-text',
					'placeholder' => ''
				)
			)
		);

		$settings = apply_filters('masonry_posts_settings_fields', $settings);

		return $settings;
	}

	// Register plugin settings
	public function register_settings()
	{
		if (is_array($this->settings))
		{
			foreach ($this->settings as $section => $data)
			{
				// Add section to page
				add_settings_section($section, $data['title'], array($this, 'settings_section'), 'masonry_posts_settings');

				foreach ($data['fields'] as $field)
				{
					// Validation callback for field
					$validation = '';
					
					if (isset($field['callback']))
					{
						$validation = $field['callback'];
					}
					
					// Register field
					$option_name = $this->base . $field['id'];
					register_setting('masonry_posts_settings', $option_name, $validation);

					// Add field to page
					add_settings_field($field['id'], $field['label'], array($this, 'display_field'), 'masonry_posts_settings', $section, array('field' => $field));
				}
			}
		}
	}
	
	public function settings_section($section)
	{
		$html = '';
	
		if (isset($this->settings[$section['id']]['description']))
		{
			$html .= '<p>' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
		}
		
		echo $html;
	}	

	public function display_field($args)
	{
		$field = $args['field'];

		$html = '';

		$option_name = $this->base . $field['id'];
		$option = get_option($option_name);

		$data = '';
		if (isset($field['default']))
		{
			$data = $field['default'];
			if ($option)
			{
				$data = $option;
			}
		}

		switch ($field['type'])
		{
			case 'text':
			case 'password':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '" />' . "\n";
				break;

			case 'number':
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" placeholder="' . esc_attr($field['placeholder']) . '" value="' . $data . '"min="' . esc_attr($field['min']) . '"class="' . esc_attr($field['class']) . '" />' . "\n";
				break;

			case 'checkbox':
				$checked = '';
				
				if ($option && 'on' == $option)
				{
					$checked = 'checked="checked"';
				}
				
				$html .= '<input id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" ' . $checked . '/>' . "\n";
				
				break;
				
			case 'textarea':
				$html .= '<textarea id="' . esc_attr($field['id']) . '" type="' . $field['type'] . '" name="' . esc_attr($option_name) . '" cols="40" rows="10">' . $data . "</textarea>\n";
				break;
		}
		
		switch( $field['type'] )
		{
			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
				break;
				
			case 'checkbox':
				$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . $field['description'] . '</label>' . "\n";
				break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . $field['description'] . '</label>' . "\n";
				break;
		}
		
		echo $html;
	}
	
	// Add settings page to menu
	public function add_menu_item()
	{
		$page = add_options_page(
			__('Masonry Posts', 'masonry-posts'),
			__('Masonry Posts', 'masonry-posts'),
			'manage_options',
			'masonry_posts_settings',
			array($this, 'settings_page')
		);
		
		add_action('admin_print_styles-' . $page, array($this, 'settings_assets'));
	}
	
	public function settings_page()
	{
		if (!current_user_can('manage_options'))
		{
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}	
	
		// Build page HTML
		$html = '<div class="wrap" id="masonry_posts_settings">' . "\n";
		
			$html .= '<h2>' . __('Masonry Posts Settings', 'masonry-posts') . '</h2>' . "\n";
			
			$html .= '<h2 class="nav-tab-wrapper">';
			$html .= '<a href="#masonry-posts-settings" class="nav-tab nav-tab-active">' . __('Settings') . '</a></h2>' . "\n";
			
			$html .= '<h3>' . __('Shortcodes made available by this plugin', 'masonry-posts') . '</h3>' . "\n";
			$html .= '<ol>';
			$html .= '<li><code>[masonry_posts]</code>' . __(' - available parameters:', 'masonry-posts') . '</li>' . "\n";
			$html .= '<p><ul>';
			$html .= '<li><code>category_name</code>' . __(' - category names (slugs) separated by comma; live empty for all categories,', 'masonry-posts') . '</li>' . "\n";
			$html .= '<li><code>post_type</code>' . __(' - post types; by default: "post",', 'masonry-posts') . '</li>' . "\n";
			$html .= '<li><code>orderby</code>' . __(' - sort retrieved posts by parameter; by default: "date",', 'masonry-posts') . '</li>' . "\n";
			$html .= '<li><code>order</code>' . __(' - ascending or descending order; by default: "DESC".', 'masonry-posts') . '</li>' . "\n";
			$html .= '</ul></p>';
			$html .= '</ol>' . "\n";
			
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Get settings fields
				ob_start();
				settings_fields('masonry_posts_settings');
				do_settings_sections('masonry_posts_settings');
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
				$html .= '<input name="submit" type="submit" class="button button-primary" value="' . esc_attr(__('Save Changes')) . '" />' . "\n";
				$html .= '<input name="reset" type="submit" class="button reset-button button-secondary" value="' . esc_attr(__('Restore defaults', 'masonry-posts')) . '" />' . "\n";
				$html .= '</p>' . "\n";
				
			$html .= '</form>' . "\n";
			
		$html .= '</div>' . "\n";

		echo $html;
	}
	
	public function settings_assets()
	{
	}
	
	// Add settings link to plugins page
	public function add_settings_link($links)
	{
		$settings_link = '<a href="options-general.php?page=masonry_posts_settings">' . __('Settings', 'masonry-posts') . '</a>';

		array_push($links, $settings_link);

		return $links;
	}	
	
	public static function instance($parent)
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self($parent);
		}
		
		return self::$_instance;
	}
	
	public function __clone()
	{
		_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
	}

	public function __wakeup()
	{
		_doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
	}	
}


