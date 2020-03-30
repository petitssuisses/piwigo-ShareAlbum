{strip}
{combine_script id="clip" path=$SHAREALBUM_PATH|cat:"template/js/clipboard.min.js"}
{combine_css id="sharealbum" path=$SHAREALBUM_PATH|cat:"template/sharealbum_style.css"}

<a id="sharealbumLink" title="{$T_SHAREALBUM_ALBUM_SHARE}" class="pwg-state-default pwg-button" rel="nofollow">
		<span class="pwg-icon sharealbum-button_active"></span><span class="pwg-button-text">{'Share'|translate}</span>
	</a>
	<div id="sharealbumBox" class="switchBox">
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<a title="{$T_SHAREALBUM_ALBUM_SHARE}" class="pwg-state-default pwg-button" rel="nofollow" class="dropdown-toggle" data-toggle="dropdown">
		  <span class="glyphicon sharealbum-button_active"> </span>
		</a>
	{else}
		<a title="{'Share'|translate}" class="pwg-state-default pwg-button" rel="nofollow" class="dropdown-toggle" data-toggle="dropdown">
		  <span class="glyphicon sharealbum-button_inactive"> </span>
		</a>
	{/if}
	
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		{'This album is shared via a public link'|translate}<br>
		<input class="sharealbum_input_url" id="sharealbum_code" selected="yes" type="text" size="{$SHAREALBUM_CODE|count_characters:true+10}" value="{$SHAREALBUM_CODE}"/><br>
	    <button class="sharealbum_button" data-clipboard-text="{$SHAREALBUM_CODE}">{$T_SHAREALBUM_COPY_TO_CLIPBOARD}</button><br>
		<a href="{$SHAREALBUM_LINK_RENEW}" onclick="return(confirm('{$T_SHAREALBUM_RENEW_WARNING}'));">{$T_SHAREALBUM_RENEW}</a><br>
		<a href="{$SHAREALBUM_LINK_CANCEL}" onclick="return(confirm('{$T_SHAREALBUM_CANCEL_WARNING}'));">{$T_SHAREALBUM_CANCEL}</a><br>
	{else}
		<a href="{$SHAREALBUM_LINK_CREATE}">{$T_SHAREALBUM_SHARE}</a>
	{/if}
	</div>
{footer_script}(
window.SwitchBox=window.SwitchBox||[]).push("#sharealbumLink", "#sharealbumBox");
{/footer_script}
{footer_script}
	    var clipboard = new Clipboard('.sharealbum_button');
	    clipboard.on('success', function(e) {
	        alert('{$T_SHAREALBUM_LINK_COPIED_SUCCESS}');
	    });
	    clipboard.on('error', function(e) {
	        alert('{$T_SHAREALBUM_LINK_COPIED_FAILURE}');
	    });
	    {/footer_script}
{if ($SHAREALBUM_USER_MESSAGE == 'link_created')}
<script>
	alert("{$T_SHAREALBUM_LINK_CREATED}");
</script>
{/if}
{if ($SHAREALBUM_USER_MESSAGE == 'link_renewed')}
<script>
	alert("{$T_SHAREALBUM_LINK_RENEWED}");
	</script>
{/if}
{if ($SHAREALBUM_USER_MESSAGE == 'link_cancelled')}
<script>
	alert("{$T_SHAREALBUM_LINK_CANCELLED}");
	</script>
{/if}
