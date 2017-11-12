{strip}
{combine_script id="clip" path=$SHAREALBUM_PATH|cat:"template/js/clipboard.min.js"}
{combine_css id="sharealbum" path=$SHAREALBUM_PATH|cat:"template/sharealbum_style.css"}

 <li class="nav-item dropdown">
	{* <!-- nothing more than the button itself must be defined here --> *}
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" title="{'Share'|translate}">
		  <i class="fa fa-share-alt fa-fw" aria-hidden="true"></i><span class="d-lg-none ml-2"></span><span class="glyphicon sharealbum-button_active"></span>
		</a>
	{else}
		<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" title="{'Share'|translate}">
		  <i class="fa fa-share-alt fa-fw" aria-hidden="true"></i><span class="d-lg-none ml-2"></span><span class="glyphicon sharealbum-button_active"></span>
		</a>
	{/if}
	
	<div class="dropdown-menu dropdown-menu-right" role="menu">
	
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<span class="dropdown-item">{'This album is shared via a public link'|translate}</span>
		<span class="dropdown-item"><span class="sharealbum_url">{$SHAREALBUM_CODE}</span></span>
		
		<button class="sharealbum_button" data-clipboard-text="{$SHAREALBUM_CODE}">{'Copy to clipboard'|translate}</button><br>
	    <script>
	    	var clipboard = new Clipboard('.sharealbum_button');
	    	clipboard.on('success', function(e) {
	        	alert("{'Link was successfully copied to clipboard. You can now use system paste functionnality to share it !'|translate}");
	    	});
	    	clipboard.on('error', function(e) {
	        	alert("{'Please select the link and use the Edit > Copy function from your browser.'|translate}");
	    	});
	    </script>
		<a class="dropdown-item" href="{$SHAREALBUM_LINK_RENEW}" onclick="return(confirm('{'You are going to renew the shared link for this album. Previously communicated link will no more be active. Do you confirm ?'|translate}'));">{'Renew link'|translate}</a>
		<a class="dropdown-item" href="{$SHAREALBUM_LINK_CANCEL}" onclick="return(confirm('{'Are you sure you wish to cancel this album sharing ?'|translate}'));">{'Cancel sharing'|translate}</a>
	{else}
		<a class="dropdown-item" href="{$SHAREALBUM_LINK_CREATE}">{'Share this album'|translate}</a>
	{/if}
	</div>
{/strip}
{if ($SHAREALBUM_USER_MESSAGE == 'link_created')}
<script>
	alert("{'Share link was successfully created. Click the share button to display it.'|translate}");
</script>
{/if}
{if ($SHAREALBUM_USER_MESSAGE == 'link_renewed')}
<script>
	alert("{'Link was successfully renewed. Click the share button to display it.'|translate}");
	</script>
{/if}
{if ($SHAREALBUM_USER_MESSAGE == 'link_cancelled')}
<script>
	alert("{'Link was successfully deleted. Album is no longer publicly shared.'|translate}");
	</script>
{/if}
