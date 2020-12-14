<?php 
require_once("vendor/autoload.php"); 

/* 
    Examples and download: https://php-download.com/package/eristemena/dialogflow-fulfillment-webhook-php/example
    Source: https://github.com/eristemena/dialogflow-fulfillment-webhook-php

*/

use Dialogflow\WebhookClient;
use Dialogflow\RichMessage\Card;
use Dialogflow\RichMessage\Suggestion;
use Dialogflow\RichMessage\Text;
use Dialogflow\RichMessage\Payload;

use Dialogflow\Action\Responses\LinkOutSuggestion;
use Dialogflow\Action\Responses\Suggestions;
use Dialogflow\Action\Responses\Image;
use Dialogflow\Action\Responses\BasicCard;

$json_url = "http://gsx2json.com/api?id=1wxxksdfhksdfh324876sfsd";
$json_ixperium_medewerkers = file_get_contents($json_url);
$data_ixperium_medewerkers = json_decode($json_ixperium_medewerkers);

$agent = new WebhookClient(json_decode(file_get_contents('php://input'),true));

$intent = $agent->getIntent();
$action = $agent->getAction();
$query = $agent->getQuery();
$parameters = $agent->getParameters();
$session = $agent->getSession();
$contexts = $agent->getContexts();
$language = $agent->getLocale();
$originalRequest = $agent->getOriginalRequest();
$agentVersion = $agent->getAgentVersion();

$conv = $agent->getActionConversation();

$hasscreen = true;
if ($conv) {
    $hasconv = true;
    $capabilities = array_column($originalRequest["payload"]["surface"]["capabilities"], "name");
    if (false !== array_search('actions.capability.SCREEN_OUTPUT', $capabilities)) {
        $hasscreen = true;
    } else {
        $hasscreen = false;        
    }
} else {
        $hasconv = false;    
}

if (array_key_exists("botId", $originalRequest["payload"])) {
    $kommunicatebot = true;
} else {
    $kommunicatebot = false;    
}   

if (strpos($session, 'dfMessenger') !== false) {
    $dfMessenger = true;
} else {
    $dfMessenger = false;
}

if ('chatbot.showsource' == $agent->getIntent() ) {
    
    $debug = array(
        "intent" => $intent,
        "action" => $action,
        "query" => $query,
        "parameters" => $parameters,
        "session" => $session,
        "contexts" => $contexts,
        "language" => $language,
        "originalRequest" => $originalRequest,
        "agentVersion" => $agentVersion,
        "hasconv" => $hasconv,
        "kommunicatebot" => $kommunicatebot,
        "hasscreen" => $hasscreen,
        "dfMessenger" => $dfMessenger);
        
    $debug_json = json_encode($debug);
    $agent->reply('Ik heb de volgende info voor je: ' . $debug_json);
    
} 

elseif ('chatbotmovel.wieis' == $agent->getIntent() ) {
    $found = false;
    foreach ($data_ixperium_medewerkers->rows as $medewerker) {
        if ($medewerker->naam == $parameters["chatbotmovel_personen"]) {
            $found = true;
            if ($conv) {
                # Google Actions on phone or other device
                if ($hasscreen) {
                    $conv->ask('Dit is de info over '.$medewerker->naam.':');
                    $card = BasicCard::create()
                        ->title($medewerker->naam)
                        ->formattedText($medewerker->functie)
                        ->image($medewerker->afbeelding)
                        ->button('Meer info...', $medewerker->meerinfo);
                        $conv->ask($card);
                } else {
                    # no screen (Google Home Mini)
                    $conv->ask($medewerker->tekstinfo);
                }
                $agent->reply($conv);    
            } elseif ($kommunicatebot) {
                $card = \Dialogflow\RichMessage\Card::create()
                    ->title($medewerker->naam)
                    ->text($medewerker->functie)
                    ->image($medewerker->afbeelding)
                    ->button('Meer info...', $medewerker->meerinfo);
                $agent->reply($card); 
            } elseif ($dfMessenger) {
                # dialogflow messenger
                $agent->reply($medewerker->tekstinfo);
                $text = array(array("text" => "Wanneer bereiken we singulariteit?"), array("text" => "Wie heeft de term singulariteit verzonnen?"));
                $richContent = array("type" => "chips", "options" => $text);
                $payload = array("richContent" => array([$richContent]));
                $agent->reply(\Dialogflow\RichMessage\Payload::create($payload));
            
            } else {
                # all other agents
                $agent->reply($medewerker->tekstinfo);
                $text = \Dialogflow\RichMessage\Text::create()
                    ->text('This is text')
                    ->ssml('<speak>This is <say-as interpret-as="characters">ssml</say-as></speak>');
                $text = array(array("text" => "Wie heeft je gebouwd?"), array("text" => "Wat is AI?"));
                $richContent = array("type" => "chips", "options" => $text);
                $payload = array("richContent" => array([$richContent]));
                $agent->reply(\Dialogflow\RichMessage\Payload::create($payload));
				$text2 = array("Wie heeft je gebouwd?", "Wat is AI?");
				$suggestion = \Dialogflow\RichMessage\Suggestion::create($text2);
				$agent->reply($suggestion);
				$card = \Dialogflow\RichMessage\Card::create()
                    ->title($medewerker->naam)
                    ->text($medewerker->functie)
                    ->image($medewerker->afbeelding)
                    ->button('Meer info...', $medewerker->meerinfo);
                $agent->reply($card); 
            }
                
        }
    }
    if (!$found) {
        $agent->reply('Ik heb nog geen uitgebreide informatie over ' . $parameters["geslacht"].' '.$parameters["chatbotmovel_personen"].' beschikbaar. Maar ik leer elke dag nog bij.');
    }
}
    
else {
    // nog niet geimplementeerde intent
    $agent->reply('Ik weet niet goed hoe ik hier een antwoord op moet geven. Intent = '.$agent->getIntent() );  
}
    header('Content-type: application/json');
    echo json_encode($agent->render());

?>