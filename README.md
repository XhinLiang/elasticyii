# ElasticYii
[![Latest Stable Version](https://poser.pugx.org/xhinliang/elasticyii/v/stable)](https://packagist.org/packages/xhinliang/elasticyii)
[![Total Downloads](https://poser.pugx.org/xhinliang/elasticyii/downloads)](https://packagist.org/packages/xhinliang/elasticyii)
[![Latest Unstable Version](https://poser.pugx.org/xhinliang/elasticyii/v/unstable)](https://packagist.org/packages/xhinliang/elasticyii)
[![License](https://poser.pugx.org/xhinliang/elasticyii/license)](https://packagist.org/packages/xhinliang/elasticyii)
[![composer.lock](https://poser.pugx.org/xhinliang/elasticyii/composerlock)](https://packagist.org/packages/xhinliang/elasticyii)

ElasticSearch PHP client for yii

## Usage

### Install
```
composer require xhinliang/elasticyii
```

### Setup
#### Basic
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

### Use
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

## Licence
```
Copyright (c) 2010-2016 Xhin Liang
Copyright (c) 2010-2012 Raymond Julin

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```
