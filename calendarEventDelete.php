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
if ($client->isAccessTokenExpired()){
    header('Location: index.php');
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
        <div id="back"><a href="calendarList.php" class="btn-sm btn-info" role="button"><span class="glyphicon glyphicon-chevron-left"></span> Retour</a></div>
        <?php if (isset($_SESSION['access_token'])) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Evènement</h3>
                </div>
                <div class="panel-body">
                    <script>window.setTimeout("location=('calendarList.php');",500);</script>
                    <div class="alert alert-success" role="alert">Votre événement à bien été effacé, vous allez être redirigé ou cliquez sur le lien suivant pour revenir au listing des événements : <a href="calendarList.php">Listing des Calendriers</a> </div>
                </div>
            </div>
        <?php } ?>

    </div><!-- /card-container -->
<?php
include 'partial/footer.html.php';