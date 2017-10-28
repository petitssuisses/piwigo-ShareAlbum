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
  <p>{'Share Album plugin enables you to share albums through a simple click.<br>
  For each shared album, it generates for a unique URL enabling autologin using a (unique) user, then having only access to the shared album.<br>
  <br>Share menu is available to administrator users in each Private category page'|@translate}</p>
</div>

</form>