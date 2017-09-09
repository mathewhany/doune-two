<?php

namespace Bestawys\Theme;

class ThemeManager
{
    /**
     * The path of the themes directory.
     *
     * @var string
     */
    protected $themesDir;

    /**
     * The url of the assets directory.
     *
     * @var string
     */
    protected $assetsDir;

    /**
     * The name of the current used theme.
     *
     * @var string
     */
    protected $defaultTheme;

    /**
     * The name of the fallback theme.
     *
     * @var string
     */
    protected $fallbackTheme;

    /**
     * Constructor
     *
     * @param string $themesDir
     * @param string $assetsDir
     * @param string $defaultTheme
     * @param string $fallbackTheme
     */
    public function __construct($themesDir, $assetsDir, $defaultTheme, $fallbackTheme)
    {
        $this->themesDir = $themesDir;
        $this->assetsDir = $assetsDir;
        $this->defaultTheme = $defaultTheme;
        $this->fallbackTheme = $fallbackTheme;
    }

    /**
     * Get the path of the themes directory.
     *
     * @return string
     */
    public function getThemesDir()
    {
        return $this->themesDir;
    }

    /**
     * Set the path of the themes directory.
     *
     * @param string $themesDir
     */
    public function setThemesDir($themesDir)
    {
        $this->themesDir = $themesDir;
    }

    /**
     * Get the url of the assets directory.
     *
     * @return string
     */
    public function getAssetsDir()
    {
        return $this->assetsDir;
    }

    /**
     * Set the url of the assets directory.
     *
     * @param string $assetsDir
     */
    public function setAssetsDir($assetsDir)
    {
        $this->assetsDir = $assetsDir;
    }

    /**
     * Get the name of the current used theme.
     *
     * @return string
     */
    public function getDefaultTheme()
    {
        return $this->defaultTheme;
    }

    /**
     * Set the name of the current used theme.
     *
     * @param string $defaultTheme
     */
    public function setDefaultTheme($defaultTheme)
    {
        $this->defaultTheme = $defaultTheme;
    }

    /**
     * Get the name of the fallback theme.
     *
     * @return string
     */
    public function getFallbackTheme()
    {
        return $this->fallbackTheme;
    }

    /**
     * Set the name of the fallback theme.
     *
     * @param string $fallbackTheme
     */
    public function setFallbackTheme($fallbackTheme)
    {
        $this->fallbackTheme = $fallbackTheme;
    }

    /**
     * Get the path of the given theme.
     *
     * @param string $themeName
     * @return string
     */
    public function pathFor($themeName)
    {
       return $this->themesDir . '/' . $themeName;
    }

    /**
     * Is the given theme exist?
     *
     * @param string $themeName
     * @return bool
     */
    public function hasTheme($themeName)
    {
        return isset($this->automaticDetect()[$themeName]);
    }

    /**
     * Get the url of the given asset.
     *
     * @param string $assetName
     * @param string $themeName
     * @return string
     */
    public function asset($assetName, $themeName = null)
    {
        $themeName = $themeName ?: $this->defaultTheme;

        $assetUrl = $this->assetsDir . '/' . $themeName . '/'. $assetName;

        if ($themeName != $this->fallbackTheme && !urlExists($assetUrl)) {
            $assetUrl = $this->asset($assetName, $this->fallbackTheme);
        }

        return $assetUrl;
    }

    /**
     * Read the theme configuration and return its info.
     *
     * @param string $themeName
     * @return array
     */
    public function getThemeInfo($themeName)
    {
        return parse_ini_file(
            $this->pathFor($themeName) . '/' . $themeName . '.theme'
        );
    }

    /**
     * Automatic detect themes in the themes directory.
     *
     * @return array
     */
    public function automaticDetect()
    {
        $themes = [];

        foreach (glob($this->themesDir . '/*') as $themeDir) {
            $themeName = basename($themeDir);
            $themes[$themeName] = $this->getThemeInfo($themeName);
        }

        return $themes;
    }
}