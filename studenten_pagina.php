<!doctype html>
<html lang=en>
<head>
<link rel="stylesheet" type="text/css" href="styles.css" />
<meta charset=utf-8>
<title>Kommunicate demo</title>
</head>
<body>
<script type="text/javascript">
    (function(d, m){
        var kommunicateSettings = 
            {"appId":"1a5acaa48e0c38fa7ce9d3de538e5ee42","popupWidget":true,"automaticChatOpenOnNavigation":true};
        var s = document.createElement("script"); s.type = "text/javascript"; s.async = true;
        s.src = "https://widget.kommunicate.io/v2/kommunicate.app";
        var h = document.getElementsByTagName("head")[0]; h.appendChild(s);
        window.kommunicate = m; m._globals = kommunicateSettings;
    })(document, window.kommunicate || {});
/* NOTE : Use web server to view HTML files as real-time update will not work if you directly open the HTML file in the browser. */
</script>
<h1>Chatbot demo MOVEL</h1>
<p>Dit is een demo van een chatbot. Hij maakt gebruik van Dialogflow en (via een "webhook") voor een aantal van de vragen van een backend in PHP.<br/>Het geheel wordt via (een demo-account) op kommunicate.io in een webpagina gehangen.</p>
<p>De 5 groepen kunnen gebruik maken van deze Google spreadsheets:</p>
<ul>
  	<li><a href="https://docs.google.com/spreadsheets/d/1hNraslasdkh34aewra43w4324qawg/edit?usp=sharing" target="_blank">Groep 1</a></li>
	<li><a href="https://docs.google.com/spreadsheets/d/1Ph02oZ9PvUasdq232Wzwke5HK24z0/edit?usp=sharing" target="_blank">Groep 2</a></li>
	<li><a href="https://docs.google.com/spreadsheets/d/18jFYnoYPGChDprwwejkbwkeaaalq/edit?usp=sharing" target="_blank">Groep 3</a></li>
	<li><a href="https://docs.google.com/spreadsheets/d/1airSsi4Qupsdfsdf334qa2qafaQ/edit?usp=sharing" target="_blank">Groep 4</a></li>
	<li><a href="https://docs.google.com/spreadsheets/d/1vZfdddedkjj3-fkfjeju3eHLYQ/edit?usp=sharing" target="_blank">Groep 5</a></li>
</ul> 

</body>
</html>