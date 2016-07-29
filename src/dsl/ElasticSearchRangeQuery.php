<?php // vim:set ts=4 sw=4 et:

/**
 * Range queries
 *
 * @author xhinliang
 * @author Raymond Julin <raymond.julin@gmail.com>
 */
class ElasticSearchRangeQuery
{
    protected $fieldname = null;
    protected $from = null;
    protected $to = null;
    protected $includeLower = null;
    protected $includeUpper = null;
    protected $boost = null;


    /**
     * Construct new RangeQuery component
     *
     * @return \ElasticSearchRangeQuery
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->fieldname = key($options);
        $values = current($options);
        if (is_array($values)) {
            foreach ($values as $key => $val)
                $this->$key = $val;
        }
    }

    /**
     * Setters
     *
     * @return \ElasticSearchRangeQuery
     * @param mixed $value
     */
    public function fieldname($value)
    {
        $this->fieldname = $value;
        return $this;
    }

    /**
     * @param $value
     * @return \ElasticSearchRangeQuery $this
     */
    public function from($value)
    {
        $this->from = $value;
        return $this;
    }

    /**
     * @param $value
     * @return \ElasticSearchRangeQuery $this
     */
    public function to($value)
    {
        $this->to = $value;
        return $this;
    }

    /**
     * @param $value
     * @return \ElasticSearchRangeQuery $this
     */
    public function includeLower($value)
    {
        $this->includeLower = $value;
        return $this;
    }

    /**
     * @param $value
     * @return \ElasticSearchRangeQuery $this
     */
    public function includeUpper($value)
    {
        $this->includeUpper = $value;
        return $this;
    }

    /**
     * @param $value
     * @return \ElasticSearchRangeQuery $this
     */
    public function boost($value)
    {
        $this->boost = $value;
        return $this;
    }

    /**
     * Build to array
     *
     * @throws \ElasticSearchException
     * @return array
     */
    public function build()
    {
        $built = array();
        if ($this->fieldname) {
            $built[$this->fieldname] = array();
            foreach (array("from", "to", "includeLower", "includeUpper", "boost") as $opt) {
                if ($this->$opt !== null)
                    $built[$this->fieldname][$opt] = $this->$opt;
            }
            if (count($built[$this->fieldname]) == 0)
                throw new ElasticSearchException("Empty RangeQuery cant be created");
        }
        return $built;
    }
}
