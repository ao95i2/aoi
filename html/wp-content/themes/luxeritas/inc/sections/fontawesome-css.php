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
<ul id="web-fonts-with-css">
<li>
<p class="control-title"><?php echo __( 'How to load CSS', 'luxeritas' ); ?></p>
<select name="awesome_load_async">
<option value="sync"<?php thk_value_check( 'awesome_load_async', 'select', 'sync' ); ?>><?php echo __( 'Synchronism', 'luxeritas' ), ' (', __( 'No delays in icon font', 'luxeritas' ), ')'; ?></option>
<option value="async"<?php thk_value_check( 'awesome_load_async', 'select', 'async' ); ?>><?php echo __( 'Asynchronous', 'luxeritas' ), ' (', __( 'High rendering speed', 'luxeritas' ), ')'; ?></option>
</select>
</li>

<li>
<p class="control-title"><?php echo __( 'CSS loading source', 'luxeritas' ); ?></p>
<p class="radio">
<input type="radio" value="cdn" name="awesome_load_css_file"<?php thk_value_check( 'awesome_load_css_file', 'radio', 'cdn' ); ?> />
CDN
</p>
<p class="radio">
<input type="radio" value="local" name="awesome_load_css_file"<?php thk_value_check( 'awesome_load_css_file', 'radio', 'local' ); ?> />
<?php echo __( 'Local', 'luxeritas' ); ?>
</p>
</li>

<li>
<p class="control-title"><?php echo __( 'Font file loading source', 'luxeritas' ); ?></p>
<p class="radio">
<input type="radio" value="cdn" name="awesome_load_file"<?php thk_value_check( 'awesome_load_file', 'radio', 'cdn' ); ?> />
CDN
</p>
<p class="radio">
<input type="radio" value="local" name="awesome_load_file"<?php thk_value_check( 'awesome_load_file', 'radio', 'local' ); ?> />
<?php echo __( 'Local', 'luxeritas' ); ?>
</p>
</li>
</ul>
