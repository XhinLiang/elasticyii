<?php // vim:set ts=4 sw=4 et:

/**
 * This file is part of the ElasticSearch PHP client
 *
 * @property array|bool|null payload
 * @property int port
 * @property string protocol
 * @property string host
 * @property string method
 * @property mixed url
 * 
 * @author xhinliang
 * @author Raymond Julin <raymond.julin@gmail.com>
 */
class ElasticSearchHTTPException extends ElasticSearchException
{
    /**
     * Exception data
     * @var array
     */
    protected $data = array(
        'payload' => null,
        'protocol' => null,
        'port' => null,
        'host' => null,
        'url' => null,
        'method' => null,
    );

    /**
     * Setter
     * @param mixed $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->data))
            $this->data[$key] = $value;
    }

    /**
     * Getter
     * @param mixed $key
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data))
            return $this->data[$key];
        else
            return false;
    }

    /**
     * Rebuild CLI command using curl to further investigate the failure
     * @return string
     */
    public function getCLICommand()
    {
        $postData = json_encode($this->payload);
        $curlCall = "curl -X{$this->method} 'http://{$this->host}:{$this->port}{$this->url}' -d '$postData'";
        return $curlCall;
    }
}
