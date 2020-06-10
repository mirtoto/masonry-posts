<?php

if (!defined('ABSPATH'))
{
	exit;
}

?>

<div id="masonry-posts">

<?php

$ads_freq = get_option('masp_native_in_feed_ads_freq', 0);
$ads_code = get_option('masp_native_in_feed_ads_code', '');

$posts = new WP_Query($argss); 

while ($posts->have_posts()) : $posts->the_post(); 

?>

<div id="masonry-post-<?php the_ID(); ?>" class="masonry-post">
<a href="<?php echo the_permalink(); ?>">
<?php $url = wp_get_attachment_url(get_post_thumbnail_id(), 'full');
if ($url) {
	echo '<img src="' . aq_resize($url, get_option('masp_thumbnail_size_w', 290)) . '" />';
}
?>
</a>
<p class="masonry-post-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><strong><?php the_title() ?></strong></a></p>
<p class="masonry-post-excerpt"><?php the_excerpt(); ?></p>

<div class="entry-meta">
<div class="masonry-post-footer">
<?php $categories_list = get_the_category_list(', ');
if ($categories_list) {
	echo '<span class="categories-links">' . $categories_list . '</span><br/>';
}

echo sprintf('<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
	esc_url(get_permalink()),
	esc_attr(sprintf(__('Permalink to %s', 'masonry-posts'), the_title_attribute('echo=0'))),
	esc_attr(get_the_date('c')),
	esc_html(sprintf('%2$s', get_post_format_string(get_post_format()), get_the_date()))
); 
?>
</div>
</div>

</div>

<?php if ($ads_code != '' && $ads_freq > 0 && $paged == 1 && (($posts->current_post + 1) % $ads_freq) == 0) : ?>
<div id="masonry-post-<?php the_ID(); ?>-ads" class="masonry-post masonry-post-ads">
<?php echo $ads_code; ?>
</div>
<?php endif; ?>

<?php

endwhile;

?>

</div>
<div id="masonry-posts-pagination">

<?php

$big = 999999999;

echo paginate_links(array(
	//'show_all' => true,
	'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
	'format' => '?paged=%#%',
	'current' => max(1, $paged),
	'total' => $posts->max_num_pages
));

?>

</div>

<?php
/*
if (get_option('masp_infinite_scroll', 'on') != 'on' && $posts->max_num_pages > 1) : 

?>

<nav class="navigation paging-navigation" role="navigation" style="max-width:auto;">
	<h1 class="screen-reader-text"><?php _e('Posts navigation', 'twentythirteen'); ?></h1>
	<div class="nav-links">

		<div class="nav-previous"><?php next_posts_link(__('<span class="meta-nav">&larr;</span> Older posts', 'twentythirteen'), $posts->max_num_pages); ?></div>

		<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentythirteen')); ?></div>

	</div><!-- .nav-links -->
</nav><!-- .navigation --> 

<?php
	
endif;
*/
wp_reset_postdata();

?>
