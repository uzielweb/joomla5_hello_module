<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_hello
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (!$list) {
    return;
}

?>
<div class="hello">
    <h3>
        <?php echo $params->get('greeting'); ?>
    </h3>
    <ul>
        <?php foreach ($list as $item) : ?>
            <li>
                <?php echo $item->title; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

