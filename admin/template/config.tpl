{combine_css path=$SHAREALBUM_PATH|@cat:"admin/template/style.css"}
{footer_script}
{/footer_script}

<div class="titrePage">
	<h2>Share Album</h2>
</div>
<div id="helpContent">
  <p>{$INTRO_CONTENT}</p>
</div>

<form method="post" action="" class="properties">
<fieldset>
  <legend>{'Navigation options'|translate}</legend>
  <ul>
    <li>
      <label>
        <input type="checkbox" id="option_hide_menus" name="option_hide_menus" value="{$sharealbum.option_hide_menus}" {if $sharealbum.option_hide_menus}checked="checked"{/if}>
        <b>{'Hide menus for albums visitors'|translate}</b>
      </label>
      <a class="icon-info-circled-1" title="{'When checked, menus are hidden for visitors of the shared album'|translate}"></a>
    </li>
    <li>
      <label>
        &nbsp;&nbsp;&nbsp;<input type="checkbox" id="option_show_login_menu" name="option_show_login_menu" value="{$sharealbum.option_show_login_menu}" {if $sharealbum.option_show_login_menu}checked="checked"{/if}>
        <b>{'Show a login menu'|translate}</b>
      </label>
      <a class="icon-info-circled-1" title="{'When checked, a login menu is shown for guests browsing via a shared link'|translate}"></a>
    </li>
   
    <li>
      <label>
        <input type="checkbox" name="option_remember_me" value="{$sharealbum.option_remember_me}" {if $sharealbum.option_remember_me}checked="checked"{/if}>
        <b>{'Sets remember me cookie for logged in guests (auto-login)'|translate}</b>
      </label>
      <a class="icon-info-circled-1" title="{'When checked, users can go back to root Piwigo url and automatically logged in to browse the last visited shared album'|translate}"></a>
    </li>
 	</ul>
 	</fieldset>
 	<fieldset>
 	<legend>{'Shared albums options'|translate}</legend>
 	<ul>
      	<li>
	    	<label>
	        <input type="checkbox" name="option_replace_breadcrumbs" value="{$sharealbum.option_replace_breadcrumbs}" {if $sharealbum.option_replace_breadcrumbs}checked="checked"{/if}>
	        <b>{'Replace navigation breadcrumbs with album name'|translate}</b>
	      	</label>
	      	<a class="icon-info-circled-1" title="{'When checked, breadcrumbs are replaced with the album name'|translate}"></a>
    	</li>
    	<li>
	    	<label>
	        <input type="checkbox" name="option_recursive_shares" value="{$sharealbum.option_recursive_shares}" {if $sharealbum.option_recursive_shares}checked="checked"{/if}>
	        <b>{'Apply shares to sub-albums'|translate}</b>
	      	</label>
	      	<a class="icon-info-circled-1" title="{'When checked, nested albums are shared as well as the parent shared album'|translate}"></a>
    	</li>
    <li>
      <label>
        <b>{'Number of displayed images per page for shared albums'|translate} : </b>
      </label>
       <select name="option_pics_per_page">
       		{foreach from=$nb_image_page_values key=mKey item=mItem}
       		<option value="{$mItem}" {if $sharealbum.option_pics_per_page==$mItem}selected{/if}>{$mItem}</option>
       		{/foreach}
		</select>
      <a class="icon-info-circled-1" title="{'Defines the number of maximum images are displayed per page for shared albums. This setting applies to all shared albums'|translate}"></a>
    </li>
  </ul>
</fieldset>

<fieldset>
 	<legend>{'Advanced options'|translate}</legend>
 	<ul>
      <li>
      <label>
        <input type="checkbox" name="option_enable_powerusers" value="{$sharealbum.option_enable_powerusers}" {if $sharealbum.option_enable_powerusers}checked="checked"{/if}>
        <b>{'Enable non-admin powerusers to share albums'|translate}</b>
      </label>
      <a class="icon-info-circled-1" title="{'When checked, any user belonging to the group \'sharealbum_powerusers\' has access to the share option in private album pages'|translate}"></a>
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{'For this, add power users into the group <b>\'sharealbum_powerusers\'</b>.'|translate}
      <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{'Warning : those users will be able to create shared albums as well as to remove existing shares'|translate}
    </li>
    
  </ul>
</fieldset>

<p class="formButtons"><input type="submit" name="save_config" value="{'Save Settings'|translate}"></p>
</form>


{footer_script require='jquery'}{literal}
function update_options() {
	if (jQuery('#option_hide_menus').prop('checked') == true)  {
  		jQuery('#option_show_login_menu').prop('disabled', false);
  	} else {
  		jQuery('#option_show_login_menu').prop('disabled', true);
      	jQuery('#option_show_login_menu').prop('checked', false);
    }
};

jQuery(".showInfo").tipTip({
  delay: 0,
  fadeIn: 200,
  fadeOut: 200,
  maxWidth: '300px',
  defaultPosition: 'bottom'
});

jQuery('#option_hide_menus').change(function() {
    update_options();
});
{/literal}
{/footer_script}