{combine_css path=$SHAREALBUM_PATH|@cat:"admin/template/style.css"}
{combine_script id="clip" path=$SHAREALBUM_PATH|cat:"template/js/clipboard.min.js"}
<link rel="stylesheet" href="{$SHAREALBUM_PATH|@cat:'template/css/font-awesome.min.css'}">

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

<div align="left">
<fieldset>
<legend>{'Share a new album'|@translate}</legend>
<form method="post" action="" class="properties">
<select name="new_share_cat" id="new_share_cat">
{if $shareable_albums|@count gt 0}
<option></option>
{else}
<option>{'No private album to be shared (or which is not yet shared)'|@translate}</option>
{/if}
{foreach from=$shareable_albums item=private_album}
<option value="{$private_album.id}">{$private_album.name}</option>
{/foreach}
</select>
<input type="submit" name="create" value="{'Share this album'|translate}" {if $shareable_albums|@count eq 0}disabled{/if}>
</form>

</fieldset>
<fieldset>
<legend>{'Active shares'|@translate}</legend>
<form method="post" action="" class="properties">
{'Please choose an action : '|@translate}<select name="p_sharedalbums_action">
{if $shared_albums|@count gt 0}
<option></option>
<option value="renew">{'Renew link'|@translate}</option>
<option value="cancel">{'Cancel sharing'|@translate}</option>
{else}
<option>{'No shared album'|@translate}</option>
{/if}
</select>
<input type="submit" name="action" value="{'Go'|translate}" {if $shared_albums|@count eq 0}disabled{/if}>
<br>
<table id="sharedAlbumsTable" align="left">
{if $shared_albums|@count gt 0}
<thead>
<tr>
	<th></th>
	<th class="dtc_date">{'Creation date'|@translate}</th>
	<th class="dtc_user">{'Album'|@translate}</th>
	<th class="">{'Shared link'|@translate}</th>
	<th class="">{'Visits'|@translate}</th>
	<th class="dtc_date">{'Last visit'|@translate}</th>
	<th></th>
</tr>
</thead>
{/if}
{foreach from=$shared_albums item=shared_album}
{strip}
<tr>
	<td><input type="radio" name="sa_cat" value="{$shared_album.category}" {if $smarty.post.sa_cat==$shared_album.category}checked="checked"{/if}></td>
	<td>{$shared_album.creation_date}</td>
	<td><i class="fa fa-user showInfo" title="{'User'|@translate}: {$shared_album.user}"></i>&nbsp; <a href="{$shared_root_path}/index.php?/category/{$shared_album.category}" alt="{$shared_album.album}">{$shared_album.album}</a></td>
	<td><button type="image" class="sharealbum_button fa fa-copy showInfo" title="{'Copy to clipboard'|@translate}" data-clipboard-text="{$shared_album.code}"></button>&nbsp;{$shared_album.code}</td>
	<td align="center"><a href="{$shared_album_logs}{$shared_album.category}">{$shared_album.visits}</a></td>
	<td align="center">{$shared_album.last_visit}</td>
	<td>	
		{if $log_category == $shared_album.category}&nbsp;{'Activity logs shown below'|@translate}{/if}
	</td>
</tr>

{/strip}
{/foreach}
<script>
	var clipboard = new Clipboard('.sharealbum_button');
	clipboard.on('success', function(e) {
		alert("{'Link was successfully copied to clipboard. You can now use system paste functionnality to share it !'|@translate}");
	});
	clipboard.on('error', function(e) {
		alert("{'The link was not copied to clipboard. Your browser may now support this functionnality.'|@translate}");
	});
</script>
</table>
</form>
</fieldset>
</div>
{if {$log_category} > 0}
<div align="left">
<fieldset>
<legend>{'Connection log for selected album'|@translate}</legend>
<table align="left">
	<thead>
		<tr>
			<th class="dtc_date">{'Visit date'|@translate}</th>
			<th>{'IP address'|@translate}</th>
		</tr>
	</thead>
	{foreach from=$shared_albums_logs item=shared_album_log}	
	<tr>
		<td>{$shared_album_log.visit_date}</td>
		<td>{$shared_album_log.ip}</td>
	</tr>
	{/foreach}
</table>
</fieldset>
{/if}

</div>