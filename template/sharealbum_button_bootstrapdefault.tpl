{strip}
{combine_script id="clip" path=$SHAREALBUM_PATH|cat:"template/js/clipboard.min.js"}
{combine_css id="sharealbum" path=$SHAREALBUM_PATH|cat:"template/sharealbum_style.css"}

 <li class="dropdown"">
	{* <!-- nothing more than the button itself must be defined here --> *}
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<a href="#" title="{$T_SHAREALBUM_ALBUM_SHARE}" class="pwg-state-default pwg-button" rel="nofollow" class="dropdown-toggle" data-toggle="dropdown">
		  <span class="glyphicon sharealbum-button_active"> </span>
		</a>
	{else}
		<a href="#" title="{'Share'|translate}" class="pwg-state-default pwg-button" rel="nofollow" class="dropdown-toggle" data-toggle="dropdown">
		  <span class="glyphicon sharealbum-button_inactive"> </span>
		</a>
	{/if}
	<ul class="dropdown-menu" role="menu">
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<li class="sharealbum_li">{$T_SHAREALBUM_ALBUM_SHARED}</li>
		<li><input class="sharealbum_input_url" id="sharealbum_code" selected="yes" type="text" size="{$SHAREALBUM_CODE|count_characters:true+10}" value="{$SHAREALBUM_CODE}"></li>
		<li><button class="sharealbum_button" data-clipboard-text="{$SHAREALBUM_CODE}">{$T_SHAREALBUM_COPY_TO_CLIPBOARD}</button></li>
	    <script>
	    var clipboard = new Clipboard('.sharealbum_button');
	    clipboard.on('success', function(e) {
	        alert('{$T_SHAREALBUM_LINK_COPIED_SUCCESS}');
	    });
	    clipboard.on('error', function(e) {
	        alert('{$T_SHAREALBUM_LINK_COPIED_FAILURE}');
	    });
	    </script>
		<li><a href="{$SHAREALBUM_LINK_RENEW}" onclick="return(confirm('{$T_SHAREALBUM_RENEW_WARNING}'));">{$T_SHAREALBUM_RENEW}</a></li>
		<li><a href="{$SHAREALBUM_LINK_CANCEL}" onclick="return(confirm('{$T_SHAREALBUM_CANCEL_WARNING}'));">{$T_SHAREALBUM_CANCEL}</a></li>
	{else}
		<li><a href="{$SHAREALBUM_LINK_CREATE}">{$T_SHAREALBUM_SHARE}</a></li>
	{/if}
	</ul>
</li>
{/strip}
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
