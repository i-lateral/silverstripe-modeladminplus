<?php

namespace ilateral\SilverStripe\ModelAdminPlus;

use SilverStripe\View\ViewableData;

/**
 * Object representing a snippet of generic data that can be loaded at the top of a
 * ModelAdminPlus interface
 */
abstract class ModelAdminSnippet extends ViewableData
{
    const PRIMARY = "primary";

    const SECONDARY = "secondary";

    const SUCCESS = "success";

    const INFO = "info";

    const WARNING = "warning";

    const DANGER = "danger";

    const LIGHT = "light";

    const DARK = "dark";

    const WHITE = "white";

    /**
     * The name/title of the current snippet.
     * 
     * @var string
     */
    private static $title;

    /**
     * The order in which this snippet will be loaded
     * 
     * @var int
     */
    private static $priority = 0;

    /**
     * Default background colour
     * 
     * @var string
     */
    private static $background = self::INFO;

    /**
     * Default text colour
     * 
     * @var string
     */
    private static $text = self::WHITE;

    /**
     * The current parent controller
     *
     * @var ModelAdminPlus
     */
    protected $parent;

    /**
     * List of extra CSS classes applied to this snippet
     *
     * @var array
     */
    protected $extra_classes = [];

    /**
     * Return an i18n friendly version of the title.
     *
     * @return string
     */
    public function getTitle()
    {
        return _t(__CLASS__ . "Title", $this->config()->title);
    }

    /**
     * Render the current snippet
     * 
     * @return string
     */
    public function getSnippet()
    {
        return $this->renderWith(__CLASS__);
    }

    /**
     * Return the background colour suitable for a template
     *
     * @return string
     */
    public function getBackgroundColour()
    {
        return $this->config()->background;
    }

    /**
     * Return the background colour suitable for a template
     *
     * @return string
     */
    public function getTextColour()
    {
        return $this->config()->text;
    }

    /**
     * The content of this snippet that will be rendered below
     * the title.
     *
     * @return string
     */
    abstract public function getContent();

    /**
     * Get the current parent controller
     *
     * @return ModelAdminPlus
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the current parent controller
     *
     * @param ModelAdminPlus $parent The current parent controller
     *
     * @return self
     */
    public function setParent(ModelAdminPlus $parent)
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get extra CSS classes as a string
     *
     * @return string
     */
    public function getExtraClasses()
    {
        return implode(" ", $this->extra_classes);
    }

    /**
     * Add additional css classes
     *
     * @param  array|string  $extra_classes  extra CSS classes
     *
     * @return  self
     */
    public function addExtraClasses($classes)
    {
        if (!is_array($classes)) {
            $classes = explode(" ", $classes);
        }

        $this->extra_classes = array_merge(
            $this->extra_classes,
            $classes
        );

        return $this;
    }

    /**
     * Remove provided CSS classes
     *
     * @param  array|string  $extra_classes  extra CSS classes
     *
     * @return  self
     */
    public function removeExtraClasses($classes)
    {
        if (!is_array($classes)) {
            $classes = explode(" ", $classes);
        }

        foreach ($classes as $class) {
            if (isset($this->extra_classes[$class])) {
                unset($this->extra_classes[$class]);
            }
        }

        return $this;
    }
}
