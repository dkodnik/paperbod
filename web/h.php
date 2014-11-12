<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="en-gb">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="DNK team">
    <meta name="copyright" content="(c) 2014 DNK team">
    <meta name="keywords" content="paperbod, paperboy, paperbot, diaspora, diasp, boy, bot, twitter, rss, feed, facebook, news, feeds">
    <meta name="description" content="PaperboD* - feeds to diaspora">

    <meta name="msvalidate.01" content="E2B51081850FFEA4EC6219BA0D1B22A9" />

    <title>PaperboD*</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
    <script src="/_static/js/jquery.backstretch.js" type="text/javascript" ></script>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <link href="/_static/css/cover.css" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-ico"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-ico"/>

    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
</head>

<body>
	<div id="backgroundfont"><div id="backgroundfont2"></div></div>
	<script>
		$("#backgroundfont2").backstretch("/_static/img/bg3.jpg");
	</script>

<!-- Modal Terms -->
<div class="modal fade" id="myModalTerms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="color:#666;text-align:left;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Terms</h4>
      </div>
      <div class="modal-body">
        <!--
        1. - Для одного аккаунта в POD Diaspora (POD*) можно подключить только один RSS поток.
        2. - Аккаунт POD* должен быть оформлен (аватар и теги) в соответствие с тематикой сайта владельца RSS потока.
        3. - Запрещено создание RSS потоков, если такой поток для Diaspora* (D*) уже создан, независимо на каком POD*.
        4. - Если владелец сайта RSS потока, намерен транслировать со своего аккаунта в D*, то RSS поток будет передан владельцу.
         -->
        <p>1. - PaperboD - not responsible for feeds.</p>
        <p>2. - For one account to POD Diaspora (POD*) can connect only one RSS Feed or Twitter Account.</p>
        <p>3. - Account POD* must be completed (picture and tags) in accordance with the theme of the site owner RSS stream or Twitter Account.</p>
        <p>4. - Prohibited creating RSS or Twitter feeds, if such a flow for Diaspora* (D*) has been created, no matter in what POD*.</p>
        <p>5. - If the owner of a site RSS flow or Twitter Account, intend to broadcast from his account in D*, then the RSS Feed or Twitter Account will be transferred to the owner.</p>
        <br>
        <br>
        <p><i>10 Feb 2014</i></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

	
    <div class="site-wrapper">

      <div class="site-wrapper-inner">

        <div class="cover-container">

          <div class="masthead clearfix">
            <div class="inner">
              <h3 class="masthead-brand"><a href="/"><img src="/_static/img/logoD.png"></a></h3>
              <ul class="nav masthead-nav">
                <?php $retSess=checkSession(); if ($retSess!=RS_OK) { ?>
                  <li><a href="/login/">Sign in</a></li>
                  <li><a href="/register/">Sign up</a></li>
                <?php } else { ?>
                  <li><a href="#" data-toggle="modal" data-target="#myModal">+feed</a></li>
                  <li><a href="/logout/">Sign out</a></li>
                <?php } ?>
              </ul>
            </div>
          </div>