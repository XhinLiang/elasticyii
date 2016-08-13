# ElasticYii
ElasticSearch PHP client for yii
---
[![Latest Stable Version](https://poser.pugx.org/xhinliang/elasticyii/v/stable)](https://packagist.org/packages/xhinliang/elasticyii)
[![Total Downloads](https://poser.pugx.org/xhinliang/elasticyii/downloads)](https://packagist.org/packages/xhinliang/elasticyii)
[![Latest Unstable Version](https://poser.pugx.org/xhinliang/elasticyii/v/unstable)](https://packagist.org/packages/xhinliang/elasticyii)
[![License](https://poser.pugx.org/xhinliang/elasticyii/license)](https://packagist.org/packages/xhinliang/elasticyii)
[![composer.lock](https://poser.pugx.org/xhinliang/elasticyii/composerlock)](https://packagist.org/packages/xhinliang/elasticyii)

## Usage

### install
```
composer require xhinliang/elasticyii
```

### setup
#### basic
Just add the component in your `config/main.php`
```
'components' => array(
    'esclient' => array(
        'class' => 'ElasticSearchClient',
    ),
),
```
#### Personalization
You can define your configurations in the `config/main.php`
```
'components' => array(
    'esclient' => array(
        'class' => 'ElasticSearchClient',
        'config' => array(
            'protocol' => 'http',
            'servers' => '127.0.0.1:9200',
            'index' => 'yourindex',
            'type' => 'yourtype',
            'timeout' => null,
        )
    ),
),
```
1. protocol
This option will define the protocol ElasticYii will use.
- **http** means the http protocol, and the client will use ElasticSearchHTTP inside.
- **memcached** means the memcached protocol, and the client will use ElasticSearchMemcached inside.

2. servers
The server ip and port. 
For example
```
`servers` => `222.22.22.44:9200'
```
default `127.0.0.1:9200`

3. index, type
the default `index` and `type`

4. timeout
the timeout, can be `null` or number.

### use
just enjoy it.
```
$esResult = Yii::app()->esclient
    ->setIndex("index")
    ->setType("type")
    ->index($json, $id);
    
$esResult = Yii::app()
    ->esclient
    ->setIndex($type)
    ->setType($group)
    ->get($id);
```

