<?php
/**
 * Template Name: Blocked Ads Template
 *
 * @author  EasyCPMods
  * @since   ClassiPress 4
 */
?>

<div class="row">

	<div id="primary" class="content-area medium-10 medium-centered columns">

		<?php get_template_part( 'parts/breadcrumbs', app_template_base() ); ?>

		<main id="main" class="site-main" role="main">

			<?php
			appthemes_before_loop( 'page' );

      ecpm_bua_show_ad('top');
      ecpm_bua_show_image('top');
      ecpm_bua_show_text('top');
      ecpm_bua_show_image('middle'); 
      ecpm_bua_show_text('bottom');
      ecpm_bua_show_image('bottom');
      ecpm_bua_show_ad('bottom');

			appthemes_after_loop( 'page' );
			?>

		</main>

	</div> <!-- #primary -->

</div> <!-- .row -->
