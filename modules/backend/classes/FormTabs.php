<?php namespace Backend\Classes;

use IteratorAggregate;
use ArrayIterator;
use ArrayAccess;

/**
 * FormTabs is a translation of the form field tab configuration
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class FormTabs implements IteratorAggregate, ArrayAccess
{
    const SECTION_OUTSIDE = 'outside';
    const SECTION_PRIMARY = 'primary';
    const SECTION_SECONDARY = 'secondary';

    /**
     * @var string section specifies the form section these tabs belong to
     */
    public $section = 'outside';

    /**
     * @var array fields is a collection of panes fields to these tabs
     */
    public $fields = [];

    /**
     * @var array lazy is the n Names of tabs to lazy load
     */
    public $lazy = [];

    /**
     * @var string defaultTab is default tab label to use when none is specified
     */
    public $defaultTab = 'backend::lang.form.undefined_tab';

    /**
     * @var array icons lists of icons for their corresponding tabs
     */
    public $icons = [];

    /**
     * @var bool stretch should these tabs stretch to the bottom of the page layout
     */
    public $stretch;

    /**
     * @var boolean suppressTabs if set to TRUE, fields will not be displayed in tabs
     */
    public $suppressTabs = false;

    /**
     * @var string cssClass cpecifies a CSS class to attach to the tab container
     */
    public $cssClass;

    /**
     * @var array paneCssClass specifies a CSS class to an individual tab pane
     */
    public $paneCssClass;

    /**
     * @var bool linkable means tab gets url fragment to be linkable
     */
    public $linkable = true;

    /**
     * __construct specifies a tabs rendering section. Supported sections are:
     * - outside - stores a section of "tabless" fields.
     * - primary - tabs section for primary fields.
     * - secondary - tabs section for secondary fields.
     * @param string $section Specifies a section as described above.
     * @param array $config A list of render mode specific config.
     */
    public function __construct($section, $config = [])
    {
        $this->section = strtolower($section) ?: $this->section;
        $this->config = $this->evalConfig($config);

        if ($this->section === self::SECTION_OUTSIDE) {
            $this->suppressTabs = true;
        }
    }

    /**
     * evalConfig process options and apply them to this object
     */
    protected function evalConfig($config)
    {
        if (array_key_exists('defaultTab', $config)) {
            $this->defaultTab = $config['defaultTab'];
        }

        if (array_key_exists('icons', $config)) {
            $this->icons = $config['icons'];
        }

        if (array_key_exists('stretch', $config)) {
            $this->stretch = $config['stretch'];
        }

        if (array_key_exists('suppressTabs', $config)) {
            $this->suppressTabs = $config['suppressTabs'];
        }

        if (array_key_exists('cssClass', $config)) {
            $this->cssClass = $config['cssClass'];
        }

        if (array_key_exists('paneCssClass', $config)) {
            $this->paneCssClass = $config['paneCssClass'];
        }

        if (array_key_exists('lazy', $config)) {
            $this->lazy = $config['lazy'];
        }
    }

    /**
     * addField to the collection of tabs
     * @param string    $name
     * @param FormField $field
     * @param string    $tab
     */
    public function addField($name, FormField $field, $tab = null)
    {
        if (!$tab) {
            $tab = $this->defaultTab;
        }

        $this->fields[$tab][$name] = $field;
    }

    /**
     * removeField from all tabs by name
     * @param string    $name
     * @return boolean
     */
    public function removeField($name)
    {
        foreach ($this->fields as $tab => $fields) {
            foreach ($fields as $fieldName => $field) {
                if ($fieldName === $name) {
                    unset($this->fields[$tab][$fieldName]);

                    /*
                     * Remove empty tabs from collection
                     */
                    if (!count($this->fields[$tab])) {
                        unset($this->fields[$tab]);
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * hasFields returns true if any fields have been registered for these tabs
     * @return boolean
     */
    public function hasFields()
    {
        return count($this->fields) > 0;
    }

    /**
     * getFields returns an array of the registered fields, including tabs
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * getAllFields returns an array of the registered fields, without tabs
     * @return array
     */
    public function getAllFields()
    {
        $tablessFields = [];

        foreach ($this->getFields() as $tab) {
            $tablessFields += $tab;
        }

        return $tablessFields;
    }

    /**
     * getIcon returns an icon for the tab based on the tab's name
     * @param string $name
     * @return string
     */
    public function getIcon($name)
    {
        if (!empty($this->icons[$name])) {
            return $this->icons[$name];
        }
    }

    /**
     * getPaneCssClass returns a tab pane CSS class
     * @param string $index
     * @param string $label
     * @return string
     */
    public function getPaneCssClass($index = null, $label = null)
    {
        if (is_string($this->paneCssClass)) {
            return $this->paneCssClass;
        }

        if ($index !== null && isset($this->paneCssClass[$index])) {
            return $this->paneCssClass[$index];
        }

        if ($label !== null && isset($this->paneCssClass[$label])) {
            return $this->paneCssClass[$label];
        }
    }

    /**
     * getIterator gets an iterator for the items
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator(
            $this->suppressTabs
                ? $this->getAllFields()
                : $this->getFields()
        );
    }

    /**
     * offsetSet is an ArrayAccess implementation
     */
    public function offsetSet($offset, $value)
    {
        $this->fields[$offset] = $value;
    }

    /**
     * offsetExists is an ArrayAccess implementation
     */
    public function offsetExists($offset)
    {
        return isset($this->fields[$offset]);
    }

    /**
     * offsetUnset is an ArrayAccess implementation
     */
    public function offsetUnset($offset)
    {
        unset($this->fields[$offset]);
    }

    /**
     * offsetGet is an ArrayAccess implementation
     */
    public function offsetGet($offset)
    {
        return $this->fields[$offset] ?? null;
    }
}
