<?php // vim:set ts=4 sw=4 et:
/**
 * This file is part of the ElasticSearch PHP client for Yii
 *
 * @author xhinliang
 * @author Raymond Julin <raymond.julin@gmail.com>
 */
class ElasticSearchClient extends CApplicationComponent
{
    const DEFAULT_PROTOCOL = 'http';
    const DEFAULT_SERVER = '127.0.0.1:9200';
    const DEFAULT_INDEX = 'default-index';
    const DEFAULT_TYPE = 'default-type';

    protected $_config = array();

    protected static $_defaults = array(
        'protocol' => self::DEFAULT_PROTOCOL,
        'servers' => self::DEFAULT_SERVER,
        'index' => self::DEFAULT_INDEX,
        'type' => self::DEFAULT_TYPE,
        'timeout' => null,
    );

    protected static $_protocols = array(
        'http' => 'ElasticSearchHTTP',
        'memcached' => 'ElasticSearchMemcached',
    );

    /**
     * @var ElasticSearchBase
     */
    private $transport;

    private $index, $type;
    /**
     * @var ElasticSearchBulk
     */
    private $bulk;

    /**
     * @var mixed
     */
    public $config;

    /**
     * Construct search client
     *
     * @return ElasticSearchClient
     * @param \ElasticSearchBase $transport
     * @param string $index
     * @param string $type
     */
    private function transConstruct($transport, $index = null, $type = null)
    {
        $this->transport = $transport;
        $this->setIndex($index)->setType($type);
    }

    public function init()
    {
        parent::init();
        $this->initConfig($this->config);
    }

    private function initConfig($config = array())
    {
        $config = $config ? $config : array();
        if (is_string($config)) {
            $config = self::parseDsn($config);
        }
        $config = array_merge(self::$_defaults, $config);
        $protocol = $config['protocol'];
        if (!isset(self::$_protocols[$protocol])) {
            throw new ElasticSearchException("Tried to use unknown protocol: $protocol");
        }
        $class = self::$_protocols[$protocol];
        if (null !== $config['timeout'] && !is_numeric($config['timeout'])) {
            throw new ElasticSearchException("HTTP timeout should have a numeric value when specified.");
        }
        $server = is_array($config['servers']) ? $config['servers'][0] : $config['servers'];
        list($host, $port) = explode(':', $server);
        $transport = new $class($host, $port, $config['timeout']);
        $this->transConstruct($transport, $config['index'], $config['type']);
        $this->configuration($config);
    }

    /**
     * @author xhinliang
     * @param $id
     * @return bool
     */
    public function checkExist($id)
    {
        $queryResult = $this->get($id);
        if (isset($queryResult['found']) && !$queryResult['found']) {
            return false;
        }
        return true;
    }

    /**
     * @param array|null $config
     * @return array|void
     */
    public function configuration($config = null)
    {
        if (!$config)
            return $this->_config;
        if (is_array($config))
            $this->_config = array_merge($this->_config, $config);
        return null;
    }

    /**
     * Get a new ElasticSearch client with configuration
     * 
     * @author xhinliang
     * @param array $config
     * @return ElasticSearchClient
     * @throws ElasticSearchException
     */
    public static function connection($config = array())
    {
        if (!$config && ($url = getenv('ELASTICSEARCH_URL'))) {
            $config = $url;
        }
        if (is_string($config)) {
            $config = self::parseDsn($config);
        }
        $config = array_merge(self::$_defaults, $config);
        $protocol = $config['protocol'];
        if (!isset(self::$_protocols[$protocol])) {
            throw new ElasticSearchException("Tried to use unknown protocol: $protocol");
        }
        $class = self::$_protocols[$protocol];
        if (null !== $config['timeout'] && !is_numeric($config['timeout'])) {
            throw new ElasticSearchException("HTTP timeout should have a numeric value when specified.");
        }
        $server = is_array($config['servers']) ? $config['servers'][0] : $config['servers'];
        list($host, $port) = explode(':', $server);
        $transport = new $class($host, $port, $config['timeout']);
        $client = new self();
        $client->transConstruct($transport, $config['index'], $config['type']);
        $client->configuration($config);
        return $client;
    }

    /**
     * Change what index to go against
     * @return \ElasticSearchClient
     * @param mixed $index
     */
    public function setIndex($index)
    {
        if (is_array($index))
            $index = implode(",", array_filter($index));
        $this->index = $index;
        $this->transport->setIndex($index);
        return $this;
    }

    /**
     * Change what types to act against
     * @return \ElasticSearchClient
     * @param mixed $type
     */
    public function setType($type)
    {
        if (is_array($type))
            $type = implode(",", array_filter($type));
        $this->type = $type;
        $this->transport->setType($type);
        return $this;
    }

    /**
     * Fetch a document by its id
     *
     * @return array
     * @param mixed $id Optional
     * @param bool $verbose
     */
    public function get($id, $verbose = false)
    {
        return $this->request($id, "GET", $payload = false, $verbose);
    }

    /**
     * Puts a mapping on index
     *
     * @param array|object $mapping
     * @param array $config
     * @throws Exception
     * @return array
     */
    public function map($mapping, $config = array())
    {
        if (is_array($mapping)) $mapping = new ElasticSearchMapping($mapping);
        $mapping->config($config);

        try {
            $type = $mapping->config('type');
        } catch (Exception $e) {
        } // No type is cool
        if (isset($type) && !$this->passesTypeConstraint($type)) {
            throw new ElasticSearchException("Cant create mapping due to type constraint mismatch");
        }

        return $this->request('_mapping', 'PUT', $mapping->export(), true);
    }

