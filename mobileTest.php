<?php

include_once './lib/OAuth.php';
class OAuthSignatureMethod_RSA_SHA1_opOpenSocialPlugin extends OAuthSignatureMethod_RSA_SHA1
{
  protected function fetch_private_cert(&$request) {
  }

  protected function fetch_public_cert(&$request) {
    return <<<EOT
-----BEGIN CERTIFICATE-----

-----END CERTIFICATE-----
EOT;
  }
}
$request = OAuthRequest::from_request(null, null, null);
$signature_method = new OAuthSignatureMethod_RSA_SHA1_opOpenSocialPlugin();
if (!$signature_method->check_signature($request, null, null, $request->get_parameter('oauth_signature')))
{
  echo 'Invalid signature';
  exit;
}

define('CONSUMER_KEY', '#CONSUMER_KEY');
define('CONSUMER_SECRET', '#CONSUMER_SECRET');
define('BASE_URL', '#BASE_URL');
define('APP_URL', '#APP_URL');

$consumer = new OAuthConsumer(CONSUMER_KEY, CONSUMER_SECRET);
$request = OAuthRequest::from_consumer_and_token(
  $consumer,
  null,
  'GET',
  BASE_URL.'/api.php/social/rest/people/@me/@self'
);
$request->set_parameter('xoauth_requestor_id', $_GET['opensocial_owner_id']);
$request->set_parameter('fields', 'gender,birthday,addresses,aboutMe');
$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, null);
$res = do_get($request->get_normalized_http_url(), $request->to_postdata());
$json = json_decode($res, true);
?>

<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
  <title>私です</title>
  <style type="text/css">
    <![CDATA[
      a:link{color:#ffffff;}
      a:visited{color:#ffffff;}
      a:focus{}
      *{
        font-size:x-small;
        color:#ffd83d;
      }
    ]]>
  </style>
</head>
<body>
<!-- Homepage -->
<?php if (is_null($_GET['refid'])): ?>
<table width="100%" bgcolor="#eeeeff">
<tbody>
  <tr>
    <td width="50%" valign="top" align="center">
      <img width="120px" height="120px" src=<?php echo $json['entry'][$i]['thumbnailUrl'] ? $json['entry'][$i]['thumbnailUrl'] : BASE_URL.'/images/no_image.gif' ?> alt="" format="jpg">
    </td>
    <td valign="top">
      <font color="#999966">性別:</font><br><?php echo $json['entry']['gender'] ?><br>
      <font color="#999966">誕生日:</font><br><?php echo $json['entry']['birthday'] ?><br>
      <font color="#999966">都道府県:</font><br><?php echo $json['entry']['addresses'][0]['region'] ?><br>
      <font color="#999966">自己紹介:</font><br><?php echo $json['entry']['aboutMe'] ?><br>
    </td>
  </tr>
 </tbody>
 </table>
<br>
<center>
  <a href="?url=<?php echo urlencode(APP_URL.'?refid=2') ?>">ゲームする</a>
</center>
<br>

<?php
$request = OAuthRequest::from_consumer_and_token(
  $consumer,
  null,
  'GET',
  BASE_URL.'/api.php/social/rest/people/@me/@friends'
);
$request->set_parameter('xoauth_requestor_id', $_GET['opensocial_owner_id']);
$request->set_parameter('count', 10);
$request->set_parameter('fields', 'thumbnailUrl');
$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, null);
$res = do_get($request->get_normalized_http_url(), $request->to_postdata());
$json = json_decode($res, true); 
?>

<table width="100%">
<tbody>
  <tr>
    <td bgcolor="#0d6ddf" align="center" colspan='2'>
      <font color="#eeeeee"><a name="top">ﾌﾚﾝﾄﾞﾘｽﾄ</a></font><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php echo $json['totalResults']?>件中 <?php echo $json['startIndex'] + 1 ?>～<?php echo $json['startIndex'] + $json['itemsPerPage'] ?>件目を表示</center>
    </td>
  </tr>
  <?php for ($i=0;$i<$json['itemsPerPage'];$i++):?>
  <tr>
    <td width="25%" valign="top" align="center">
      <img width="60px" height="60px" src=<?php echo $json['entry'][$i]['thumbnailUrl'] ? $json['entry'][$i]['thumbnailUrl'] : BASE_URL.'/images/no_image.gif' ?> alt="" format="jpg">
    </td>
    <td bgcolor="#ffffff">
      <?php echo $json['entry'][$i]['displayName'] ?>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <hr color="#0d6ddf">
    </td>
  </tr>
  <?php endfor;?>
</tbody>
</table>

<?php if ($json['itemsPerPage'] < $json['totalResults']): ?>
<center><a href="?url=<?php echo urlencode(APP_URL.'?refid=1&p=1') ?>">もっとみる</a></center>
<?php endif; ?>

<!-- Friend List -->
<?php elseif (1 == $_GET['refid']): ?>
<?php
$request = OAuthRequest::from_consumer_and_token(
  $consumer,
  null,
  'GET',
  BASE_URL.'/api.php/social/rest/people/@me/@friends'
);
$request->set_parameter('xoauth_requestor_id', $_GET['opensocial_owner_id']);
$request->set_parameter('count', 10);
$request->set_parameter('startIndex', ($_GET['p']-1)*10);
$request->set_parameter('fields', 'thumbnailUrl');
$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, null);
$res = do_get($request->get_normalized_http_url(), $request->to_postdata());
$json = json_decode($res, true); 
?>

