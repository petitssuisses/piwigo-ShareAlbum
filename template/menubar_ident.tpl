{if ($sharealbum_theme=='bootstrap_darkroom')}
<li class="nav-item dropdown" id="identificationDropdown">
	<a href="{$sharealbum_login_link}" class="nav-link" onclick="return(confirm('{'This will disconnect you from the current album and load the identification page. Do you confirm ?'|translate}'));">{'Login'|@translate}</a>
</li>
{else if ($sharealbum_theme=='bootstrapdefault')}
<li class="nav-item">
	<a href="{$sharealbum_login_link}" onclick="return(confirm('{'This will disconnect you from the current album and load the identification page. Do you confirm ?'|translate}'));">{'Login'|@translate} <span class="caret"></span></a>
</li>
{else}
<dt><a href="{$sharealbum_login_link}" onclick="return(confirm('{'This will disconnect you from the current album and load the identification page. Do you confirm ?'|translate}'));">{'Login'|@translate}</a></dt>
{/if}
