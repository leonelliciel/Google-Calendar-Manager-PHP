<?php
session_start();
require_once 'google-api-php-client-master/src/Google/autoload.php';

include('parameters.php');

$redirect_uri = 'http://localhost/liciel/google_agenda_syncro/test.php';


$client = new Google_Client();
$client->setApplicationName("Client_Library_Examples");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');   // Gets us our refreshtoken


$client = new Google_Client();
$client->setApplicationName("Client_Library_Examples");
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->setAccessType('offline');   // Gets us our refreshtoken

$client->setScopes(array('https://www.googleapis.com/auth/calendar.readonly'));


//For loging out.
if (isset($_GET['logout'])) {
    unset($_SESSION['token']);
}


// Step 2: The user accepted your access now you need to exchange it.
if (isset($_GET['code'])) {

    $client->authenticate($_GET['code']);
    $_SESSION['token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

// Step 1:  The user has not authenticated we give them a link to login
if (!isset($_SESSION['token'])) {

    $authUrl = $client->createAuthUrl();

    print "<a class='login' href='$authUrl'>Connect Me!</a>";
}


// Step 3: We have access we can now create our service

print_r($_SESSION['token']);

if (isset($_SESSION['token'])) {
    $client->setAccessToken($_SESSION['token']);
    print "<a class='logout' href='http://www.daimto.com/Tutorials/PHP/GCOAuth.php?logout=1'>LogOut</a><br>";

    $service = new Google_Service_Calendar($client);

    $calendarList  = $service->calendarList->listCalendarList();;

    while(true) {
        foreach ($calendarList->getItems() as $calendarListEntry) {

            echo $calendarListEntry->getSummary()."<br>\n";


            // get events
            $events = $service->events->listEvents($calendarListEntry->id);


            foreach ($events->getItems() as $event) {
                echo "-----".$event->getSummary()."<br>";
            }
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