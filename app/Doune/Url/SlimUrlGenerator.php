<?php

namespace Doune\Url;

use Slim\Slim;

class SlimUrlGenerator implements UrlGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function route($name, array $params = [])
    {
        return Slim::getInstance()->urlFor($name, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return Slim::getInstance()->request->getUrl();
    }
}