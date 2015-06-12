<?php
session_start();
require_once 'google-api-php-client-master/src/Google/autoload.php';

include('parameters.php');

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


?>
<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
</head>
<body>
<nav class="navbar navbar-fixed-top">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navigation">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand text-uppercase" >Google Login</span>
        </div>
    </div>
</nav>

<div class="container" style="padding-top: 100px;">

    <div class="container">
        <div class="card card-container">
            <div class="forgot-password"><a href='index.php'>Retour</a></div>
            <img id="profile-img" class="profile-img-card" src="<?php echo $userData["picture"]; ?>" width="100px" size="100px" /><br/>
            <?php
            if (isset($_SESSION['access_token'])) {

            ?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Liste des événements</h3>
    </div>
    <ul class="list-group">
        <?php

        $calendarList  = $service->calendarList->listCalendarList();
        while(true) {
            $i = 1;
            foreach ($calendarList->getItems() as $calendarListEntry) {

                echo '<li class="list-group-item">
                        <div class="row toggle" id="dropdown-detail-'.$i.'" data-toggle="detail-'.$i.'">
                            <div class="col-xs-10">'.$calendarListEntry->getSummary().'</div>
                            <div class="col-xs-2"><i class="fa fa-chevron-down pull-right"></i></div>
                        </div>
                        <div id="detail-'.$i.'">
                            <hr></hr>
                            <div class="container">
                                <div class="fluid-row">
                        ';

                // get events
                $events = $service->events->listEvents($calendarListEntry->id);


                foreach ($events->getItems() as $event) {
                    //print_r($event);
                    echo '<div> <a href="calendarEventDisplay.php?eventId='.$event->getId().'" >'.$event->getSummary().'</a></div>';
                }
                echo '</div>
                            </div>
                        </div>
                    </li>';
                $i++;
            }
            $pageToken = $calendarList->getNextPageToken();
            if ($pageToken) {
                $optParams = array('pageToken' => $pageToken);
                $calendarList = $service->calendarList->listCalendarList($optParams);
            } else {
                break;
            }
        }
        }
        ?>
    </ul>
</div>
</div><!-- /card-container -->
</div><!-- /container -->
</div>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        $('[id^=detail-]').hide();
        $('.toggle').click(function() {
            $input = $( this );
            $target = $('#'+$input.attr('data-toggle'));
            $target.slideToggle();
        });
    });
</script>
</body>
</html>