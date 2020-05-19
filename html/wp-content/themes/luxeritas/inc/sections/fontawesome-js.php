<?php
/**
 * Luxeritas WordPress Theme - free/libre wordpress platform
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * @copyright Copyright (C) 2015 Thought is free.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 * @author LunaNuko
 * @link https://thk.kanzae.net/
 * @translators rakeem( http://rakeem.jp/ )
 */

?>
<ul id="svg-with-javascript">
<li>
<p class="control-title"><?php echo __( 'Javascript loading source', 'luxeritas' ); ?></p>
<p class="radio">
<input type="radio" value="cdn" name="awesome_load_js_file"<?php thk_value_check( 'awesome_load_js_file', 'radio', 'cdn' ); ?> />
CDN
</p>
<p class="radio">
<input type="radio" value="local" name="awesome_load_js_file"<?php thk_value_check( 'awesome_load_js_file', 'radio', 'local' ); ?> />
<?php echo __( 'Local', 'luxeritas' ); ?>
</p>
</li>
</ul>

<script>
jQuery(document).ready(function($) {
	var e = function() {
		var a = $("#awesome5-settings")
		,   s = $("#svg-with-javascript")
		,   o = $("#web-fonts-with-css")
		,   v = $('input[name="awesome_version"]:checked').val()
		,   n = $('input[name="awesome_load"]:checked').val();
		if( 5 == v ) {
			a.removeAttr("style");
			"svg" !== n ? (s.css("opacity", ".6"), s.css("pointer-events", "none")) : s.removeAttr("style");
			"css" !== n ? (o.css("opacity", ".6"), o.css("pointer-events", "none")) : o.removeAttr("style");
		} else if( 4 == v ) {
			s.css("opacity", ".6"), s.css("pointer-events", "none");
			a.css("opacity", ".6"), a.css("pointer-events", "none");
			o.removeAttr("style");
		} else {
			s.css("opacity", ".6"), s.css("pointer-events", "none");
			a.css("opacity", ".6"), a.css("pointer-events", "none");
			o.css("opacity", ".6"), o.css("pointer-events", "none");
		}
	};
	e();
	$('input[name="awesome_version"]').on("click", function() {
		n = this.getAttribute("value");
		e();
	}), $('input[name="awesome_load"]').on("click", function() {
		n = this.getAttribute("value");
		e();
	}), $('input[name="awesome_load_css_file"]').on("click", function() {
		var e = $('input[name="awesome_load_file"]');
		"cdn" === this.getAttribute("value") ? (e.prop("checked", !0), $('input[name="awesome_load_file"][value="cdn"]').prop("checked", !0), e.css("opacity", ".4"), e.css("pointer-events", "none")) : e.removeAttr("style");
	})
});
</script>
