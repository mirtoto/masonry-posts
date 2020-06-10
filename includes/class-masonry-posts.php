<?php

if (!defined('ABSPATH'))
{
	exit;
}

class Masonry_Posts
{
	private static $_instance = null;
	
	public $settings = null;
	public $_version;
	public $_token;
	public $file;
	public $dir;
	public $assets_dir;
	public $assets_url;
	public $script_suffix;

    public function __construct($file = '', $version = '1.0.0.0')
    {
		$this->_version = $version;
		$this->_token = 'mansory_posts';

		$this->file = $file;
		$this->dir = dirname($this->file);
		$this->assets_dir = trailingslashit($this->dir).'assets';
		$this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

		$this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook($this->file, array($this, 'install'));

		// Load localization
		add_action('plugins_loaded', array($this, 'i18n'));
		
		add_shortcode('masonry_posts', array($this, 'masonry_posts_shortcode'));
		
		// Load frontend JS & CSS
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
	}
	
	public function i18n()
	{
		load_plugin_textdomain('masonry-posts', false, dirname(plugin_basename($this->file)).'/languages/');
	}

	public function enqueue_styles()
	{
		wp_register_style($this->_token.'-frontend', esc_url($this->assets_url).'css/frontend.css');
		wp_enqueue_style($this->_token.'-frontend');
	}

	public function enqueue_scripts()
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-masonry');
		
		wp_register_script('imagesloaded.pkgd', esc_url($this->assets_url).'js/imagesloaded.pkgd'.$this->script_suffix.'.js', array('jquery'));
		wp_enqueue_script('imagesloaded.pkgd');

		wp_register_script('jquery.infinitescroll', esc_url($this->assets_url).'js/jquery.infinitescroll'.$this->script_suffix.'.js', array('jquery'));
		wp_enqueue_script('jquery.infinitescroll');
		wp_register_script('jquery.infinitescroll.behaviors.facebook', esc_url($this->assets_url).'js/jquery.infinitescroll.behaviors/facebook.js', array('jquery'));
		wp_enqueue_script('jquery.infinitescroll.behaviors.facebook');
		wp_register_script('jquery.adsenseloader.js', esc_url($this->assets_url).'js/jquery.adsenseloader.js', array('jquery'));
		wp_enqueue_script('jquery.adsenseloader.js');
		
		wp_register_script($this->_token.'-frontend', esc_url($this->assets_url).'js/frontend'.$this->script_suffix.'.js', array('jquery-masonry'), $this->_version);
		wp_enqueue_script($this->_token.'-frontend');
		
		wp_localize_script($this->_token.'-frontend', 'phpvars',
			array(
				'msg_infinitescroll_finishedmsg' => __('<em>No more pages.</em>', 'masonry-posts'),
				'msg_infinitescroll_manualselector' => __('<span>Load more posts</span>', 'masonry-posts'),
				'opt_infinite_scroll' => get_option('masp_infinite_scroll', 'on'),
				'opt_max_scrolls_before_manual' => get_option('masp_max_scrolls_before_manual', '0')
			)
		);
	}

	public static function instance($file = '', $version = '1.0.0.0')
	{
		if (is_null(self::$_instance))
		{
			self::$_instance = new self($file, $version);
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
	
	public function install()
	{
		$this->_log_version_number();
	}

	private function _log_version_number()
	{
		update_option($this->_token . '_version', $this->_version);
	}
	
	public function masonry_posts_shortcode($atts, $content = null)
	{
		extract(shortcode_atts(array(
			'category_name'			=> '',
			'post_type'				=> 'post',
			'orderby'				=> 'date',
			'order'					=> 'DESC'
		), $atts));

		//$paged = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;
		if (get_query_var('paged'))
		{
			$paged = get_query_var('paged');
		}
		elseif (get_query_var('page'))
		{
			$paged = get_query_var('page');
		}
		else
		{
			$paged = 1;
		}
	
		$argss = array(
			'ignore_sticky_posts'	=> 1,
			'category_name'			=> $category_name,
			'post_type'				=> $post_type,
			'post_status'			=> array('publish', 'inherit'),
			'posts_per_page'		=> get_option('masp_posts_per_page', 6) /*wp_count_posts()->publish*/,
			'paged'					=> $paged,
			'offset'				=> null,
			'orderby'				=> $orderby,
			'order'					=> $order
		);
		
		// Get the posts for this instance
		include('view-masonry-posts.php');
	}
}



