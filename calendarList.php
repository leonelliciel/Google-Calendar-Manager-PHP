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
    header('Location: index.php');
}


$service = new Google_Service_Calendar($client);
$objOAuthService = new Google_Service_Oauth2($client);
$userData = $objOAuthService->userinfo->get();

include('partial/head.html.php');
?>
<div class="card">
    <div id="back"><a href="index.php" class="btn-sm btn-info" role="button"><span class="glyphicon glyphicon-chevron-left"></span> Retour</a></div>
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
                        <div class="row toggle" id="dropdown-detail-'.$i.'" data-toggle="detail-'.$i.'" >
                            <div class="col-xs-10 calendar">'.$calendarListEntry->getSummary().'</div>
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

                        if($calendarListEntry->getId() == $userData->getEmail()){
                            echo '
                                <div>
                                    <div class="btn-group">
                                        <button class="btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="calendarEventUpdate.php?eventId='.$event->getId().'" ><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> Editer</a></li>
                                            <li><a href="calendarEventDelete.php?eventId='.$event->getId().'&calendarId='.$calendarListEntry->getId().'"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Supprimer</a></li>
                                        </ul>
                                    </div>
                                    - <a href="calendarEventDisplay.php?eventId='.$event->getId().'" >'.$event->getSummary().'</a>
                                </div>';
                        }else{
                            echo '<div><a href="calendarEventDisplay.php?eventId='.$event->getId().'" >'.$event->getSummary().'</a></div>';
                        }
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
<?php
$javascript = '<script>
    $(document).ready(function() {
        $(\'[id^=detail-]\').hide();
        $(\'.toggle\').click(function() {
            $input = $( this );
            $target = $(\'#\'+$input.attr(\'data-toggle\'));
            $target.slideToggle();
        });
    });
</script>';

include 'partial/footer.html.php';
?>
