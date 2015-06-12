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

$result = file_get_contents("https://www.googleapis.com/calendar/v3/calendars/".urlencode($calendarId)."/events/".$eventId."?key=".$simple_api_key);

?>
<html>
<head>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <link href="./css/datepicker.css" rel="stylesheet">
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
        <div class="card">

            <div class="forgot-password"><a href='index.php'>Retour</a></div>
            <?php if (isset($_SESSION['access_token'])) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Evènement</h3>
                    </div>
                    <div class="panel-body">
                        <?php
                        //var_dump(json_decode($result));
                        $obj = json_decode($result);
                        ?>
                        <h1><?php echo $obj->{'summary'} ?></h1>
                        <h3>Description :</h3>
                        <p><?php echo $obj->{'description'} ?></p>
                        <h3>Début :</h3>
                        <p><?php echo date_format($obj->{'start'}->{'dateTime'},'d/m/y') ?></p>
                        <h3>Fin :</h3>
                        <p><?php echo date_format($obj->{'start'}->{'dateTime'},'d/m/y') ?></p>
                    </div>
                </div>
            <?php } ?>

        </div><!-- /card-container -->
    </div><!-- /container -->
</div>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
<script src="./js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    $(function () {
        $('#datetimepicker1').datetimepicker({
            locale: 'fr',
            format: 'DD/MM/YYYY'
        });
        $('#datetimepicker2').datetimepicker({
            locale: 'fr',
            format: 'DD/MM/YYYY'
        });
    });
</script>
</body>
</html>