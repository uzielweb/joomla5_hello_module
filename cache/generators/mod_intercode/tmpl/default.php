<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_intercode
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('mod_intercode_css', 'media/mod_intercode/css/style.css');
$wa->registerAndUseScript('mod_intercode_script', 'media/mod_intercode/js/script.js');
if (!$list) {
    return;
}

?>
<div class="intercode">
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

