<?php

namespace Doune\Url;

interface UrlGeneratorInterface
{
    /**
     * Get the url for the given route.
     *
     * @param string $name
     * @param array $params
     * @return string
     */
    public function route($name, array $params = []);

    /**
     * Get the current url.
     *
     * @return string
     */
    public function current();
}