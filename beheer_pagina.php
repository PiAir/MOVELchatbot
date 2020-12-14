<!doctype html>
<html lang=en>
<head>
<link rel="stylesheet" type="text/css" href="styles.css" />
<meta charset=utf-8>
<title>MOVEL dialogflow beheeromgeving</title>
</head>
<body>
<img src="iXperium_logo.png" alt="iXperium Logo">
<?php 
require_once("vendor/autoload.php"); 
require_once("helperfunctions.php");

/* 

    https://github.com/GoogleCloudPlatform/php-docs-samples/tree/master/dialogflow
    https://github.com/googleapis/google-cloud-php/tree/master/Dialogflow
    https://cloud.google.com/dialogflow/es/docs/how/manage-intents
    https://clubmate.fi/protect-a-directory-or-a-domain-with-a-password-on-nginx-server
    
    setPayload($var)
    https://github.com/googleapis/google-cloud-php/blob/master/Dialogflow/src/V2/Intent/Message.php
*/

use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase\Part;
use Google\Cloud\Dialogflow\V2\Intent\TrainingPhrase;
use Google\Cloud\Dialogflow\V2\Intent\Message\Text;
use Google\Cloud\Dialogflow\V2\Intent\Message;
use Google\Cloud\Dialogflow\V2\Intent;

putenv('GOOGLE_APPLICATION_CREDENTIALS=/var/www/html/webhook/cloud/chatbotmovel-aawz-klkjhsuek.json');
$projectId = 'chatbotmovel-aawz';

$num_groups = 5;
$json_ids = array();
$json_ids[100] = "1hNraslaseee4uieiuewgwaj2awg";
$json_ids[1] = "1hNraslasdkh34aewra43w4324qawg";
$json_ids[2] = "1Ph02oZ9PvUasdq232Wzwke5HK24z0";
$json_ids[3] = "18jFYnoYPGChDprwwejkbwkeaaalq";
$json_ids[4] = "1airSsi4Qupsdfsdf334qa2qafaQ";
$json_ids[5] = "1vZfdddedkjj3-fkfjeju3eHLYQ";

$gsx2json = "http://gsx2json.com/api?id=";
    echo "<p><a href='/beheer/index.php?id=100'>Importeer hoofdgroep</a></p>";
for ($x = 1; $x <=5; $x++){
    echo "<p><a href='/beheer/index.php?id=".$x."'>Importeer groep ".$x."</a></p>";
}

if(isset($_GET['id'])  && !empty( $_GET['id'])  && isset($json_ids[$_GET['id']])  ) { 

    $json_url = $gsx2json.$json_ids[$_GET['id']]; 
    $json_intents = file_get_contents($json_url);
    $data_intents = json_decode($json_intents);


    $prefix = "chatbot_";
    $current_name = "";
    $slug = "";
    $trainingPhrasesParts = array();
    $messageTexts = array();
    $webhookState = 0;


    echo "<pre>";
    foreach ($data_intents->rows as $intent) {

        $slug = str_replace(" ", "", $prefix.$intent->naam);
        printf('Processing: %s' . PHP_EOL, $slug);
    
        if($slug != $current_name) {
            # save current intent
            if ($current_name != "") { 
                intent_delete_by_name($projectId, $current_name);      
                intent_create($projectId, $current_name, $trainingPhrasesParts, $messageTexts, $webhookState);
                print('Created intent: '.$current_name);
                print(PHP_EOL);
            }
            $webhookState == $intent->webhook;
            $trainingPhrasesParts = array();
            $messageTexts = array();
            $current_name = $slug;    
        } 
        # continue with current intent
        if (isset($intent->vraag) and ($intent->vraag != "")) {
            array_push($trainingPhrasesParts, $intent->vraag);
        }
        if (isset($intent->antwoord) and ($intent->antwoord != "")) { 
            array_push($messageTexts, $intent->antwoord);
        }        

    }
    # save last intent
    if ($slug != "") { 
        intent_delete_by_name($projectId, $slug);      
        intent_create($projectId, $slug, $trainingPhrasesParts, $messageTexts, $webhookState);
        print('Created intent: '.$slug);
        print(PHP_EOL);
    }


    # intent_list($projectId);
    print('Done!');
    echo "</pre>";
} else {
    print('<b>Either no or an incorred id provided.</b>');
}

?>

</body>
</html>