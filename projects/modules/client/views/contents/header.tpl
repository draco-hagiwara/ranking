<!DOCTYPE html>
<html class="no-js" lang="jp">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<title>案件管理システム &#xB7; PROJECTS</title>

{* Versionと並び順に注意 *}
<link href="{base_url()}../../css/bootstrap.min.css" rel="stylesheet">

{*<script src="{base_url()}../../js/jquery-3.1.1.min.js"></script>*}
<script src="{base_url()}../../js/jquery-2.1.4.min.js"></script>
<script src="{base_url()}../../js/bootstrap.min.js"></script>

</head>

<div>
  <section class="container">
    <!-- TwitterBootstrapのグリッドシステムclass="row"で開始 -->
    <div class="row">

      <ul class="list-inline text-right">
        <li>{$mem_Name}</li>
      </ul>

    {if $login_chk==TRUE}
      {if (($mem_Type==1) OR ($mem_Type==9))}{*管理者用メニュー*}
        <ul class="list-inline text-right"></ul>
        <nav class="navbar navbar-inverse">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">PROJ</a>
        </div>
        <div id="patern05" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/client/top/"><i class="glyphicon glyphicon-home"></i> TOP</a></li>
            <li class="active"><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> SEO案件一覧</a></li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> 顧客<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/customerlist/"><i class="glyphicon glyphicon-list-alt"></i> 顧客一覧</a></li>
                <li><a href="/client/customerlist/add/"><i class="glyphicon glyphicon-pencil"></i> 顧客登録</a></li>
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> アカウント<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> アカウント一覧</a></li>
                <li><a href="/client/accountlist/add/"><i class="glyphicon glyphicon-pencil"></i> アカウント登録</a></li>
              </ul>
            </li>
            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-stats"></i> グラフ関連<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/graph/"><i class="glyphicon glyphicon-stats"></i> グラフ表示</a></li>
                <li><a href="/client/graph/graph_print"><i class="glyphicon glyphicon-stats"></i> グラフ印刷</a></li>
                <li><a href="/client/graph/pdf_test"><i class="glyphicon glyphicon-stats"></i> PDFテスト</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-wrench"></i> システム管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> アカウント一覧</a></li>
                <li><a href="/client/accountlist/add/"><i class="glyphicon glyphicon-user"></i> アカウント登録</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/rank_create/"><i class="glyphicon glyphicon-refresh"></i> 手動順位データ取得</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/system/backup/"><i class="glyphicon glyphicon-floppy-open"></i> 手動バックアップ</a></li>
                <li><a href="/client/system/sess_destroy/"><i class="glyphicon glyphicon-trash"></i> セッション削除</a></li>
                <li><a href="/client/system/memcached_delete/"><i class="glyphicon glyphicon-trash"></i> MEMキャッシュ削除</a></li>
              </ul>
            </li>
            <li class="active"><a href="/client/login/logout/"><i class="glyphicon glyphicon-log-out"></i> ログアウト</a></li>
          </ul>
        </div>
        </nav>
      {elseif $mem_Type==0}{*一般用メニュー*}
        <ul class="list-inline text-right"></ul>
        <nav class="navbar navbar-inverse">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">Fnote</a>
        </div>
        <div id="patern05" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/client/top/">TOP</a></li>
            <li class="active"><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> SEOランキング一覧</a></li>
            <li class="active"><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> SEO登録</a></li>
            <li class="active"><a href="/client/accountlist/detail/"><i class="glyphicon glyphicon-pencil"></i> アカウント編集</a></li>
            <li><a href="/client/graph/"><i class="glyphicon glyphicon-pencil"></i> グラフ表示</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/client/login/logout/">ログアウト</a></li>
          </ul>
        </div>
        </nav>
      {/if}
    {else}
    <div class="page-header">
      <ul class="list-inline text-right">
        <li><a href="/">TOP</a></li>
        <li><a href="/admin/login/">Adminログイン</a></li>
      </ul>

      <nav class="navbar navbar-inverse">
        <div class="navbar-header">toggle="collapse" data-target="#patern05">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <a href="/admin/login/" class="navbar-brand">案件管理</a>
        </div>
      </nav>

    </div>
    {/if}

    </div>
