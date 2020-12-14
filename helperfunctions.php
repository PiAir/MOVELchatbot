<?php 

use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\Cloud\Dialogflow\V2\Intent\Message\SimpleResponse;
use Google\Cloud\Dialogflow\V2\Intent\Message\SimpleResponses;
use Google\Cloud\Dialogflow\V2\Intent\Message\Suggestions;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent;
$ACTIONS_ON_GOOGLE = 8;

function intent_list($projectId)
{
    // get intents
    $intentsClient = new IntentsClient();
    $parent = $intentsClient->agentName($projectId);
    $intents = $intentsClient->listIntents($parent);

    foreach ($intents->iterateAllElements() as $intent) {
        // print relevant info
        print(str_repeat("=", 20) . PHP_EOL);
        printf('Intent name: %s' . PHP_EOL, $intent->getName());
        printf('Intent display name: %s' . PHP_EOL, $intent->getDisplayName());
        printf('Action: %s' . PHP_EOL, $intent->getAction());
        printf('Root followup intent: %s' . PHP_EOL,
            $intent->getRootFollowupIntentName());
        printf('Parent followup intent: %s' . PHP_EOL,
            $intent->getParentFollowupIntentName());
        print(PHP_EOL);

        print('Input contexts: ' . PHP_EOL);
        foreach ($intent->getInputContextNames() as $inputContextName) {
            printf("\t Name: %s" . PHP_EOL, $inputContextName);
        }

        print('Output contexts: ' . PHP_EOL);
        foreach ($intent->getOutputContexts() as $outputContext) {
            printf("\t Name: %s" . PHP_EOL, $outputContext->getName());
        }
    }
    $intentsClient->close();
}

function intent_delete_by_name($projectId, $intentName)
{
    // get intents
    $intentsClient = new IntentsClient();
    $parent = $intentsClient->agentName($projectId);
    $intents = $intentsClient->listIntents($parent);

    foreach ($intents->iterateAllElements() as $intent) {
        if ($intentName == $intent->getDisplayName()) {
        
            $intentName = $intent->getName();

            $intentId = end(preg_split('#/#',$intentName));
            # printf('Intent id to delete: %s' . PHP_EOL,$intentId);
            # print(PHP_EOL);
            intent_delete($projectId, $intentId);
        }
    }
    $intentsClient->close();
}

function intent_delete($projectId, $intentId)
{
    $intentsClient = new IntentsClient();
    $intentName = $intentsClient->intentName($projectId, $intentId);

    $intentsClient->deleteIntent($intentName);
    # printf('Intent deleted: %s' . PHP_EOL, $intentId);
    # print(PHP_EOL);


    $intentsClient->close();
}

function intent_create($projectId, $displayName, $trainingPhraseParts = [],
    $messageTexts = [], $webhookState = 1)   
{

    $messages = array();
    $intentsClient = new IntentsClient();

    // prepare parent
    $parent = $intentsClient->agentName($projectId);
	
    // prepare training phrases for intent
    $trainingPhrases = [];
    foreach ($trainingPhraseParts as $trainingPhrasePart) {
        $part = (new Part())
            ->setText($trainingPhrasePart);

        // create new training phrase for each provided part
        $trainingPhrase = (new TrainingPhrase())
            ->setParts([$part]);
        $trainingPhrases[] = $trainingPhrase;
    }
	
    foreach ($messageTexts as $messageText) {
		$simpleResponse = (new SimpleResponse())
							->setSsml("<speak>".$messageText."</speak>")
							->setDisplayText(strip_tags($messageText));
        $Responses[] = $simpleResponse;
		$plainTexts[] = strip_tags($messageText);
    }	
	$simpleResponses = (new SimpleResponses())
		->setSimpleResponses($Responses);
	
	// prepare messages for intent
    $text = (new Text())
        ->setText($plainTexts);
    $message = (new Message())
        ->setText($text);	
    $messages[] = $message;
    
    $googleMessage = (new Message())
		->setPlatform(8)
		->setSimpleResponses($simpleResponses);	
    $messages[] = $googleMessage;
        
       // prepare intent
    $intent = (new Intent())
        ->setDisplayName($displayName)
        ->setTrainingPhrases($trainingPhrases)
        ->setMessages($messages)
        ->setWebhookState($webhookState);

    // create intent
    $response = $intentsClient->createIntent($parent, $intent);
    # printf('Intent created: %s' . PHP_EOL, $response->getName());

    $intentsClient->close();
}

