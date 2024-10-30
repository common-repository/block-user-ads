
<div id='bua_sections'>

<section id="ecpm_bua_section" style="display:block;">  
    <table width="100%" cellspacing="0" cellpadding="5" border="0" class="bua-table-top">

      <tr>
        <td align="center" width="20"></td>
        <td align="center" width="120"><strong><?php echo _e('User ID', ECPM_BUA); ?></strong></td>
        <td align="center" width="120"><strong><?php echo _e('Blocked since', ECPM_BUA); ?></strong></td>
        <td align="center" width="150"><strong><?php echo _e('Expires', ECPM_BUA); ?></strong></td>
        <td align="center" width="150"><strong><?php echo _e('Notes', ECPM_BUA); ?></strong></td>
        <td align="center" width="80"><strong><?php echo _e('No. of ads', ECPM_BUA); ?></strong></td>
        <td align="center" width="100"><strong><?php echo _e('Action', ECPM_BUA); ?></strong></td>
        
      </tr>
      <tr><td colspan="7"><hr></td></tr>
      <?php
      
      $userCount = 0;
      $blocked = ecpm_bua_get_blocked_users();
      
      if (count($blocked) == 0) {
        echo "<tr><td colspan='4'>". __('There are no blocked users so far', ECPM_BUA)."</td></tr>";
      }
      
      foreach ($blocked as $user) {
        $userdata = get_userdata($user->ID);
        $blocked_since = get_user_meta( $user->ID, ECPM_BUA_META_KEY.'_date', true );
        $expire = get_user_meta( $user->ID, ECPM_BUA_META_KEY.'_expire', true );
        $notes = get_user_meta( $user->ID, ECPM_BUA_META_KEY.'_notes', true );
      ?>
      <tr>
        <td align="center">
          <?php echo ++$userCount;?>
  		  </td>
        
        <td align="center">
          <a href="<?php echo get_edit_user_link( $user->ID );?>"><?php echo esc_html($userdata->user_login);?></a>
  		  </td>
        
        <td align="center">
          <?php 
          if ($blocked_since != '')
            echo date_i18n( get_option('date_format'), strtotime( $blocked_since ) );?>
  		  </td>

        <td align="center">
          <?php 
          if ($expire == '')
            echo _e('Never', ECPM_BUA);
          else  
            echo date_i18n( get_option('date_format'), strtotime( $expire ) );
          ?>
  		  </td>
        
        <td align="center">
          <?php 
            echo $notes;
          ?>
  		  </td>
        
        <td align="center"><?= ecpm_bua_count_ads($user->ID);?></td>
        
        <td align="center">
          <?php
            $unblock_url = add_query_arg( array( 'bua_unblock' => $user->ID ), $form_url );
          ?>
            <a href="<?php echo esc_url($unblock_url);?>" onclick="return confirmBuaDelete('<?php echo sprintf( __( "Are you sure you want to unblock user %s?", ECPM_BUA ), $userdata->user_login);?>');"><?= __('Unblock', ECPM_BUA); ?></a>
        </td>
        
      </tr>
      <?php
      }
      ?>
      
      <tr><td colspan="7"><hr></td></tr>
      <tr>
        <td colspan="3">
          <?php
            $user_list = ecpm_bua_get_avail_users();
          ?>
          <select id='ecpm_bua_block_user' name="ecpm_bua_block_user">
					  <option value=""><?php _e('Select user to block...', ECPM_BUA);?></option>
					  <?php
            foreach ($user_list as $user) {
              $user_mail = '';
              if (!strpos($user->user_login, '@') )
                $user_mail = ' ('.$user->user_email.')';
            ?>
              <option value="<?= $user->ID;?>"><?= $user->user_login.$user_mail;?></option>
            <?php  
            }
            ?>
          </select>
        </td>
        <td align="center">
          <input type='text' size='2' id='ecpm_bua_expire' Name='ecpm_bua_expire' style="width:30px;">  
          
          <select name="ecpm_bua_expire_time" id="ecpm_bua_expire_time" style="margin-bottom:1px;">
            <option value="never"><?php echo _e('Never', ECPM_BUA); ?></option>
            <option value="day"><?php echo _e('Day', ECPM_BUA); ?></option>
            <option value="week"><?php echo _e('Week', ECPM_BUA); ?></option>
            <option value="month"><?php echo _e('Month', ECPM_BUA); ?></option>
            <option value="year"><?php echo _e('Year', ECPM_BUA); ?></option>
          </select>  
        
        </td>

        <td align="center" colspan="2">
          <Input type='text' size='30' id='ecpm_bua_notes' Name='ecpm_bua_notes'>
        </td>

        <td>    
          <input class="button-secondary" style="float:right;" type="submit" id="bua_block" name="bua_block" onclick="return confirmBuaDelete('<?php echo __( "Are you sure you want to block user?", ECPM_BUA );?>');" value="<?= __('Block user', ECPM_BUA); ?>">
        </td>

        
      </tr>
       
    </table>
  </section>

  <section id="ecpm_bua_section">
    <table width="100%" cellspacing="0" cellpadding="10" border="0" class="bua-table-top">
      <tr>
    		<th align="left">
          <label for="ecpm_bua_image"><?php echo _e('Image', ECPM_BUA); ?></label>
        </th>  
        <td colspan="2">
          <Input type='text' size='100' id='ecpm_bua_image' Name='ecpm_bua_image' value='<?php echo esc_html($ecpm_bua_settings['image']);?>'>
          <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
     
          <script type="text/javascript">
          jQuery(document).ready(function($){
              $('#upload-btn').click(function(e) {
                  e.preventDefault();
                  var image = wp.media({ 
                      title: 'Upload Image',
                      // mutiple: true if you want to upload multiple files at once
                      multiple: false
                  }).open()
                  .on('select', function(e){
                      // This will return the selected image from the Media Uploader, the result is an object
                      var uploaded_image = image.state().get('selection').first();
                      // We convert uploaded_image to a JSON object to make accessing it easier
                      // Output to the console uploaded_image
                      console.log(uploaded_image);
                      var image_url = uploaded_image.toJSON().url;
                      // Let's assign the url value to the input field
                      $('#ecpm_bua_image').val(image_url);
                  });
              });
          });
          </script>
               
        </td>
      </tr>
      
      <tr>    
        <th align="left" valign="top">
          <label for="ecpm_bua_image_position"><?php echo _e('Image position', ECPM_BUA); ?></label>
    		</th>
        <td> 
          <select name="ecpm_bua_image_position" id="ecpm_bua_image_position">
             <option value="top" <?php echo ($ecpm_bua_settings['image_position'] == 'top' ? 'selected':'') ;?>><?php echo _e('Top', ECPM_BUA); ?></option>
             <option value="middle" <?php echo ($ecpm_bua_settings['image_position'] == 'middle' ? 'selected':'') ;?>><?php echo _e('Middle', ECPM_BUA); ?></option>
             <option value="bottom" <?php echo ($ecpm_bua_settings['image_position'] == 'bottom' ? 'selected':'') ;?>><?php echo _e('Bottom', ECPM_BUA); ?></option>
             <option value="hide" <?php echo ($ecpm_bua_settings['image_position'] == 'hide' ? 'selected':'') ;?>><?php echo _e('Hide image', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Where to position the image' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      
    </table>
  </section>
  
  <section id="ecpm_bua_section">  
    <table width="100%" cellspacing="0" cellpadding="10" border="0" class="bua-table-top">
      
      <tr>
    		<th align="left" valign="top">
    			<label for="ecpm_bua_show_top"><?php echo _e('Show text on top', ECPM_BUA); ?></label>
    		</th>
    		<td valign="top">
          <Input type='checkbox' Name='ecpm_bua_show_top' id="ecpm_bua_show_top" <?php echo ( $ecpm_bua_settings['show_top'] == 'on' ? 'checked':'') ;?> >
        </td>
        <td>  
          <span class="description"><?php _e( 'Would you like to show notice on top of image?' , ECPM_BUA ); ?></span>
    		</td>
    	</tr>
    
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_top_text"><?php echo _e('Top notice text', ECPM_BUA); ?></label>
        </th>
        <td>  
          <textarea cols="80" rows='4' id='ecpm_bua_top_text' Name='ecpm_bua_top_text'><?php echo esc_html($ecpm_bua_settings['top_text']);?></textarea>
          <br><i><?php _e( 'Variables you can use: [home], [dashboard]' , ECPM_BUA ); ?></i>
    	  </td>
        <td>
          <span class="description"><?php _e( 'Notice text on top of the image)' , ECPM_ECP ); ?></span>
    		</td> 
    	</tr>
      
      <tr>
    		<th align="left" valign="top">
    			<label for="ecpm_bua_top_size"><?php echo _e('Size', ECPM_BUA); ?></label>
    		</th>
    		<td>
          <select name="ecpm_bua_top_size" id="ecpm_bua_top_size">
             <option value=""></option>
             <option value="xx-small" <?php echo ($ecpm_bua_settings['top_size'] == 'xx-small' ? 'selected':'') ;?>><?php echo _e('xx-small', ECPM_BUA); ?></option>
             <option value="x-small" <?php echo ($ecpm_bua_settings['top_size'] == 'x-small' ? 'selected':'') ;?>><?php echo _e('x-small', ECPM_BUA); ?></option>
             <option value="small" <?php echo ($ecpm_bua_settings['top_size'] == 'small' ? 'selected':'') ;?>><?php echo _e('small', ECPM_BUA); ?></option>
             <option value="medium" <?php echo ($ecpm_bua_settings['top_size'] == 'medium' ? 'selected':'') ;?>><?php echo _e('medium', ECPM_BUA); ?></option>
             <option value="large" <?php echo ($ecpm_bua_settings['top_size'] == 'large' ? 'selected':'') ;?>><?php echo _e('large', ECPM_BUA); ?></option>
             <option value="x-large" <?php echo ($ecpm_bua_settings['top_size'] == 'x-large' ? 'selected':'') ;?>><?php echo _e('x-large', ECPM_BUA); ?></option>
             <option value="xx-large" <?php echo ($ecpm_bua_settings['top_size'] == 'xx-large' ? 'selected':'') ;?>><?php echo _e('xx-large', ECPM_BUA); ?></option>
             <option value="smaller" <?php echo ($ecpm_bua_settings['top_size'] == 'smaller' ? 'selected':'') ;?>><?php echo _e('smaller', ECPM_BUA); ?></option>
             <option value="larger" <?php echo ($ecpm_bua_settings['top_size'] == 'larger' ? 'selected':'') ;?>><?php echo _e('larger', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Top notice font size' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_top_weight"><?php echo _e('Weight', ECPM_BUA); ?></label>
    		</th>
        <td>  
          <select name="ecpm_bua_top_weight" id="ecpm_bua_top_weight">
             <option value="normal" <?php echo ($ecpm_bua_settings['top_weight'] == 'normal' ? 'selected':'') ;?>><?php echo _e('Normal', ECPM_BUA); ?></option>
             <option value="lighter" <?php echo ($ecpm_bua_settings['top_weight'] == 'lighter' ? 'selected':'') ;?>><?php echo _e('Lighter', ECPM_BUA); ?></option>
             <option value="bold" <?php echo ($ecpm_bua_settings['top_weight'] == 'bold' ? 'selected':'') ;?>><?php echo _e('Bold', ECPM_BUA); ?></option>
             <option value="bolder" <?php echo ($ecpm_bua_settings['top_weight'] == 'bolder' ? 'selected':'') ;?>><?php echo _e('Bolder', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Top notice font weight' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_top_style"><?php echo _e('Style', ECPM_BUA); ?></label>
    		</th>
        <td>  
          <select name="ecpm_bua_top_style" id="ecpm_bua_top_style">
             <option value="normal" <?php echo ($ecpm_bua_settings['top_style'] == 'normal' ? 'selected':'') ;?>><?php echo _e('Normal', ECPM_BUA); ?></option>
             <option value="italic" <?php echo ($ecpm_bua_settings['top_style'] == 'italic' ? 'selected':'') ;?>><?php echo _e('Italic', ECPM_BUA); ?></option>
             <option value="oblique" <?php echo ($ecpm_bua_settings['top_style'] == 'oblique' ? 'selected':'') ;?>><?php echo _e('Oblique', ECPM_BUA); ?></option>
             <option value="initial" <?php echo ($ecpm_bua_settings['top_style'] == 'initial' ? 'selected':'') ;?>><?php echo _e('Initial', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Top notice font style' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_top_decoration"><?php echo _e('Decoration', ECPM_BUA); ?>
    		</th>
        <td>  
          <select name="ecpm_bua_top_decoration" id="ecpm_bua_top_decoration">
             <option value="none" <?php echo ($ecpm_bua_settings['top_decoration'] == 'normal' ? 'selected':'') ;?>><?php echo _e('None', ECPM_BUA); ?></option>
             <option value="underline" <?php echo ($ecpm_bua_settings['top_decoration'] == 'underline' ? 'selected':'') ;?>><?php echo _e('Underline', ECPM_BUA); ?></option>
             <option value="overline" <?php echo ($ecpm_bua_settings['top_decoration'] == 'overline' ? 'selected':'') ;?>><?php echo _e('Overline', ECPM_BUA); ?></option>
             <option value="line-through" <?php echo ($ecpm_bua_settings['top_decoration'] == 'line-through' ? 'selected':'') ;?>><?php echo _e('Line-through', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Top notice font decoration' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      <tr>    
        <th align="left" valign="top">
          <label for="ecpm_bua_top_transform"><?php echo _e('Transform', ECPM_BUA); ?></label>
    		</th>
        <td> 
          <select name="ecpm_bua_top_transform" id="ecpm_bua_top_transform">
             <option value="none" <?php echo ($ecpm_bua_settings['top_transform'] == 'none' ? 'selected':'') ;?>><?php echo _e('None', ECPM_BUA); ?></option>
             <option value="capitalize" <?php echo ($ecpm_bua_settings['top_transform'] == 'capitalize' ? 'selected':'') ;?>><?php echo _e('Capitalize', ECPM_BUA); ?></option>
             <option value="uppercase" <?php echo ($ecpm_bua_settings['top_transform'] == 'uppercase' ? 'selected':'') ;?>><?php echo _e('Upper case', ECPM_BUA); ?></option>
             <option value="lowercase" <?php echo ($ecpm_bua_settings['top_transform'] == 'lowercase' ? 'selected':'') ;?>><?php echo _e('Lower case', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Top notice font transformation' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_top_color"><?php echo _e('Color', ECPM_BUA); ?></label>
    		</th>
        <td>    
          <div style=" display:inline-block; vertical-align:middle;">
            <Input type='text' size='4' class="ecpm-bua-color-field" id='ecpm_bua_top_color' Name='ecpm_bua_top_color' value='<?php echo esc_html($ecpm_bua_settings['top_color']);?>'>
          </div>
    	  </td>
        <td>		
          <span class="description"><?php _e( 'Top notice label font color' , ECPM_BUA ); ?></span>
    		</td>
      </tr> 
      
    </table>
  </section>
  
  <section id="ecpm_bua_section">  
    <table width="100%" cellspacing="0" cellpadding="10" border="0" class="bua-table-top">

      <tr>
    		<th align="left" valign="top">
    			<label for="ecpm_bua_show_bottom"><?php echo _e('Show text on bottom', ECPM_BUA); ?></label>
    		</th>
    		<td valign="top">
          <Input type='checkbox' Name='ecpm_bua_show_bottom' id="ecpm_bua_show_bottom" <?php echo ( $ecpm_bua_settings['show_bottom'] == 'on' ? 'checked':'') ;?> >
        </td>
        <td>  
          <span class="description"><?php _e( 'Would you like to show notice on bottom of image?' , ECPM_BUA ); ?></span>
    		</td>
    	</tr>
      
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_bottom_text"><?php echo _e('Bottom notice text', ECPM_BUA); ?></label>
        </th>
        <td>  
          <textarea cols="80" rows='4' id='ecpm_bua_bottom_text' Name='ecpm_bua_bottom_text'><?php echo esc_html($ecpm_bua_settings['bottom_text']);?></textarea>
          <br><i><?php _e( 'Variables you can use: [home], [dashboard]' , ECPM_BUA ); ?></i>
    	  </td>
        <td>
          <span class="description"><?php _e( 'Notice text bottom of the image)' , ECPM_ECP ); ?></span>
    		</td> 
    	</tr>
      
      <tr>
    		<th align="left" valign="bottom">
    			<label for="ecpm_bua_bottom_size"><?php echo _e('Size', ECPM_BUA); ?></label>
    		</th>
    		<td>
          <select name="ecpm_bua_bottom_size" id="ecpm_bua_bottom_size">
             <option value=""></option>
             <option value="xx-small" <?php echo ($ecpm_bua_settings['bottom_size'] == 'xx-small' ? 'selected':'') ;?>><?php echo _e('xx-small', ECPM_BUA); ?></option>
             <option value="x-small" <?php echo ($ecpm_bua_settings['bottom_size'] == 'x-small' ? 'selected':'') ;?>><?php echo _e('x-small', ECPM_BUA); ?></option>
             <option value="small" <?php echo ($ecpm_bua_settings['bottom_size'] == 'small' ? 'selected':'') ;?>><?php echo _e('small', ECPM_BUA); ?></option>
             <option value="medium" <?php echo ($ecpm_bua_settings['bottom_size'] == 'medium' ? 'selected':'') ;?>><?php echo _e('medium', ECPM_BUA); ?></option>
             <option value="large" <?php echo ($ecpm_bua_settings['bottom_size'] == 'large' ? 'selected':'') ;?>><?php echo _e('large', ECPM_BUA); ?></option>
             <option value="x-large" <?php echo ($ecpm_bua_settings['bottom_size'] == 'x-large' ? 'selected':'') ;?>><?php echo _e('x-large', ECPM_BUA); ?></option>
             <option value="xx-large" <?php echo ($ecpm_bua_settings['bottom_size'] == 'xx-large' ? 'selected':'') ;?>><?php echo _e('xx-large', ECPM_BUA); ?></option>
             <option value="smaller" <?php echo ($ecpm_bua_settings['bottom_size'] == 'smaller' ? 'selected':'') ;?>><?php echo _e('smaller', ECPM_BUA); ?></option>
             <option value="larger" <?php echo ($ecpm_bua_settings['bottom_size'] == 'larger' ? 'selected':'') ;?>><?php echo _e('larger', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Bottom notice font size' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      <tr>
        <th align="left" valign="bottom">
          <label for="ecpm_bua_bottom_weight"><?php echo _e('Weight', ECPM_BUA); ?></label>
    		</th>
        <td>  
          <select name="ecpm_bua_bottom_weight" id="ecpm_bua_bottom_weight">
             <option value="normal" <?php echo ($ecpm_bua_settings['bottom_weight'] == 'normal' ? 'selected':'') ;?>><?php echo _e('Normal', ECPM_BUA); ?></option>
             <option value="lighter" <?php echo ($ecpm_bua_settings['bottom_weight'] == 'lighter' ? 'selected':'') ;?>><?php echo _e('Lighter', ECPM_BUA); ?></option>
             <option value="bold" <?php echo ($ecpm_bua_settings['bottom_weight'] == 'bold' ? 'selected':'') ;?>><?php echo _e('Bold', ECPM_BUA); ?></option>
             <option value="bolder" <?php echo ($ecpm_bua_settings['bottom_weight'] == 'bolder' ? 'selected':'') ;?>><?php echo _e('Bolder', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Bottom notice font weight' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_bottom_style"><?php echo _e('Style', ECPM_BUA); ?></label>
    		</th>
        <td>  
          <select name="ecpm_bua_bottom_style" id="ecpm_bua_bottom_style">
             <option value="normal" <?php echo ($ecpm_bua_settings['bottom_style'] == 'normal' ? 'selected':'') ;?>><?php echo _e('Normal', ECPM_BUA); ?></option>
             <option value="italic" <?php echo ($ecpm_bua_settings['bottom_style'] == 'italic' ? 'selected':'') ;?>><?php echo _e('Italic', ECPM_BUA); ?></option>
             <option value="oblique" <?php echo ($ecpm_bua_settings['bottom_style'] == 'oblique' ? 'selected':'') ;?>><?php echo _e('Oblique', ECPM_BUA); ?></option>
             <option value="initial" <?php echo ($ecpm_bua_settings['bottom_style'] == 'initial' ? 'selected':'') ;?>><?php echo _e('Initial', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Bottom notice font style' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      
      <tr>
        <th align="left" valign="bottom">
          <label for="ecpm_bua_bottom_decoration"><?php echo _e('Decoration', ECPM_BUA); ?>
    		</th>
        <td>  
          <select name="ecpm_bua_bottom_decoration" id="ecpm_bua_bottom_decoration">
             <option value="none" <?php echo ($ecpm_bua_settings['bottom_decoration'] == 'normal' ? 'selected':'') ;?>><?php echo _e('None', ECPM_BUA); ?></option>
             <option value="underline" <?php echo ($ecpm_bua_settings['bottom_decoration'] == 'underline' ? 'selected':'') ;?>><?php echo _e('Underline', ECPM_BUA); ?></option>
             <option value="overline" <?php echo ($ecpm_bua_settings['bottom_decoration'] == 'overline' ? 'selected':'') ;?>><?php echo _e('Overline', ECPM_BUA); ?></option>
             <option value="line-through" <?php echo ($ecpm_bua_settings['bottom_decoration'] == 'line-through' ? 'selected':'') ;?>><?php echo _e('Line-through', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Bottom notice font decoration' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      <tr>    
        <th align="left" valign="bottom">
          <label for="ecpm_bua_bottom_transform"><?php echo _e('Transform', ECPM_BUA); ?></label>
    		</th>
        <td> 
          <select name="ecpm_bua_bottom_transform" id="ecpm_bua_bottom_transform">
             <option value="none" <?php echo ($ecpm_bua_settings['bottom_transform'] == 'none' ? 'selected':'') ;?>><?php echo _e('None', ECPM_BUA); ?></option>
             <option value="capitalize" <?php echo ($ecpm_bua_settings['bottom_transform'] == 'capitalize' ? 'selected':'') ;?>><?php echo _e('Capitalize', ECPM_BUA); ?></option>
             <option value="uppercase" <?php echo ($ecpm_bua_settings['bottom_transform'] == 'uppercase' ? 'selected':'') ;?>><?php echo _e('Upper case', ECPM_BUA); ?></option>
             <option value="lowercase" <?php echo ($ecpm_bua_settings['bottom_transform'] == 'lowercase' ? 'selected':'') ;?>><?php echo _e('Lower case', ECPM_BUA); ?></option>
          </select>
        </td>
        <td>		
          <span class="description"><?php _e( 'Bottom notice font transformation' , ECPM_BUA ); ?></span>
    		</td>
      </tr>
      <tr>
        <th align="left" valign="bottom">
          <label for="ecpm_bua_bottom_color"><?php echo _e('Color', ECPM_BUA); ?></label>
    		</th>
        <td>    
          <div style=" display:inline-block; vertical-align:middle;">
            <Input type='text' size='4' class="ecpm-bua-color-field" id='ecpm_bua_bottom_color' Name='ecpm_bua_bottom_color' value='<?php echo esc_html($ecpm_bua_settings['bottom_color']);?>'>
          </div>
    	  </td>
        <td>		
          <span class="description"><?php _e( 'Bottom notice label font color' , ECPM_BUA ); ?></span>
    		</td>
      </tr> 
      
    </table>
  </section>
  
  <section id="ecpm_bua_section">  
    <table width="100%" cellspacing="0" cellpadding="10" border="0" class="bua-table-top">

      <tr>
    		<th align="left" valign="top">
    			<label for="ecpm_bua_show_top_ad"><?php echo _e('Show top ad', ECPM_BUA); ?></label>
    		</th>
    		<td valign="top">
          <Input type='checkbox' Name='ecpm_bua_show_top_ad' id="ecpm_bua_show_top_ad" <?php echo ( $ecpm_bua_settings['show_top_ad'] == 'on' ? 'checked':'') ;?> >
        </td>
        <td>  
          <span class="description"><?php _e( 'Would you like to show top ad?' , ECPM_BUA ); ?></span>
    		</td>
    	</tr>
      
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_top_ad"><?php echo _e('Top ad', ECPM_BUA); ?></label>
        </th>
        <td colspan="2">  
          <textarea cols="80" rows='10' id='ecpm_bua_top_ad' Name='ecpm_bua_top_ad'><?php echo esc_html($ecpm_bua_settings['top_ad']);?></textarea>
    	  </td>
    	</tr>
      
      <tr>
    		<th align="left" valign="top">
    			<label for="ecpm_bua_show_bottom_ad"><?php echo _e('Show bottom ad', ECPM_BUA); ?></label>
    		</th>
    		<td valign="top">
          <Input type='checkbox' Name='ecpm_bua_show_bottom_ad' id="ecpm_bua_show_bottom_ad" <?php echo ( $ecpm_bua_settings['show_bottom_ad'] == 'on' ? 'checked':'') ;?> >
        </td>
        <td>  
          <span class="description"><?php _e( 'Would you like to show bottom ad?' , ECPM_BUA ); ?></span>
    		</td>
    	</tr>
      
      <tr>
        <th align="left" valign="top">
          <label for="ecpm_bua_bottom_ad"><?php echo _e('Bottom ad', ECPM_BUA); ?></label>
        </th>
        <td colspan="2">  
          <textarea cols="80" rows='10' id='ecpm_bua_bottom_ad' Name='ecpm_bua_bottom_ad'><?php echo esc_html($ecpm_bua_settings['bottom_ad']);?></textarea>
    	  </td>
    	</tr>
      
    </table>

  </section>
  
  <section id="ecpm_bua_section">  
    <table width="100%" cellspacing="0" cellpadding="10" border="0" class="bua-table-top">
      
      <tr>
    		<th align="left" valign="top">
    			<label for="ecpm_bua_remove_userdata"><?php echo _e('Remove user data on uninstall', ECPM_BUA); ?></label>
    		</th>
    		<td valign="top">
          <Input type='checkbox' Name='ecpm_bua_remove_userdata' id="ecpm_bua_remove_userdata" <?php echo ( $ecpm_bua_settings['remove_userdata'] == 'on' ? 'checked':'') ;?> >
        </td>
        <td>  
          <span class="description"><?php _e( 'Would you like to remove data about blocked users on plugin install?' , ECPM_BUA ); ?></span>
    		</td>
    	</tr>  
    </table>
  </section>  
  
</div>