{strip}
{combine_script id="clip" path=$SHAREALBUM_PATH|cat:"template/js/clipboard.min.js"}
{combine_css id="sharealbum" path=$SHAREALBUM_PATH|cat:"template/sharealbum_style.css"}

 <li class="nav-item dropdown">
	{* <!-- nothing more than the button itself must be defined here --> *}
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" title="{$T_SHAREALBUM_ALBUM_SHARE}">
		  <i class="fa fa-share-alt fa-fw" aria-hidden="true"></i><span class="d-lg-none ml-2"></span><span class="glyphicon sharealbum-button_active"></span>
		</a>
	{else}
		<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" title="{'Share'|translate}">
		  <i class="fa fa-share-alt fa-fw" aria-hidden="true"></i><span class="d-lg-none ml-2"></span><span class="glyphicon sharealbum-button_active"></span>
		</a>
	{/if}
	
	<div class="dropdown-menu dropdown-menu-right" role="menu">
	
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<span class="dropdown-item">{$T_SHAREALBUM_ALBUM_SHARED}</span>
		<span class="dropdown-item"><span class="sharealbum_url">{$SHAREALBUM_CODE}</span></span>
		
		<button class="sharealbum_button" data-clipboard-text="{$SHAREALBUM_CODE}">{$T_SHAREALBUM_COPY_TO_CLIPBOARD}</button><br>
	    <script>
	    	var clipboard = new Clipboard('.sharealbum_button');
	    	clipboard.on('success', function(e) {
				alert('{$T_SHAREALBUM_LINK_COPIED_SUCCESS}');
	    	});
	    	clipboard.on('error', function(e) {
	        	alert('{$T_SHAREALBUM_LINK_COPIED_FAILURE}');
	    	});
	    </script>
		<a class="dropdown-item" href="{$SHAREALBUM_LINK_RENEW}" onclick="return(confirm('{$T_SHAREALBUM_RENEW_WARNING}'));">{$T_SHAREALBUM_RENEW}</a>
		<a class="dropdown-item" href="{$SHAREALBUM_LINK_CANCEL}" onclick="return(confirm('{$T_SHAREALBUM_CANCEL_WARNING}'));">{$T_SHAREALBUM_CANCEL}</a>
	{else}
		<a href="{$SHAREALBUM_LINK_CREATE}" rel="nofollow">{$T_SHAREALBUM_SHARE}</a>
	{/if}
	</div>
{/strip}
{if (isset($SHAREALBUM_USER_MESSAGE) && $SHAREALBUM_USER_MESSAGE == 'link_created')}
<script>
	alert("{$T_SHAREALBUM_LINK_CREATED}");
</script>
{/if}
{if (isset($SHAREALBUM_USER_MESSAGE) && $SHAREALBUM_USER_MESSAGE == 'link_renewed')}
<script>
	alert("{$T_SHAREALBUM_LINK_RENEWED}");
	</script>
{/if}
{if (isset($SHAREALBUM_USER_MESSAGE) && $SHAREALBUM_USER_MESSAGE == 'link_cancelled')}
<script>
	alert("{$T_SHAREALBUM_LINK_CANCELLED}");
	</script>
{/if}
