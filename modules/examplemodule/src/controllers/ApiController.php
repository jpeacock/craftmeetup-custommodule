<?php
/**
 * Example module for Craft CMS 3.x
 *
 * Example API
 *
 * @link      https://clearmpls.com
 * @copyright Copyright (c) 2021 John Peacock
 */

namespace modules\examplemodule\controllers;

use modules\examplemodule\ExampleModule;
use modules\examplemodule\models\Api;

use Craft;
use craft\web\Controller;


/**
 * Api Controller
 *
 * Generally speaking, controllers are the middlemen between the front end of
 * the CP/website and your module’s services. They contain action methods which
 * handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering
 * post data, saving it on a model, passing the model off to a service, and then
 * responding to the request appropriately depending on the service method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what
 * the method does (for example, actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    John Peacock
 * @package   ExampleModule
 * @since     1
 */
class ApiController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['create-or-update-entry'];
    public $enableCsrfValidation = false;

    // Public Methods
    // =========================================================================

    /**
     * Handle a request going to our module's index action URL,
     * e.g.: actions/example-module/api
     *
     * @return mixed
     */
    public function actionIndex()
    {
        exit;
    }

    public function actionCreateOrUpdateEntry()
    {

        $model = new Api();
        $isAuthenticated = $model->checkAuthentication();
        $redemptionData = json_decode(Craft::$app->request->getRawBody(), true); 

        $result = $model->updateEntryByField($redemptionData);
        
        return $result;
    }

}