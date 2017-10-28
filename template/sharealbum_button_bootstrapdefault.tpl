{strip}
{combine_script id="clip" path=$SHAREALBUM_PATH|cat:"template/js/clipboard.min.js"}
{combine_css id="sharealbum" path=$SHAREALBUM_PATH|cat:"template/sharealbum_style.css"}

 <li class="dropdown"">
	{* <!-- nothing more than the button itself must be defined here --> *}
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<a href="#" title="{'Share'|translate}" class="pwg-state-default pwg-button" rel="nofollow" class="dropdown-toggle" data-toggle="dropdown">
		  <span class="glyphicon sharealbum-button_active"> </span>
		</a>
	{else}
		<a href="#" title="{'Share'|translate}" class="pwg-state-default pwg-button" rel="nofollow" class="dropdown-toggle" data-toggle="dropdown">
		  <span class="glyphicon sharealbum-button_inactive"> </span>
		</a>
	{/if}
	<ul class="dropdown-menu" role="menu">
	{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<li class="sharealbum_li">{'This album is shared via a public link'|translate}</li>
		<li><input class="sharealbum_input_url" id="sharealbum_code" selected="yes" type="text" size="{$SHAREALBUM_CODE|count_characters:true+10}" value="{$SHAREALBUM_CODE}"></li>
		<li><button class="sharealbum_button" data-clipboard-text="{$SHAREALBUM_CODE}">{'Copy to clipboard'|translate}</button></li>
	    <script>
	    var clipboard = new Clipboard('.sharealbum_button');
	    clipboard.on('success', function(e) {
	        alert("{'Link was successfully copied to clipboard. You can now use system paste functionnality to share it !'|translate}");
	    });
	    clipboard.on('error', function(e) {
	        alert("{'Please select the link and use the Edit > Copy function from your browser.'|translate}");
	    });
	    </script>
		<li><a href="{$SHAREALBUM_LINK_RENEW}" onclick="return(confirm('{'You are going to renew the shared link for this album. Previously communicated link will no more be active. Do you confirm ?'|translate}'));">{'Renew link'|translate}</a></li>
		<li><a href="{$SHAREALBUM_LINK_CANCEL}" onclick="return(confirm('{'Are you sure you wish to cancel this album sharing ?'|translate}'));">{'Cancel sharing'|translate}</a></li>
	{else}
		<li><a href="{$SHAREALBUM_LINK_CREATE}">{'Share this album'|translate}</a></li>
	{/if}
	</ul>
</li>
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