<table width="100%">
<tbody>
  <tr>
    <td bgcolor="#0d6ddf" align="center" colspan='2'>
      <font color="#eeeeee"><a name="top">ﾌﾚﾝﾄﾞﾘｽﾄ</a></font><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php echo $json['totalResults']?>件中 <?php echo $json['startIndex'] + 1 ?>～<?php echo $json['startIndex'] + $json['itemsPerPage'] ?>件目を表示</center>
    </td>
  </tr>
  <?php for ($i=0;$i<$json['itemsPerPage'];$i++):?>
  <tr>
    <td width="25%" valign="top" align="center">
      <img width="60px" height="60px" src=<?php echo $json['entry'][$i]['thumbnailUrl'] ? $json['entry'][$i]['thumbnailUrl'] : BASE_URL.'/images/no_image.gif' ?> alt="" format="jpg">
    </td>
    <td bgcolor="#ffffff">
      <?php echo $json['entry'][$i]['displayName'] ?>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <hr color="#0d6ddf">
    </td>
  </tr>
  <?php endfor;?>
</tbody>
</table>
<center>
  <?php if ($_GET['p'] > 1): ?>
  <a href="?url=<?php echo urlencode(APP_URL.'?refid=1&p='.($_GET['p']-1)) ?>">前へ</a>
  <?php endif; ?> 
  <?php if ($_GET['p']*10 < $json['totalResults']): ?>
  <a href="?url=<?php echo urlencode(APP_URL.'?refid=1&p='.($_GET['p']+1)) ?>">次へ</a>
  <?php endif; ?>
  <br>
  <a href="?url=<?php echo urlencode(APP_URL.'') ?>">マイページに戻る</a>  
</center>
<!-- Game Page -->
<?php elseif (2 == $_GET['refid']): ?>
<center>
<a href="?url=<?php echo urlencode(APP_URL.'?refid=3&result=1') ?>">イシをする</a><br><br>
<a href="?url=<?php echo urlencode(APP_URL.'?refid=3&result=2') ?>">カミをする</a><br><br>
<a href="?url=<?php echo urlencode(APP_URL.'?refid=3&result=3') ?>">ハサミをする</a><br><br>
</center>
<?php elseif (3 == $_GET['refid']): ?>
<?php 
$params = array('xoauth_requestor_id' => $_GET['opensocial_owner_id']);
$request = OAuthRequest::from_consumer_and_token(
  $consumer,
  null,
  'POST',
  BASE_URL.'/api.php/social/rest/activities/@me/@self',
  $params
);

switch ($_GET['result'])
{
  case 1:
    $data = json_encode(array('title' => 'イシをする'));
    break;
  case 2:
    $data = json_encode(array('title' => 'カミをする'));
    break;
  case 3:
    $data = json_encode(array('title' => 'ハサミをする'));
    break;
} 

$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, null);
$result = do_post($request->get_normalized_http_url(),$params, $request, $data);
?>
<center>
<br><a href="?url=<?php echo urlencode(APP_URL) ?>">マイページに戻る</a><br>
</center>
<?php endif; ?>  
</body>
</html>
<?php
function do_get($uri, $data = '')
{
  $h = curl_init();
  curl_setopt($h, CURLOPT_URL, $uri.'?'.$data);
  curl_setopt($h, CURLOPT_POST, false);
  curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

  $result = curl_exec($h);

  curl_close($h);

  return $result;
}

function do_post($uri, $params, $oauthrequest, $data)
{
  $h = curl_init();
  curl_setopt($h, CURLOPT_URL, $uri.'?'.http_build_query($params));
  curl_setopt($h, CURLOPT_POST, true);
  curl_setopt($h, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $oauthrequest->to_header()));
  curl_setopt($h, CURLOPT_POSTFIELDS, $data);
  curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

  $result = curl_exec($h);

  curl_close($h);
  return $result;
}

function do_delete($uri, $params, $oauthrequest, $data)
{
  $h = curl_init();
  curl_setopt($h, CURLOPT_URL, $uri.'?'.http_build_query($params));
  curl_setopt($h, CURLOPT_CUSTOMREQUEST, 'DELETE');
  curl_setopt($h, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $oauthrequest->to_header()));
  curl_setopt($h, CURLOPT_POSTFIELDS, $data);
  curl_setopt($h, CURLOPT_RETURNTRANSFER, true);

  $result = curl_exec($h);

  curl_close($h);
  return $result;
}
