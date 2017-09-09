<?php

namespace Doune\Session;

class SlimSessionManager implements SessionManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOldInput($name, $default = null)
    {
        return isset($_SESSION['slim.flash']['input'][$name]) ? $_SESSION['slim.flash']['input'][$name] : $default;
    }
}