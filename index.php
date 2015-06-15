<?php
session_start();

require_once 'google-api-php-client-master/src/Google/autoload.php';

include('parameters.php');

// On accéde à l'API Google
$client = new Google_Client();
$client->setApplicationName("PHP Google OAuth Login Example");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setDeveloperKey($simple_api_key);
$client->setAccessType('offline');
$client->setScopes(array('https://www.googleapis.com/auth/calendar',"https://www.googleapis.com/auth/userinfo.email"));

// Service d'authentification
$objOAuthService = new Google_Service_Oauth2($client);

//Logout
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
    $client->revokeToken();
    header('Location: '.filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
}

if ($client->getAccessToken()) {
    $userData = $objOAuthService->userinfo->get();
    $_SESSION['access_token'] = $client->getAccessToken();
} else {
    $authUrl = $client->createAuthUrl();
}

if (isset($_GET['code'])){
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    header('Location: '.filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

// Chargement du service Calendar
$service = new Google_Service_Calendar($client);

include('partial/head.html.php');
?>
<div class="card card-container">
    <!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->

    <!-- Show Login if the OAuth Request URL is set -->
    <?php if (isset($authUrl)){ ?>
        <img src="https://ssl.gstatic.com/accounts/ui/logo_2x.png" width="250" /><br/>
        <button style="opacity: 0; z-index: 10000; left: 0px; top: 0px; position: absolute; cursor: pointer; outline: 0px; width: 135px; height: 36px;">Se connecter avec Google</button>
        <a class='login' href='<?php echo $authUrl; ?>'><img class='login' src="https://developers.google.com/+/images/branding/sign-in-buttons/Red-signin_Long_base_44dp.png" width="250" /></a>
        <!-- Show User Profile otherwise-->

    <?php }else{
        ;                ?>

        <img id="profile-img" class="profile-img-card" src="<?php echo $userData["picture"]; ?>" width="100px" size="100px" /><br/>
        <p class="welcome">Bienvenue <a href="<?php echo $userData["link"]; ?>" /><?php echo $userData["name"]; ?></a>.</p>
        <p id="profile-name" class="profile-name-card"></p>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Actions</h3>
            </div>
            <div class="list-group">
                <a href="calendarList.php" class="list-group-item">Lister les calendriers</a>
                <a href="calendarEventAdd.php" class="list-group-item">Ajouter un &eacute;venement</a>
            </div>
        </div>
        <div class="forgot-password"><a href="?logout" class="btn-sm btn-danger" role="button"><span class="glyphicon glyphicon glyphicon-user"></span> Logout</a></div>
    <?php }
    ?>
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
