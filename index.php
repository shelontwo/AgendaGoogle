<meta charset="utf-8">
<meta http-equiv="refresh" content="30;">
<?php

require_once __DIR__ . '/vendor/autoload.php';
define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', '~/dados/.credentials/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR_READONLY)
));



/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');

  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode('4/JVSlPi9-_dWjORZDas3T4FBPTd2WuEVnXGtfehxFlKU');
    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);

  // Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    @file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}

/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}

// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);

// Print the next 7 events on the user's calendar.
$calendarId = 'EMAIL DA AGENDA QUE DESEJA INSERIR';
$optParams = array(
  'maxResults' => 7,
  'orderBy' => 'startTime',
  'singleEvents' => TRUE,
  'timeZone' => 'UTC-2:00',
  'timeMin' => date('c')
);
$results = $service->events->listEvents($calendarId, $optParams);



/*$resource =  {
    "kind": "admin#directory#resources#calendars#CalendarResource",
    "etags": "\"TN30oD80QTVK45AAxvl_wbzs4vs/26AIxYaVpw0L3T6E9eNtE1v9JD0\"",
    "resourceId": "68039437240",
    "resourceName": "Sala de Reuniões Principal",
    "generatedResourceName": "Ó DOIS GO - Chapecó-1-Sala de Reuniões Principal (10)",
    "resourceEmail": "odoisgo.com_3638303339343337323430@resource.calendar.google.com",
    "capacity": 10,
    "buildingId": "2",
    "floorName": "1",
    "resourceCategory": "CONFERENCE_ROOM",
    "userVisibleDescription": "Agenda Sala de Reuniões Principal"
   };*/
?>

<html>
    <head>
        <title></title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css"/>
        <meta name="viewport" content="width=device-width, user-scalable=no" charset="utf-8">
        <link rel="stylesheet" href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        
        
        
    </head>
    <body class="fundo" style="background-color: #1c334f">
        <div class="container-fluid">
           
            
            
            
            
            <?php
                if (count($results->getItems()) == 0) {
                   $errorEventos = 'Sem eventos para hoje!';
                } else {
                  $countEvents = 1;
                  foreach ($results->getItems() as $event) {
                    $dateStart = new DateTime($event['start']['dateTime']);
                    $dateFinish = new DateTime($event['end']['dateTime']);
                      if($countEvents == 1){           
                            // ver se está rolando ou vago
                            $dataAtual = new DateTime(); 
                            if(($dataAtual > $dateStart) && ($dataAtual < $dateFinish)){
                                $classe = 'ocupado';
                                $classe2 = 'box-ocupado';
                            }else{
                                $classe = 'livre';
                                $classe2= 'box-livre';
                            }
                          ?>
                            <div class="emandamento box <?php echo $classe; ?>">
                                <div class="row align-items-center" style="margin: 15px 0px;">
                                    <div class="col-xs-12 col-sm-9 col-lg-8 box">
                                        <div class="row px-4" style="height:150px;">
                                            <div class="col-xs-12 col-sm-7 col-lg-7 ">
                                                <h5><strong><?php echo ($event['summary']); ?></strong></h5>
                                                <p class="descricao-principal">
                                                    <?php echo ($event['description']); ?>
                                                    <br>
                                                    <strong><?php echo $event['creator']['displayName']; ?></strong>
                                                    <br>
                                                    <?php echo $event['creator']['email']; ?>
                                                    
                                                </p>
                                            </div>
                                            <div class=" text-center col-xs-12 col-sm-5 col-lg-5">
                                                <div class="box-interno">
                                                    <strong>Data:</strong><br>
                                                     <h5><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo date_format($dateStart, 'd/m'); ?></h5>
                                                    <strong>Horário de Início:</strong><br>
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date_format($dateStart, 'H:i'); ?><br>
                                                    <strong>Horário de Fim:</strong><br>
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo date_format($dateFinish, 'H:i'); ?><br>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3 col-lg-4 py-lg-2 py-md-5 py-sm-5 py-5 py-xs-2">
                                        <div class="box-status">
                                            <h5><strong><?php echo ucfirst($classe)?></strong></h5>
                                            <div class="<?php echo $classe2; ?>"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          <?php
                            }else{
                                if($countEvents == 2){
                                    ?>
                                    <div class="proximas">
                                        <br>
                                    <h7 style="color: #fff"><strong>PRÓXIMAS REUNIÕES</strong></h7>
                                        <div class="row">
                                        
                                <?php
                                    }
                                ?>      
                                        <div style="padding:5px;" class="col-xs-12 col-sm-2 col-md-4 col-lg-3 col-xl-2">
                                        <div class="box box-menor">
                                        <strong><?php echo ($event['summary']); ?></strong><br>
                                            <p class="descricao">
                                                <i><?php echo ($event['description']); ?></i>
                                                <br>
                                                <strong><?php echo $event['creator']['displayName']; ?></strong>
                                                <br>
                                                <?php echo $event['creator']['email']; ?>
                                                <br><br>
                                                <strong><?php echo date_format($dateStart, 'd/m'); ?></strong><br>
                                             <strong>De<span class="data"><?php echo date_format($dateStart, 'H:i'); ?></span> Até<span class="data"><?php echo date_format($dateFinish, 'H:i'); ?></span></strong>
                                            </p>
                                        </div>
                                    </div>

                            <?php
                          }     
                          $countEvents++;
                      }
                    }
                    ?>
            
                    </div>
                </div>
            <br>
            <div class="text-center py-4">
               <img src="https://odoisgo.com/midia/logos.gif" width="80px">
            </div>
            <br>
        </div>
    </body>
</html>