    protected function passesTypeConstraint($constraint)
    {
        if (is_string($constraint)) $constraint = array($constraint);
        $currentType = explode(',', $this->type);
        $includeTypes = array_intersect($constraint, $currentType);
        return ($constraint && count($includeTypes) === count($constraint));
    }

    /**
     * Perform a raw request
     *
     * Usage example
     *
     *     $response = $client->request('_status', 'GET');
     *
     * @return array
     * @param mixed $path Request path to use.
     *     `type` is prepended to this path inside request
     * @param string $method HTTP verb to use
     * @param mixed $payload Array of data to be json-encoded
     * @param bool $verbose Controls response data, if `false`
     *     only `_source` of response is returned
     */
    public function request($path, $method = 'GET', $payload = false, $verbose = false)
    {
        $response = $this->transport->request($this->expandPath($path), $method, $payload);
        return ($verbose || !isset($response['_source']))
            ? $response
            : $response['_source'];
    }

    /**
     * Index a new document or update it if existing
     *
     * @return array
     * @param array $document
     * @param mixed $id Optional
     * @param array $options Allow sending query parameters to control indexing further
     *        _refresh_ *bool* If set to true, immediately refresh the shard after indexing
     */
    public function index($document, $id = false, $options = array())
    {
        if ($this->bulk) {
            return $this->bulk->index($document, $id, $this->index, $this->type, $options);
        }
        return $this->transport->index($document, $id, $options);
    }

    /**
     * Update a part of a document
     *
     * @return array
     *
     * @param array $partialDocument
     * @param mixed $id
     * @param array $options Allow sending query parameters to control indexing further
     *                        _refresh_ *bool* If set to true, immediately refresh the shard after indexing
     */
    public function update($partialDocument, $id, $options = array())
    {
        if ($this->bulk) {
            return $this->bulk->update($partialDocument, $id, $this->index, $this->type, $options);
        }
        return $this->transport->update($partialDocument, $id, $options);
    }

    /**
     * Perform search, this is the sweet spot
     *
     * @return array
     * @param $query
     * @param array $options
     */
    public function search($query, $options = array())
    {
        $start = microtime(true);
        $result = $this->transport->search($query, $options);
        $result['time'] = microtime(true) - $start;
        return $result;
    }

    /**
     * Flush this index/type combination
     *
     * @return array
     * @param mixed $id If id is supplied, delete that id for this index
     *                  if not wipe the entire index
     * @param array $options Parameters to pass to delete action
     */
    public function delete($id = false, $options = array())
    {
        if ($this->bulk) {
            return $this->bulk->delete($id, $this->index, $this->type, $options);
        }
        return $this->transport->delete($id, $options);
    }

    /**
     * Flush this index/type combination
     *
     * @return array
     * @param mixed $query Text or array based query to delete everything that matches
     * @param array $options Parameters to pass to delete action
     */
    public function deleteByQuery($query, $options = array())
    {
        return $this->transport->deleteByQuery($query, $options);
    }

    /**
     * Perform refresh of current indexes
     *
     * @return array
     */
    public function refresh()
    {
        return $this->transport->request(array('_refresh'), 'GET');
    }

    /**
     * Expand a given path (array or string)
     * If this is not an absolute path index + type will be prepended
     * If it is an absolute path it will be used as is
     *
     * @param mixed $path
     * @return array
     */
    protected function expandPath($path)
    {
        $path = (array)$path;
        $isAbsolute = $path[0][0] === '/';

        return $isAbsolute
            ? $path
            : array_merge((array)$this->type, $path);
    }

    /**
     * Parse a DSN string into an associative array
     *
     * @param string $dsn
     * @return array
     */
    protected static function parseDsn($dsn)
    {
        $parts = parse_url($dsn);
        $protocol = $parts['scheme'];
        $servers = $parts['host'] . ':' . $parts['port'];
        if (isset($parts['path'])) {
            $path = explode('/', $parts['path']);
            list($index, $type) = array_values(array_filter($path));
        }
        return compact('protocol', 'servers', 'index', 'type');
    }

    /**
     * Create a bulk-transaction
     *
     * @return \ElasticSearchBulk
     */

    public function createBulk()
    {
        return new ElasticSearchBulk($this);
    }


    /**
     * Begin a transparent bulk-transaction
     * if one is already running, return its handle
     * @return \ElasticSearchBulk
     */

    public function beginBulk()
    {
        if (!$this->bulk) {
            $this->bulk = $this->createBulk();
        }
        return $this->bulk;
    }

    /**
     * @see beginBulk
     */
    public function begin()
    {
        return $this->beginBulk();
    }

    /**
     * commit a bulk-transaction
     * @return array
     * @throws Exception
     */

    public function commitBulk()
    {
        if ($this->bulk && $this->bulk->count()) {
            $result = $this->bulk->commit();
            $this->bulk = null;
            return $result;
        }
        throw  new ElasticSearchException('bulk error!');
    }

    /**
     * @see commitBulk
     */
    public function commit()
    {
        return $this->commitBulk();
    }

}
