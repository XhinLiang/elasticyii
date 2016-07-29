<?php // vim:set ts=4 sw=4 et:

/**
 * Handle the query sub dsl
 *
 * @author xhinliang
 * @author Raymond Julin <raymond.julin@gmail.com>
 */
class ElasticSearchQuery
{
    protected $term = null;
    /**
     * @var ElasticSearchRangeQuery
     */
    protected $range;
    protected $prefix = null;
    protected $wildcard = null;
    protected $matchAll = null;
    protected $queryString = null;
    protected $bool = null;
    protected $disMax = null;
    protected $constantScore = null;
    protected $filteredQuery = null;

    public function __construct($options = array())
    {
    }

    /**
     * Add a term to this query
     *
     * @return \ElasticSearchQuery
     * @param string $term
     * @param bool|string $field
     */
    public function term($term, $field = false)
    {
        $this->term = ($field)
            ? array($field => $term)
            : $term;
        return $this;
    }

    /**
     * Add a wildcard to this query
     *
     * @return \ElasticSearchQuery
     * @param $val
     * @param bool|string $field
     */
    public function wildcard($val, $field = false)
    {
        $this->wildcard = ($field)
            ? array($field => $val)
            : $val;
        return $this;
    }

    /**
     * Add a range query
     *
     * @return \ElasticSearchRangeQuery
     * @param array $options
     */
    public function range(array $options = array())
    {
        $this->range = new ElasticSearchRangeQuery($options);
        return $this->range;
    }

    /**
     * Build the DSL as array
     *
     * @return array
     */
    public function build()
    {
        $built = array();
        if ($this->term)
            $built['term'] = $this->term;
        elseif ($this->range)
            $built['range'] = $this->range->build();
        elseif ($this->wildcard)
            $built['wildcard'] = $this->wildcard;
        return $built;
    }
}
