<?php // vim:set ts=4 sw=4 et:
/**
 * This file is part of the ElasticSearch PHP client
 *
 * @author xhinliang
 * @author Raymond Julin <raymond.julin@gmail.com>
 */
class ElasticSearchMapping
{

    protected $properties = array();
    protected $config = array();

    /**
     * Build mapping data
     *
     * @param array $properties
     * @param array $config
     * @return \ElasticSearchMapping
     */
    public function __construct(array $properties = array(), array $config = array())
    {
        $this->properties = $properties;
        $this->config = $config;
    }

    /**
     * Export mapping data as a json-ready array
     *
     * @return string
     */
    public function export()
    {
        return array(
            'properties' => $this->properties
        );
    }

    /**
     * Add or overwrite existing field by name
     *
     * @param string $field
     * @param string|array $config
     * @return $this
     */
    public function field($field, $config = array())
    {
        if (is_string($config)) $config = array('type' => $config);
        $this->properties[$field] = $config;
        return $this;
    }

    /**
     * Get or set a config
     *
     * @param string $key
     * @param mixed $value
     * @return array|void
     * @throws Exception
     */
    public function config($key, $value = null)
    {
        if (is_array($key))
            $this->config = $key + $this->config;
        if ($value !== null)
            $this->config[$key] = $value;
        if (!isset($this->config[$key]))
            throw new ElasticSearchException("Configuration key `type` is not set");
        return $this->config[$key];
    }
}
