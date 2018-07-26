<?php
/**
 * trails plugin for Craft CMS 3.x
 *
 * AA
 *
 * @link      https://www.google.com
 * @copyright Copyright (c) 2018 AA
 */

namespace lxcodes\trails\variables;

use lxcodes\trails\Trails;

use Craft;

/**
 * trails Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.trails }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    AA
 * @package   Trails
 * @since     0.0.1
 */
class TrailsVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.trails.exampleVariable }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.trails.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function exampleVariable($optional = null)
    {
        $result = "And away we go to the Twig template...";
        if ($optional) {
            $result = "I'm feeling optional today...";
        }
        return $result;
    }

    /**
     * Get breadcrumbs as HTML.
     *
     * @param array  $params
     *
     * Params possibilities:
     * - id             ID for the breadcrumbs wrapper.
     * - class          Class name for the breadcrumbs wrapper.
     * - classDefault   Default class name for every breadcrumb.
     * - classFirst     Class name for the first breadcrumb.
     * - classLast      Class name for the last breadcrumb.
     * - wrapper        Wrapper element without the < and >.
     * - beforeText     Text before the first item, like 'You are here:'.
     * - renameHome     Change the title of the home entry.
     * - lastIsLink     Whether the last breadcrumb should be a link.
     * - customNodes    Add custom nodes after the elements are handled.
     *                  [ { title: 'A title', url: 'an url' }, { title: 'A title', url: 'an url' } ]
     *
     * @return string
     */
    public function getBreadcrumbs($params = array())
    {
        return Trails::$plugin->trailsService->getBreadcrumbs($params);
    }

    public function getBreadcrumbsRaw() {
        return Trails::$plugin->trailsService->getBreadcrumbsRaw();
    }
}