function intent_create2($projectId, $displayName, $trainingPhraseParts = [],
    $messageTexts = [], $webhookState = 1, $payload1)
{

    $messages = array();
    $intentsClient = new IntentsClient();

    // prepare parent
    $parent = $intentsClient->agentName($projectId);
	


    // prepare training phrases for intent
    $trainingPhrases = [];
    foreach ($trainingPhraseParts as $trainingPhrasePart) {
        $part = (new Part())
            ->setText($trainingPhrasePart);

        // create new training phrase for each provided part
        $trainingPhrase = (new TrainingPhrase())
            ->setParts([$part]);
        $trainingPhrases[] = $trainingPhrase;
    }
	// prepare messages for intent
    foreach ($messageTexts as $messageText) {
		$simpleResponse = (new SimpleResponse())
							->setSsml("<speak>".$messageText."</speak>")
							->setDisplayText(strip_tags($messageText));
        $Responses[] = $simpleResponse;
		$plainTexts[] = strip_tags($messageText);
    }	
	$simpleResponses = (new SimpleResponses())
		->setSimpleResponses($Responses);

    $text = (new Text())
        ->setText($plainTexts);
    $message = (new Message())
        ->setText($text);	
    $messages[] = $message;
    
    $googleMessage = (new Message())
		->setPlatform(8)
		->setSimpleResponses($simpleResponses);	
    $messages[] = $googleMessage;
        
    $messages[] = $payload1;    

  
    // prepare intent
    $intent = (new Intent())
        ->setDisplayName($displayName)
        ->setTrainingPhrases($trainingPhrases)
        ->setMessages($messages)
        ->setWebhookState($webhookState);

    // create intent
    $response = $intentsClient->createIntent($parent, $intent);
    printf('Intent created: %s' . PHP_EOL, $response->getName());

    $intentsClient->close();
}

function intent_create3($projectId, $displayName, $trainingPhraseParts = [],
    $messageTexts = [], $webhookState = 1, $suggestions)
{

    $messages = array();
    $intentsClient = new IntentsClient();

    // prepare parent
    $parent = $intentsClient->agentName($projectId);
	


    // prepare training phrases for intent
    $trainingPhrases = [];
    foreach ($trainingPhraseParts as $trainingPhrasePart) {
        $part = (new Part())
            ->setText($trainingPhrasePart);

        // create new training phrase for each provided part
        $trainingPhrase = (new TrainingPhrase())
            ->setParts([$part]);
        $trainingPhrases[] = $trainingPhrase;
    }
	// prepare messages for intent
    foreach ($messageTexts as $messageText) {
		$simpleResponse = (new SimpleResponse())
							->setSsml("<speak>".$messageText."</speak>")
							->setDisplayText(strip_tags($messageText));
        $Responses[] = $simpleResponse;
		$plainTexts[] = strip_tags($messageText);
    }	
	$simpleResponses = (new SimpleResponses())
		->setSimpleResponses($Responses);
        
    $text = (new Text())
        ->setText($plainTexts);
    $message = (new Message())
        ->setText($text);	
    $messages[] = $message;
     
     
    $googleMessage = (new Message())
		->setPlatform(8)
		->setSimpleResponses($simpleResponses);	
    $messages[] = $googleMessage;

    $SuggestionsObj = (new Suggestions())
        ->setSuggestions($suggestions);
    $SuggestionsMessage = (new Message())
        ->setPlatform(8)
        ->setSuggestions($SuggestionsObj);
    $messages[] = $SuggestionsMessage;
    
  
    // prepare intent
    $intent = (new Intent())
        ->setDisplayName($displayName)
        ->setTrainingPhrases($trainingPhrases)
        ->setMessages($messages)
        ->setWebhookState($webhookState);

    // create intent
    $response = $intentsClient->createIntent($parent, $intent);
    printf('Intent created: %s' . PHP_EOL, $response->getName());

    $intentsClient->close();
}


?>