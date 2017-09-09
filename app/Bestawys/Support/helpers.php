<?php

/**
 * Check if the given url is exists.
 *
 * @param string $url
 * @return bool
 */
function urlExists($url) {
    return ! strpos(@get_headers($url)[0], '404');
}