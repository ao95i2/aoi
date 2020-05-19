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
<ul id="awesome5-settings">
<li>
<p class="control-title"><?php echo __( 'How to load icon', 'luxeritas' ); ?></p>
<p class="radio">
<input type="radio" value="css" name="awesome_load"<?php thk_value_check( 'awesome_load', 'radio', 'css' ); ?> />
<?php echo __( 'Web Fonts with CSS', 'luxeritas' ); ?>
</p>
<p class="radio">
<input type="radio" value="svg" name="awesome_load"<?php thk_value_check( 'awesome_load', 'radio', 'svg' ); ?> />
<?php echo __( 'SVG with JavaScript', 'luxeritas' ); ?>
</p>
<p>cf. <a href="https://fontawesome.com/how-to-use/on-the-web/other-topics/performance" target="_blank" rel="nofollow noopener noreferrer">Performance &amp; Font Awesome</a></p>
</li>

<li>
<p class="control-title"><?php echo __( 'Backward compatibility', 'luxeritas' ); ?></p>
<p class="checkbox">
<input type="checkbox" value="" name="awesome_4_support"<?php thk_value_check( 'awesome_4_support', 'checkbox' ); ?> />
<?php echo __( 'Font Awesome 4 compatibility support', 'luxeritas' ); ?>
</p>
</li>
</ul>
