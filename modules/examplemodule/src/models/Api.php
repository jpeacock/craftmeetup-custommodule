<?php
/**
 * Example module for Craft CMS 3.x
 *
 * Example API
 *
 * @link      https://clearmpls.com
 * @copyright Copyright (c) 2021 John Peacock
 */

namespace modules\examplemodule\models;

use modules\examplemodule\ExampleModule;

use Craft;
use craft\db\Query;
use craft\base\Model;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\helpers\UrlHelper;
use craft\records\EntryType;
use craft\services\Relations;
use craft\helpers\DateTimeHelper;


/**
 * Api Model
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    John Peacock
 * @package   ExampleModule
 * @since     1
 */
class Api extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * Some model attribute
     *
     * @var string
     */
    public $apiKeys = ['abc123'];

    // Public Methods
    // =========================================================================

    public function updateEntryByField($postedData){
        if (!$postedData['pageTitle']){
            exit;
        }
        $pageTitle = $postedData['pageTitle'];
        $result = 0;

        $entry = Entry::find()
            ->section('blog')
            ->limit(1)
            ->title($pageTitle)
            ->one();


        if (!$entry){
            // if the entry doesn't exist yet
            $result = $this->saveNewEntry('blog', $postedData, [
                'title' => $pageTitle,
                'enabled' => $postedData['status']
            ]);

        } else {
            // if there's an entry that matches that page title
            $matrixFieldsToSave = $this->_saveMatrixFields($entry, 'components', $postedData);

            $entry->setFieldValue('bodyText', $postedData['bodyText']);
           
            $result = Craft::$app->elements->saveElement($entry, false);
            $this->_writeToLog('Adding new entry...');
            $this->_writeToLog($entry); 

            if (!$result) {
                $this->_writeToLog('Couldn’t save the entry'); 
                $this->_writeToLog($entry); 
                $result = 'Couldn’t save the existing entry: '.$entry->title;
            }
        }
        
        return $result;
    }

    public function saveNewEntry(string $handle, array $postedData, array $fields) {
        $section = Craft::$app->sections->getSectionByHandle($handle); // what section are we working in?
        $entryTypes = $section->getEntryTypes(); // get all entry types for this section
        $entryType = $entryTypes[0]; // I only have 1 entry type for this section

        $entry = new Entry(); // create a new Entry type
        $entry->sectionId = $section->id;
        $entry->typeId = $entryType->id;
        $entry->authorId = 1;
        
        if(isset($fields['title'])) {
          $entry->title = $fields['title'];
          unset($fields['title']);
        }
    
        if(isset($fields['enabled'])) {
          $entry->enabled = $fields['enabled'];
          unset($fields['enabled']);
        }
    
        $entry->setFieldValues($fields);   

        $success = Craft::$app->elements->saveElement($entry);
        
        $this->_writeToLog('Saving new entry...');
        $this->_writeToLog($entry); 

        if (!$success) {
            $this->_writeToLog('Couldn’t save this new entry'); 
            $this->_writeToLog($entry); 
            return 'Couldn’t save new entry: '.json_encode($entry->getErrors(), true);
        } else {
            $matrixFieldsToSave = $this->_saveMatrixFields($entry, 'components', $postedData); 
            if ($matrixFieldsToSave){
                return 1;
            } else {
                $this->_writeToLog('Couldn’t save matrix field data'); 
                $this->_writeToLog($entry); 
                return 0;
            }
            
        }
    }
    
    private function _saveMatrixFields($entry, $handle, $postedData){
      // what are all of the possible block types in this matrix field?
      $blockTypes = Craft::$app->matrix->getAllBlockTypes();
      
      // I dunno, maybe you wanna delete all of the existing matrix blocks?
      // You could also have some logic to parse through the existing ones, update some, etc
      $existingBlocks = $entry->{$handle}->anyStatus();
      if (count($existingBlocks) > 0){
        $existingBlocks = $existingBlocks->all();
      }
      if (! empty($existingBlocks)) {
        $elementsService = Craft::$app->getElements();
        foreach ($existingBlocks as $block) {
          $elementsService->deleteElement($block);
        }
      }
      
      // since we're putting in a bunch of new blocks, this gives us something to return
      $results = [];

      foreach ($postedData['blocks'] as &$block) {
        // get the block ID based on the handle of the block you want to create
        $blockTypeId = array_search($block['blockType'], array_column($blockTypes, 'handle', 'id'));
        
        $newBlock = new MatrixBlock();
        $newBlock->fieldId = 1; // Matrix field's ID
        $newBlock->ownerId = $entry->id; // ID of entry the block should be added to
        $newBlock->siteId = 1;
        $newBlock->typeId = $blockTypeId;
        
        if ($block['blockType'] == 'exampleBlock'){
          // has headline and bodyText fields
          $newBlock->setFieldValues([
            'headline' => $block['headline'],
            'bodyText' => $block['bodyText']
          ]);
        }
        if ($block['blockType'] == 'anotherBlockType'){
          // has headline and showThis fields
          $newBlock->setFieldValues([
            'headline' => $block['headline'],
            'showThis' => $block['showThis']
          ]);
        }

        $results[] = Craft::$app->elements->saveElement($newBlock, false, false, false);
        
      }
      return $results;
    }   

    public function checkAuthentication(){
      $headers = Craft::$app->request->headers;
      $apiKey = $headers->get('X-api-key');

      $keys = $this->apiKeys;
    
      if(!in_array($apiKey, $keys)) {
          throw new \yii\web\ForbiddenHttpException("Unauthorized access. Please access with an authorized API key.");
      }
    }

    private function _writeToLog($message){
      $file = Craft::getAlias('@storage/logs/exampleapi.log');
      $log = date('Y-m-d H:i:s').' '.json_encode($message)."\n";
      \craft\helpers\FileHelper::writeToFile($file, $log, ['append' => true]);
    }
}