<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_legismapdiariosoficiaisrecentes
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
$wa->registerAndUseStyle('mod_legismapdiariosoficiaisrecentes_css', 'media/mod_legismapdiariosoficiaisrecentes/css/style.css');
$wa->registerAndUseScript('mod_legismapdiariosoficiaisrecentes_script', 'media/mod_legismapdiariosoficiaisrecentes/js/script.js');
if (!$list) {
    return;
}

?>
<div class="legismapdiariosoficiaisrecentes">
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

