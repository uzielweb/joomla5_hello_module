<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_legismapdiariosoficiaisrecentes
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Module\Legismapdiariosoficiaisrecentes\Site\Helper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Component\Content\Site\Model\ArticlesModel;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects
/**
 * Helper for mod_legismapdiariosoficiaisrecentes
 *
 * @since  1.6
 */
class LegismapdiariosoficiaisrecentesHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;
    /**
     * Retrieve a list of article
     *
     * @param   Registry       $params  The module parameters.
     * @param   ArticlesModel  $model   The model.
     *
     * @return  mixed
     *
     * @since   4.2.0
     */
    public function getItems(Registry $params, SiteApplication $app)
    {
        $db   = $this->getDatabase();
        $user = $app->getIdentity();
        $query = $db->getQuery(true)
            ->select('a.id, a.title')
            ->from('#__modules AS a')
            ->order('a.title ASC')
            ->setLimit($params->get('count', 5));
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }
    public static function getList(Registry $params)
    {
        return (new self())->getItems($params, Factory::getApplication());
    }
}
