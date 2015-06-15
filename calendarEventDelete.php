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

$success = $error = 0;

if(isset($_GET['eventId'])){
    $eventID = $_GET['eventId'];
    $event = $service->events->delete($calendarId, $eventID);

    if($event == 1){
        $success = 1;
    }else{
        $error = 1;
    }
}

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
                    <div class="alert alert-success" role="alert">Votre événement à bien été effacé cliquez ici pour retourner à l'accueil <a href="calendarList.php">Listing des Calendriers</a> </div>
                </div>
            </div>
        <?php } ?>

    </div><!-- /card-container -->
<?php
include 'partial/footer.html.php';