<?php
session_start();
require_once 'google-api-php-client-master/src/Google/autoload.php';

include('parameters.php');

$success = $error = 0;

$client = new Google_Client();
$client->setApplicationName("Liciel_Google_Agenda");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');
$client->setScopes(array('https://www.googleapis.com/auth/calendar'));

$client->setAccessToken($_SESSION['access_token']);
if ($client->isAccessTokenExpired()) {
    Die('Refresh Token');
}

$service = new Google_Service_Calendar($client);
$objOAuthService = new Google_Service_Oauth2($client);
$userData = $objOAuthService->userinfo->get();
$calendarId = $userData['email'];
$eventId = $_GET['eventId'];

$event = new Google_Service_Calendar_Event();
$event = $service->events->get($calendarId, $eventId);

if($event->getStart()->timeZone !=  NULL && $event->getStart()->dateTime != NULL) {
    $dateStart = new DateTime($event->getStart()->dateTime, new DateTimeZone($event->getStart()->timeZone));
    $dateEnd = new DateTime($event->getEnd()->dateTime, new DateTimeZone($event->getEnd()->timeZone));
}else{
    if($event->getStart()->date != NULL){
        $dateStart = new DateTime($event->getStart()->date);
        $dateEnd = new DateTime($event->getEnd()->date);
    }else{
        $dateStart = new DateTime($event->getStart()->dateTime);
        $dateEnd = new DateTime($event->getEnd()->dateTime);
    }
}

include('partial/head.html.php');
?>
    <div class="card">
        <div id="back"><a href="calendarList.php" class="btn-sm btn-info" role="button"><span class="glyphicon glyphicon-chevron-left"></span> Retour</a> <a href="calendarEventUpdate.php?eventId=<?php echo $eventId; ?>" class="btn-sm btn-info pull-right" role="button"><span class="glyphicon glyphicon-chevron-info"></span> Editer</a></div>
        <?php if (isset($_SESSION['access_token'])) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title"><?php echo $event->getSummary(); ?></h1>
                </div>
                <div class="panel-body">
                    <h3><span class="glyphicon glyphicon glyphicon glyphicon-question-sign" aria-hidden="true"></span> Description :</h3>
                    <p><?php echo $event->getDescription(); ?></p>
                    <h3><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Début :</h3>
                    <p><?php
                        echo $dateStart->format('d/m/Y H:i');
                        ?></p>
                    <h3><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Fin :</h3>
                    <p><?php
                        echo $dateEnd->format('d/m/Y H:i');
                        ?></p>
                </div>
            </div>
        <?php } ?>

    </div><!-- /card-container -->
<?php
include 'partial/footer.html.php';