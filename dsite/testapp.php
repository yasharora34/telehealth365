<?php
//require '/src/facebook.php';
$facebook = new Facebook(array(
  'appId'  => '1408872926046123',
  'secret' => 'b78e93eba64f0992a25c721a415d340c',
));

$app_id =   '1408872926046123';
$app_secret = 'b78e93eba64f0992a25c721a415d340c';

$user = $facebook->getUser();

if ($user) {
  try {
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}
if ($user) {
    $logoutUrl = $facebook->getLogoutUrl();

    $result = $facebook->api('/me/friends');

    print "<pre>";
    print_r($result);
    print "</pre>";


} else {
  $statusUrl = $facebook->getLoginStatusUrl();
  $loginUrl = $facebook->getLoginUrl(array('scope' => 'user_friends,read_stream, export_stream'));
}


?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>demo</title>
  </head>
  <body>
    <?php if ($user): ?>
      <h3>You</h3>
      <img src="https://graph.facebook.com/<?php echo $user; ?>/picture">
      <a href="<?php echo $logoutUrl; ?>">Logout</a>
    <?php else: ?>
      <div>
        Login:<a href="<?php echo $loginUrl; ?>">Login with Facebook</a>
      </div>
    <?php endif ?>                                
    </body>
</html> 