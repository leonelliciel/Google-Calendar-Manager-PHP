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

if(isset($_POST['send'])){
    foreach($_POST as $key => $val){
        ${$key} = $val;
    }

    /* RFC 3339
    [event] => evenement
    [desc] => zerzefzr
    [debut] => 16/06/2015
    [hour1] => 5
    [min1] => 30
    [fin] => 26/06/2015
    [hour2] => 6
    [min2] => 30
    */
    preg_match_all("/([0-9]{2,2})\/([0-9]{2,2})\/([0-9]{4,4})/",$debut,$start);
    preg_match_all("/([0-9]{2,2})\/([0-9]{2,2})\/([0-9]{4,4})/",$fin,$end);

    $eventDebut = $start[3][0].'-'.$start[2][0].'-'.$start[1][0].'T'.sprintf("%02d",$hour1).':'.sprintf("%02d",$min1).':00+01:00';
    $eventEnd = $end[3][0].'-'.$end[2][0].'-'.$end[1][0].'T'.sprintf("%02d",$hour2).':'.sprintf("%02d",$min2).':00+01:00';

    $event = new Google_Service_Calendar_Event(
        array(
            'summary' => $summary,
            'description' => $desc,
            'start' => array(
                'dateTime' => $eventDebut,
                'timeZone' => date_default_timezone_get(),
            ),
            'end' => array(
                'dateTime' => $eventEnd,
                'timeZone' => date_default_timezone_get(),
            ),
        )
    );

    $event = $service->events->insert($calendarId, $event);

    if($event->htmlLink != ''){
        $success = 1;
    }else{
        $error = 1;
    }
}

include('partial/head.html.php');
?>
    <div class="card">
        <?php
        if($success == 1){
            ?>
            <div class="alert alert-success" role="alert">Votre événement à bien été ajouté, voici le lien de ce dernier : <a href="<?php echo $event->htmlLink; ?>" target="_blank">New Event</a> </div>
        <?php
        }
        if($error == 1){
            ?>
            <div class="alert alert-danger" role="alert">Erreur d'enregistrement veuillez resaisir le formulaire correctement !</div>
        <?php
        }
        ?>

        <div id="back"><a href="calendarList.php" class="btn-sm btn-info" role="button"><span class="glyphicon glyphicon-chevron-left"></span> Retour</a></div>
        <img id="profile-img" class="profile-img-card" src="<?php echo $userData["picture"]; ?>" width="100px" size="100px" /><br/>
        <?php if (isset($_SESSION['access_token'])) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Ajout d'un événement</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" method="post">
                        <fieldset>
                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="event">Nom de l'événement</label>
                                <div class="col-md-8">
                                    <input id="event" name="summary" type="text" placeholder="Evénement" class="form-control input-md" required="">
                                    <span class="help-block">Saisir le titre de l'événement</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="desc">Description de l'événement</label>
                                <div class="col-md-8">
                                    <input id="desc" name="desc" type="text" placeholder="Description" class="form-control input-md"  >
                                    <span class="help-block">Saisir la description de l'événement</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="start">Début</label>
                                <div class="col-md-2">
                                    <div class="input-group date" id="datetimepicker1">
                                        <input type="text" class="form-control" placeholder="JJ/MM/AAAA" name="debut" value="<?php echo date('d/m/Y'); ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                    <span class="help-block">Début de l'événement</span>
                                </div>
                                <div class="col-md-1">
                                    <select name="hour1"  class="form-control time" required="">
                                        <?php for($i = 0;$i<24;$i++){ ?>
                                            <option value="<?php echo $i; ?>"><?php echo sprintf("%02d", $i); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-1 control-label">
                                    h.
                                </div>
                                <div class="col-md-1">
                                    <select name="min1"  class="form-control time" required="">
                                        <option value="0">00</option>
                                        <option value="30">30</option>
                                    </select>
                                </div>
                                <div class="col-md-1 control-label">
                                    min.
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="desc">Fin</label>
                                <div class="col-md-2">
                                    <div class="input-group date" id="datetimepicker2">
                                        <input type='text' class="form-control" placeholder="JJ/MM/AAAA" name="fin" value="<?php echo date('d/m/Y'); ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                    <span class="help-block">Début de l'événement</span>
                                </div>
                                <div class="col-md-1">
                                    <select name="hour2"  class="form-control time">
                                        <?php for($i = 0;$i<24;$i++){ ?>
                                            <option value="<?php echo $i; ?>"><?php echo sprintf("%02d", $i); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-1 control-label">
                                    h.
                                </div>
                                <div class="col-md-1">
                                    <select name="min2"  class="form-control time">
                                        <option value="0">00</option>
                                        <option value="30">30</option>
                                    </select>
                                </div>
                                <div class="col-md-1 control-label">
                                    min.
                                </div>
                            </div>

                            <!-- Button -->
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="send">Envoyer</label>
                                <div class="col-md-4">
                                    <button id="send" name="send" class="btn btn-primary">Envoyer</button>
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>
            </DIV>
        <?php } ?>
    </div><!-- /card-container -->
<?php
$javascript = '<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="./js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $(\'#datetimepicker1\').datetimepicker({
            locale: \'fr\',
            format: \'DD/MM/YYYY\'
        });
        $(\'#datetimepicker2\').datetimepicker({
            locale: \'fr\',
            format: \'DD/MM/YYYY\'
        });
    });
</script>';
include ('partial/footer.html.php');