<?php
/**
 * Trails plugin for Craft CMS 3.x
 *
 * Craft 3 Twig filter to help generate breadcrumbs based off of URL segments
 *
 * @link      https://github.com/lxcodes
 * @copyright Copyright (c) 2018 Alexander Ambrose
 */

namespace lxcodes\trails\services;

use lxcodes\trails\Trails;

use Craft;
use craft\base\Component;
use craft\helpers\Template;

/**
 * TrailsService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Alexander Ambrose
 * @package   Trails
 * @since     0.0.1
 */
class TrailsService extends Component
{
    private $_params;

    // Public Methods
    // =========================================================================

    /**
     * Get a navigation structure as HTML.
     *
     * @param array $params
     *
     * @return string
     */
    public function getBreadcrumbs($params)
    {
        // Get the params
        $this->_setParams($params);
        // Return built HTML
        return $this->_buildBreadcrumbsHtml();
    }

    /**
     * Get breadcrumbs without HTML.
     *
     * @return array
     */
    public function getBreadcrumbsRaw()
    {
        // Return the active URI elements
        return $this->_getActiveElements();
    }

    // Private Methods
    // =========================================================================

    /**
     * Set parameters for the navigation HTML output.
     *
     * @param array $params
     */
    private function _setParams($params)
    {
        $this->_params = array();
        foreach ($params as $paramKey => $paramValue) {
            $this->_params[$paramKey] = $paramValue;
        }
    }

    /**
     * Get parameter value.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    private function _getParam($name, $default)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : $default;
    }

    /**
     * Get active elements based on URI.
     *
     * @return array
     */
    private function _getActiveElements()
    {
        $elements = array();
        $segments = Craft::$app->request->getSegments();
        // Add homepage
        $element = Craft::$app->elements->getElementByUri('__home__');
        if ($element) {
            $elements[] = $element;
        }
        // Find other elements
        if (count($segments)) {
            $count = 0; // Start at second
            $segmentString = $segments[0]; // Add first
            while ($count < count($segments)) {
                // Get element
                $element = Craft::$app->elements->getElementByUri($segmentString);
                // Add element to active elements
                if ($element) {
                    $elements[] = $element;
                }
                // Search for next possible element
                $count ++;
                if (isset($segments[$count])) {
                    $segmentString .= '/' . $segments[$count];
                }
            }
        }
        return $elements;
    }

        /**
     * Create the breadcrumbs HTML.
     *
     * @return string
     */
    private function _buildBreadcrumbsHtml()
    {
        // Get active elements
        $nodes = $this->_getActiveElements();
        // Do we have custom nodes?
        $customNodes = $this->_getParam('customNodes', false);
        if ($customNodes && is_array($customNodes) && count($customNodes)) {
            $nodes = array_merge($nodes, $customNodes);
        }
        // Create breadcrumbs
        $length = count($nodes);
        $breadcrumbs = "\n" . sprintf('<%1$s%2$s%3$s xmlns:v="http://rdf.data-vocabulary.org/#">',
            $this->_getParam('wrapper', 'ol'),
            $this->_getParam('id', false) ? ' id="' . $this->_getParam('id', '') . '"' : '',
            $this->_getParam('class', false) ? ' class="' . $this->_getParam('class', '') . '"' : ''
        );
        // Before text
        if ($this->_getParam('beforeText', false)) {
            $breadcrumbs .= sprintf("\n" . '<li%1$s><span>%2$s</span></li>',
                $this->_getParam('classDefault', false) ? ' class="' . $this->_getParam('classDefault', '') . '"' : '',
                $this->_getParam('beforeText', '')
            );
        }
        foreach ($nodes as $index => $node) {
            $nodeTitle = is_array($node) ? (isset($node['title']) ? $node['title'] : Craft::t('Unknown')) : $node->__toString();
            $nodeUrl = is_array($node) ? (isset($node['url']) ? $node['url'] : '') : $node->url;
            // Gather node classes
            $childClasses = array();
            if ($this->_getParam('classDefault', false)) {
                $childClasses[] = $this->_getParam('classDefault', '');
            }
            // First
            if ($index == 0) {
                $childClasses[] = $this->_getParam('classFirst', 'first');
                $breadcrumbs .= sprintf("\n" . '<li%1$s typeof="v:Breadcrumb"><a href="%2$s" title="%3$s" rel="v:url" property="v:title">%3$s</a></li>',
                    $childClasses ? ' class="' . implode(' ', $childClasses) . '"' : '',
                    $nodeUrl,
                    $this->_getParam('renameHome', $nodeTitle)
                );
            }
            // Last
            elseif ($index == $length - 1)
            {
                $childClasses[] = $this->_getParam('classLast', 'last');
                $breadcrumb = sprintf('<span property="v:title">%1$s</span>',
                    $nodeTitle
                );
                if ($this->_getParam('lastIsLink', false)) {
                    $breadcrumb = sprintf('<a href="%1$s" title="%2$s" rel="v:url" property="v:title">%2$s</a>',
                        $nodeUrl,
                        $nodeTitle
                    );
                }
                $breadcrumbs .= sprintf("\n" . '<li%1$s typeof="v:Breadcrumb">%2$s</li>',
                    $childClasses ? ' class="' . implode(' ', $childClasses) . '"' : '',
                    $breadcrumb
                );
            }
            else {
                $breadcrumbs .= sprintf("\n" . '<li%1$s typeof="v:Breadcrumb"><a href="%2$s" title="%3$s" rel="v:url" property="v:title">%3$s</a></li>',
                    $childClasses ? ' class="' . implode(' ', $childClasses) . '"' : '',
                    $nodeUrl,
                    $nodeTitle
                );
            }
        }
        $breadcrumbs .= "\n" . sprintf('</%1$s>',
            $this->_getParam('wrapper', 'ol')
        );

        return \craft\helpers\Template::raw($breadcrumbs);
    }
}
