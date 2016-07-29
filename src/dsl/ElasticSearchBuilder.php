<?php // vim:set ts=4 sw=4 et:

/**
 * Helper stuff for working with the ElasticSearch DSL
 * How to build a mildly complex query:
 * $dsl = new ElasticSearchDSL;
 * $bool = $dsl->bool(); // Return a new bool structure
 *
 * @author xhinliang
 * @author Raymond Julin <raymond.julin@gmail.com>
 */
class ElasticSearchBuilder
{

    protected $dsl = array();

    //private $explain = null;
    //private $fields = null;
    //private $facets = null;
    
    private $from = null;
    private $size = null;
    
    /**
     * @var ElasticSearchQuery
     */
    private $query = null;
    private $sort = null;

    /**
     * Construct DSL object
     *
     * @return \ElasticSearchBuilder
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        foreach ($options as $key => $value)
            $this->$key = $value;
    }

    /**
     * Add array clause, can only be one
     *
     * @return \ElasticSearchQuery
     * @param array $options
     */
    public function query(array $options = array())
    {
        if (!($this->query instanceof ElasticSearchQuery))
            $this->query = new ElasticSearchQuery($options);
        return $this->query;
    }

    /**
     * Build the DSL as array
     *
     * @throws \ElasticSearchException
     * @return array
     */
    public function build()
    {
        $built = array();
        if ($this->from != null)
            $built['from'] = $this->from;
        if ($this->size != null)
            $built['size'] = $this->size;
        if ($this->sort && is_array($this->sort))
            $built['sort'] = $this->sort;
        if (!$this->query)
            throw new ElasticSearchException("Query must be specified");
        else
            $built['query'] = $this->query->build();
        return $built;
    }
}
