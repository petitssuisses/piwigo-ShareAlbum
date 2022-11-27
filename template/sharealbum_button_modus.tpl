{strip}
{combine_script id="clip" path=$SHAREALBUM_PATH|cat:"template/js/clipboard.min.js"}
{combine_css id="sharealbum" path=$SHAREALBUM_PATH|cat:"template/sharealbum_style.css"}
{combine_css path=$SHAREALBUM_PATH|cat:"template/css/font-awesome.css"}
{footer_script}(
window.SwitchBox=window.SwitchBox||[]).push("#sharealbumSwitchLink", "#sharealbumBox");
{/footer_script}

<li><a id="sharealbumSwitchLink" title="{$T_SHAREALBUM_ALBUM_SHARE}" class="pwg-state-default pwg-button" rel="nofollow"> <span style="font-size: 22px;" class="pwg-icon fa fa-share-alt fa-2x fa-fw"></span><span class="pwg-button-text">{$T_SHAREALBUM_ALBUM_SHARE}</span> </a>
	<div id="sharealbumBox" class="switchBox">
		{if ($SHAREALBUM_LINK_IS_ACTIVE == 1)}
		<div class="switchBoxTitle">{$T_SHAREALBUM_ALBUM_SHARED}</div>
		<span style="visibility:hidden">&#x2714; </span>
		<input class="sharealbum_input_url" id="sharealbum_code" selected="yes" type="text" size="{$SHAREALBUM_CODE|count_characters:true+10}" value="{$SHAREALBUM_CODE}"><br/>
		<button class="sharealbum_button" data-clipboard-text="{$SHAREALBUM_CODE}">{$T_SHAREALBUM_COPY_TO_CLIPBOARD}</button><br/>
	    <script>
	    var clipboard = new Clipboard('.sharealbum_button');
	    clipboard.on('success', function(e) {
	        alert('{$T_SHAREALBUM_LINK_COPIED_SUCCESS}');
	    });
	    clipboard.on('error', function(e) {
	        alert('{$T_SHAREALBUM_LINK_COPIED_FAILURE}');
	    });
	    </script>
		<span style="visibility:hidden">&#x2714; </span>
		<a href="{$SHAREALBUM_LINK_RENEW}" onclick="return(confirm('{$T_SHAREALBUM_RENEW_WARNING}'));">{$T_SHAREALBUM_RENEW}</a><br/>
		<span style="visibility:hidden">&#x2714; </span>
		<a href="{$SHAREALBUM_LINK_CANCEL}" onclick="return(confirm('{$T_SHAREALBUM_CANCEL_WARNING}'));">{$T_SHAREALBUM_CANCEL}</a>
		{else}
		<a href="{$SHAREALBUM_LINK_CREATE}" rel="nofollow">{$T_SHAREALBUM_SHARE}</a>
		{/if}
	</div>
</li>

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
