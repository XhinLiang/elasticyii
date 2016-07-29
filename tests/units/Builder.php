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
class Builder extends Base
{
    public function testTermQuery()
    {
        $dsl = new \ElasticSearchBuilder;
        $query = $dsl->query();
        $query->term("cool", "title");

        $arr = array(
            'query' => array(
                'term' => array('title' => 'cool')
            )
        );
        $built = $dsl->build();
        $this->assert->array($built)->isEqualTo($arr);

        $query->wildcard("cool*", "title");
        $this->assert->array($dsl->build())->isEqualTo($arr);
    }

    public function testRangeQuery()
    {
        $dsl = new \ElasticSearchBuilder;
        $query = $dsl->query();
        $query->range(array(
            'age' => array(
                'from' => 18,
                'to' => 100,
                'includeLower' => true,
                'includeUpper' => false,
                'boost' => 2.0
            )
        ));

        // This is how it should turn out
        $arr = array(
            'query' => array(
                'range' => array(
                    'age' => array(
                        'from' => 18,
                        'to' => 100,
                        'includeLower' => true,
                        'includeUpper' => false,
                        'boost' => 2.0
                    )
                )
            )
        );

        $this->assert->array($dsl->build())->isEqualTo($arr);
    }

    public function testRangeQueryAlternativeSyntax()
    {
        $dsl = new \ElasticSearchBuilder;
        $query = $dsl->query();
        $range = $query->range();
        $range->fieldname('age')
            ->from(18)
            ->to(100)
            ->includeUpper(false)
            ->includeLower(false)
            ->boost(2.0);

        // This is how it should turn out
        $arr = array(
            'query' => array(
                'range' => array(
                    'age' => array(
                        'from' => 18,
                        'to' => 100,
                        'includeLower' => false,
                        'includeUpper' => false,
                        'boost' => 2.0
                    )
                )
            )
        );

        $this->assert->array($dsl->build())->isEqualTo($arr);
    }

    public function testSortClause()
    {
        $sort = array('title' => 'desc');
        $dsl = new \ElasticSearchBuilder(compact('sort'));
        $dsl->query()->term("cool", "title");

        $this->assert->array($dsl->build())
            ->isEqualTo(array(
                'query' => array(
                    'term' => array('title' => 'cool')
                ),
                'sort' => $sort
            ));
    }
}
