<?php
/**
 * Defines the interface for QueryFilters
 *
 * Implementing this interface will allow one to create a chain of QueryFilters
 * with the use of Waf_Model_QueryFilter, which is the default QueryFilter used
 * by Waf_Model_Repository
 *
 * Name your filters as precise as possible. For example;
 * It's ok to have a ByDate filter. But if you do a lot of queries for today or
 * tomorrow, create a today and tomorrow filter, which may extend the ByDate
 * filter.
 *
 * There's no limit of classes to use so ROCK OUT!
 *
 * @see Waf_Model_QueryFilter
 * @see Waf_Model_Repository
 * @category   Waf
 * @package    Waf_Model
 * @subpackage QueryFilter
 * @version    $Id: $
 */
interface Waf_Model_QueryFilter_QueryFilterInterface
{
    /**
     * Filter passed $query. For example:
     *
     * public function filter($query)
     * {
     *     return $query->where('enabled = ?', 1);
     * }
     *
     * @param mixed $query
     */
    public function filter($query);
}