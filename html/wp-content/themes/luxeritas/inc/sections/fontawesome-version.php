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
<ul>
<li>
<p class="control-title">Font Awesome <?php echo __( 'Version', 'luxeritas' ); ?></p>
<p class="radio">
<input type="radio" value="5" name="awesome_version"<?php thk_value_check( 'awesome_version', 'radio', 5 ); ?> />
Font Awesome 5
</p>
<p class="radio">
<input type="radio" value="4" name="awesome_version"<?php thk_value_check( 'awesome_version', 'radio', 4 ); ?> />
Font Awesome 4
</p>
<p class="radio">
<input type="radio" value="none" name="awesome_version"<?php thk_value_check( 'awesome_version', 'radio', 'none' ); ?> />
<?php echo __( 'Don&apos;t load icon font', 'luxeritas' ), ' ( ',  __( 'For loading with plugin', 'luxeritas' ), ' )'; ?>
</p>
</li>
</ul>
