<?php
/**
 * Template Name: Blocked Ads Template
 *
 * @author  EasyCPMods
 * @since   ClassiPress 3.2
 */
?>


<div class="content">

	<div class="content_botbg">

		<div class="content_res">

			<div id="breadcrumb"><?php cp_breadcrumb(); ?></div>

			<div class="content_left">

				<div class="shadowblock_out">

					<div class="shadowblock">
              
							<?php 
              ecpm_bua_show_ad('top');
              ecpm_bua_show_image('top');
              ecpm_bua_show_text('top');
              ecpm_bua_show_image('middle'); 
              ecpm_bua_show_text('bottom');
              ecpm_bua_show_image('bottom');
              ecpm_bua_show_ad('bottom');
              ?>

							<div class="clr"></div>

					</div><!-- /shadowblock -->

				</div><!-- /shadowblock_out -->
        
			</div><!-- /content_left -->

			<?php get_sidebar(); ?>

			<div class="clr"></div>

		</div><!-- /content_res -->

	</div><!-- /content_botbg -->

</div><!-- /content -->
