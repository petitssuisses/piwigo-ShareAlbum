{combine_css path=$SHAREALBUM_PATH|@cat:"admin/template/style.css"}
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
<legend>{'Active shares'|@translate}</legend>

<table id="sharedAlbumsTable" align="left">
<thead>
<tr>
	<th></th>
	<th class="dtc_date">{'Creation date'|@translate}</th>
	<th class="dtc_user">{'Album'|@translate}</th>
	<th class="">{'Visits'|@translate}</th>
	<th class="dtc_date">{'Last visit'|@translate}</th>
	<th></th>
</tr>
</thead>

{foreach from=$shared_albums item=shared_album}
{strip}
<tr>
<td></td>
<td>{$shared_album.creation_date}</td>
<td><a href="#"><i class="fa fa-user showInfo" title="{'User'|@translate}: {$shared_album.user}"></a></i>&nbsp; <a href="{$shared_root_path}/index.php?/category/{$shared_album.category}">{$shared_album.album}</a></td>
<td align="center">{$shared_album.visits}</td>
<td align="center">{$shared_album.last_visit}</td>
</tr>
{/strip}
{/foreach}
</table>

</fieldset>
</div>