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

$dateStart = new DateTime($event->getStart()->dateTime,new DateTimeZone($event->getStart()->timeZone));
$dateEnd = new DateTime($event->getEnd()->dateTime,new DateTimeZone($event->getEnd()->timeZone));

include('partial/head.html.php');
?>
    <div class="card">

        <div class="forgot-password"><a href='index.php'>Retour</a></div>
        <?php if (isset($_SESSION['access_token'])) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Evènement</h3>
                </div>
                <div class="panel-body">
                    <h1><?php echo $event->getSummary(); ?></h1>
                    <h3>Description :</h3>
                    <p><?php echo $event->getDescription(); ?></p>
                    <h3>Début :</h3>
                    <p><?php
                        echo $dateStart->format('d/m/Y H:i');
                        ?></p>
                    <h3>Fin :</h3>
                    <p><?php
                        echo $dateEnd->format('d/m/Y H:i');
                        ?></p>
                </div>
            </div>
        <?php } ?>

    </div><!-- /card-container -->
<?php
include 'partial/footer.html.php';