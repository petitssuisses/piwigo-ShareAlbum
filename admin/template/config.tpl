{combine_css path=$SHAREALBUM_PATH|@cat:"admin/template/style.css"}

{footer_script}
jQuery('input[name="option2"]').change(function() {
  $('.option1').toggle();
});

jQuery(".showInfo").tipTip({
  delay: 0,
  fadeIn: 200,
  fadeOut: 200,
  maxWidth: '300px',
  defaultPosition: 'bottom'
});
{/footer_script}


<div class="titrePage">
	<h2>Share Album</h2>
</div>
<div id="helpContent">
  <p>{$INTRO_CONTENT}</p>
</div>

<form method="post" action="" class="properties">
<fieldset>
  <legend>{'Shared albums options'|translate}</legend>
  <ul>
    <li>
      <label>
        <input type="checkbox" name="option_hide_menus" value="{$sharealbum.option_hide_menus}" {if $sharealbum.option_hide_menus}checked="checked"{/if}>
        <b>{'Hide menus for albums visitors'|translate}</b>
      </label>
      <a class="icon-info-circled-1 showInfo" title="{'When checked, menus are hidden for visitors of the shared album'|translate}"></a>
    </li>
    <li>
      <label>
        <input type="checkbox" name="option_replace_breadcrumbs" value="{$sharealbum.option_replace_breadcrumbs}" {if $sharealbum.option_replace_breadcrumbs}checked="checked"{/if}>
        <b>{'Replace navigation breadcrumbs with album name'|translate}</b>
      </label>
      <a class="icon-info-circled-1 showInfo" title="{'When checked, breadcrumbs are replaced with the album name'|translate}"></a>
    </li>
  </ul>
</fieldset>
<p class="formButtons"><input type="submit" name="save_config" value="{'Save Settings'|translate}"></p>
</form>