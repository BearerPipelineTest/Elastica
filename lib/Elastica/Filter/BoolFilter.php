<?php
namespace Elastica\Filter;

use Elastica\Exception\InvalidException;

/**
 * Bool Filter.
 *
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-filter.html
 */
class BoolFilter extends AbstractFilter
{
    /**
     * Must.
     *
     * @var array
     */
    protected $_must = array();

    /**
     * Should.
     *
     * @var array
     */
    protected $_should = array();

    /**
     * Must not.
     *
     * @var array
     */
    protected $_mustNot = array();

    /**
     * Adds should filter.
     *
     * @param array|\Elastica\Filter\AbstractFilter $args Filter data
     *
     * @return $this
     */
    public function addShould($args)
    {
        return $this->_addFilter('should', $args);
    }

    /**
     * Adds must filter.
     *
     * @param array|\Elastica\Filter\AbstractFilter $args Filter data
     *
     * @return $this
     */
    public function addMust($args)
    {
        return $this->_addFilter('must', $args);
    }

    /**
     * Adds mustNot filter.
     *
     * @param array|\Elastica\Filter\AbstractFilter $args Filter data
     *
     * @return $this
     */
    public function addMustNot($args)
    {
        return $this->_addFilter('mustNot', $args);
    }

    /**
     * Adds general filter based on type.
     *
     * @throws \Elastica\Exception\InvalidException
     *
     * @param string                                $type Filter type
     * @param array|\Elastica\Filter\AbstractFilter $args Filter data
     *
     * @return $this
     */
    protected function _addFilter($type, $args)
    {
        if ($args instanceof AbstractFilter) {
            $args = $args->toArray();
        } elseif (!is_array($args)) {
            throw new InvalidException('Invalid parameter. Has to be array or instance of Elastica\Filter');
        } else {
            $parsedArgs = array();
            foreach ($args as $filter) {
                if ($filter instanceof AbstractFilter) {
                    $parsedArgs[] = $filter->toArray();
                }
            }
            $args = $parsedArgs;
        }

        $varName = '_'.$type;
        $this->{$varName}[] = $args;

        return $this;
    }

    /**
     * Converts bool filter to array.
     *
     * @see \Elastica\Filter\AbstractFilter::toArray()
     *
     * @return array Filter array
     */
    public function toArray()
    {
        $args = array();

        if (!empty($this->_must)) {
            $args['bool']['must'] = $this->_must;
        }

        if (!empty($this->_should)) {
            $args['bool']['should'] = $this->_should;
        }

        if (!empty($this->_mustNot)) {
            $args['bool']['must_not'] = $this->_mustNot;
        }

        if (isset($args['bool'])) {
            $args['bool'] = array_merge($args['bool'], $this->getParams());
        }

        return $args;
    }
}
