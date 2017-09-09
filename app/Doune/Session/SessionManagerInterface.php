<?php

namespace Doune\Session;

interface SessionManagerInterface
{
    /**
     * Get the value of the input from the session.
     *
     * @param string $name
     * @param string|null $default
     * @return string
     */
    public function getOldInput($name, $default = null);
}
