<?php
/**
 * Single Template
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if( in_array( get_post_meta(get_the_ID(), 'mfn_template_type', true), array('single-product', 'shop-archive')) ){
	get_header( 'shop' );
}else{
	get_header();
}
?>

<div id="Content">
	<div class="content_wrapper clearfix">

		<div class="sections_group">

			<div class="entry-content" itemprop="mainContentOfPage">

				<div class="product">
				<?php

					$mfn_builder = new Mfn_Builder_Front(get_the_ID());
					$mfn_builder->show();
					
				?>
				</div>

				<?php 

				// sample content for header builder
				if( get_post_meta(get_the_ID(), 'mfn_template_type', true) == 'header'){
					echo '<div class="mfn-only-sample-content">';
		        	$sample_page_id = get_option( 'page_on_front' );
		        	$mfn_item_sample = get_post_meta($sample_page_id, 'mfn-page-items', true);

		        	$front = new Mfn_Builder_Front($sample_page_id);
					$front->show($mfn_item_sample);
					echo '</div>';
		        }

				?>

			</div>

		</div>

		<?php get_sidebar(); ?>

	</div>
</div>

<?php 
if( in_array( get_post_meta(get_the_ID(), 'mfn_template_type', true), array('single-product', 'shop-archive')) ){
	get_footer( 'shop' );
}else{
	get_footer();
}
