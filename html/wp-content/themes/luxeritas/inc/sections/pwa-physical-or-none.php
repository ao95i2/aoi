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

global $_is;
?>
<ul>
<li>
<p class="control-title"><?php echo __( 'How to output Service Worker and Manifest files.', 'luxeritas' ); ?></p>
<p class="checkbox">
<input type="checkbox" value="" name="pwa_dynamic_files"<?php thk_value_check( 'pwa_dynamic_files', 'checkbox' ); ?> />
<?php echo __( 'Dynamic', 'luxeritas' ); ?>
<p><span class="f09em bg-gray"><?php echo __( '* If the root directory does not have write permission, please check dynamic.', 'luxeritas' ); ?></span></p>
</li>
</ul>
