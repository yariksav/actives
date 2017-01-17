<?php
/**
 * @link
 * @copyright
 * @license
 */

namespace yariksav\actives\base;

/**
 * The method [[run()]] should be implemented to return the response.
 *
 * @author Savaryn Yaroslav
 */
interface RunnableInterface
{
    /**
     * @return object the response.
     */
    public function run($action = null);
}
