<!DOCTYPE html>
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
    
    <link rel="icon" href="/favicon.ico" type="image/x-ico"/>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-ico"/>

    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    
    <link href="/_static/css/jumbotron-narrow.css" rel="stylesheet">
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
<!-- Modal Help -->
<div class="modal fade" id="myModalHelp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="color:#666">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Help</h4>
      </div>
      <div class="modal-body">
        <!-- 
        Как добавить новый поток?

1 - Получить ссылку на RSS или Atom, на сайтах обозначают вот таким значком (*). RSS/Atom имеют структуры XML, вот ссылка для примера: hhh.
2 - Создать аккаунт на любом из Pod Diaspora
3 - Оформить аккаунт в соответствие сайту с которого будет браться поток (теги, название и аватарка)
4 - На данном сайте нажать кнопку '+feed' и ввести соответствующие данные
      -->
        <h2>How to add a new thread?</h2>
        <p>1 - Get a link to the RSS or Atom, to designate sites like this with <i class="fa fa-rss"></i>. RSS/Atom structures are XML, here is the link for an example: http://lifehacker.com/rss</p>
        <p>2 - Create an account on any of <a href="http://podupti.me/" target="_blank">pod</a> Diaspora*</p>
        <p>3 - Making an account in accordance with the site which will be taken stream (tags, name and avatar)</p>
        <p>4 - At this site click '+feed' and enter the appropriate data</p>
        <br>
        <h2>How to add a new thread Twitter?</h2>
        <p>1 - Get the name of the account Twitter, for example: from Ashton Kutcher is here <i>https://twitter.com/aplusk</i> account and account name <b>aplusk</b></p>
        <p>2 - Create an account on any of <a href="http://podupti.me/" target="_blank">pod</a> Diaspora*</p>
        <p>3 - Making an account in accordance with the site which will be taken stream (tags, name and avatar)</p>
        <p>4 - At this site click '+feed' and enter the appropriate data</p>
      </div>
    </div>
  </div>
</div>
<!-- Modal ADD -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="color:#666">
    <div class="modal-content">
      <script>
      function guid() {
        function s4() {
          return Math.floor((1 + Math.random()) * 0x10000)
            .toString(16)
            .substring(1);
        }
        return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
      }
      function redrawForm(){
        var zzz = $("#selFeed option:selected").val();
        if(zzz=="rss") {
          $("#inputFeedURL").attr("placeholder","Url feed RSS/Atom");
          $("#inputFeedURL").removeAttr("readonly");
          $("#inputFeedURL").val("");
        } else if(zzz=="twitter") {
          $("#inputFeedURL").attr("placeholder","Name twitter account");
          $("#inputFeedURL").removeAttr("readonly");
          $("#inputFeedURL").val("");
        } else if(zzz=="facebook") {
          $("#inputFeedURL").attr("placeholder","Name facebook account or id");
          $("#inputFeedURL").removeAttr("readonly");
          $("#inputFeedURL").val("");
        } else if(zzz=="token") {
          $("#inputFeedURL").attr("placeholder","Token API");
          $("#inputFeedURL").attr("readonly","readonly");
          $("#inputFeedURL").val(guid());
        }
      }
      redrawForm();
      </script>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">add feed</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" role="form" method="POST" action="/add/">
          <div class="form-group">
            <label for="inputFeedURL" class="col-sm-2 control-label">Type</label>
            <div class="col-sm-10">
              <select class="form-control" name="selectFeedType" id="selFeed" onclick="redrawForm()">
                <option value="rss" selected="selected">RSS/Atom</option>
                <option value="twitter">Twitter</option>
                <option value="facebook">Facebook</option>
                <option value="token">Token API</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="inputFeedURL" class="col-sm-2 control-label">Feed</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputFeedURL" autocomplete="off" name="inputFeedURL" placeholder="Url feed RSS/Atom or Twitter or Facebook account">
            </div>
          </div>
          <div class="form-group">
            <label for="inputURLPOD" class="col-sm-2 control-label">POD</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputURLPOD" autocomplete="off" name="inputURLPOD" placeholder="Url pod, e.g.: https://diasp.org">
            </div>
          </div>
          <div class="form-group">
            <label for="inputUsrNmPOD" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputUsrNmPOD" autocomplete="off" name="inputUsrNmPOD" placeholder="Username pod">
            </div>
          </div>
          <div class="form-group">
            <label for="inputPasswordAcPOD" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="inputPasswordAcPOD" autocomplete="off" name="inputPasswordAcPOD" placeholder="Password pod">
            </div>
          </div>
          <div class="form-group">
            <label for="inputStringFooter" class="col-sm-2 control-label">Footer</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="inputStringFooter" autocomplete="off" name="inputStringFooter" placeholder="String for footer feeds">
            </div>
          </div>
          <div class="form-group">
            <label for="inputViewURLFeeds" class="col-sm-2 control-label">View URL</label>
            <div class="col-sm-10">
              <input type="checkbox" id="inputViewURLFeeds" name="inputViewURLFeeds" checked> View URL for feeds
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              On one account Diaspora* - one thread RSS/Atom or Twitter or Facebook
            </div>
          </div>

          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit">add</button>
            </div>
          </div>
        </form>
      </div>
      <!--<div class="modal-footer"></div>-->
    </div>
  </div>
</div>

<div style="height: 100%;  overflow-x: hidden;  overflow-y: auto;  z-index: 9;  padding: 0 0 0 10px;  position: relative;">

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li><a href="#" data-toggle="modal" data-target="#myModal">+feed</a></li>
          <li><a href="#" data-toggle="modal" data-target="#myModalHelp">Help</a></li>
          <li><a href="/logout/">Sign out</a></li>
        </ul>
        
        <a href="/"><img src="/_static/img/logoD.png"></a>
      </div>

      <div class="row marketing">
        <div>