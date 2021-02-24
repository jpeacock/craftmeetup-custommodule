<?php
/**
 * Example module for Craft CMS 3.x
 *
 * Rah Rah API
 *
 * @link      https://clearmpls.com
 * @copyright Copyright (c) 2021 John Peacock
 */

namespace modules\examplemodule\services;

use modules\examplemodule\ExampleModule;

use Craft;
use craft\base\Component;

/**
 * Api Service
 *
 * All of your module’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other modules can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    John Peacock
 * @package   ExampleModule
 * @since     1
 */
class Api extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin/module file, call it like this:
     *
     *     ExampleModule::$instance->api->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';

        return $result;
    }
}
