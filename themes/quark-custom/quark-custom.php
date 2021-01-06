<?php
namespace Grav\Theme;

use Grav\Common\Theme;

class QuarkCustom extends Quark
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
        ];
    }
    
    /**
     * Set needed variables to display the search results.
     *
     * @return void
     */
    public function onTwigSiteVariables()
    {
        $twig = $this->grav['twig'];

        // Add FontAwesome Globally
        if ($this->config->get('theme.include_fontawesome')) {
            $this->grav['assets']->add('theme://css/fontAwesome.css');
        }

        if ($this->config->get('theme.dark_mode')) {
            $this->grav['assets']->add('theme://css/darkMode.css');
            $this->grav['assets']->addJs('theme://js/darkMode.js', ['group' => 'bottom']);
        }
    }
}
