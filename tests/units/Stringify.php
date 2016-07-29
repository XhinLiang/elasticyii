<?php // vim:set ts=4 sw=4 et:
namespace ElasticSearch\DSL\tests\units;

use ElasticSearch\tests\Base;

require_once __DIR__ . '/../Base.php';

/**
 * This file is part of the ElasticSearch PHP client
 *
 * (c) Raymond Julin <raymond.julin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * @property mixed assert
 */
class Stringify extends Base
{
    public function testNamedTerm()
    {
        $arr = array(
            'query' => array(
                'term' => array('title' => 'cool')
            )
        );
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('title:cool');
    }

    public function testTerm()
    {
        $arr = array(
            'query' => array(
                'term' => 'cool'
            )
        );
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('cool');
    }

    public function testGroupedTerms()
    {
        $arr = array(
            'query' => array(
                'term' => 'cool stuff'
            )
        );
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('"cool stuff"');
    }

    public function testNamedGroupedTerms()
    {
        $arr = array(
            'query' => array(
                'term' => array('title' => 'cool stuff')
            )
        );
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('title:"cool stuff"');
    }

    public function testSort()
    {
        $arr = array(
            'sort' => array(
                array('title' => 'desc')
            ),
            'query' => array(
                'term' => array('title' => 'cool stuff')
            )
        );
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('title:"cool stuff"&sort=title:reverse');

        $arr['sort'] = array('title');
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('title:"cool stuff"&sort=title');

        $arr['sort'] = array(array('title' => array('reverse' => true)));
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('title:"cool stuff"&sort=title:reverse');
    }

    public function testLimitReturnFields()
    {
        $arr = array(
            'fields' => array('title', 'body'),
            'query' => array(
                'term' => array('title' => 'cool')
            )
        );
        $dsl = new \ElasticSearchStringify($arr);
        $this->assert->string((string)$dsl)
            ->isEqualTo('title:cool&fields=title,body');
    }
}
