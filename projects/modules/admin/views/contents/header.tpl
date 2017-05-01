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

<script src="{base_url()}../../js/jquery-3.1.1.min.js"></script>
<script src="{base_url()}../../js/bootstrap.min.js"></script>

</head>

<div>
  <section class="container">
    <!-- TwitterBootstrapのグリッドシステムclass="row"で開始 -->
    <div class="row">

    {if $login_chk==TRUE}
        <ul class="list-inline text-right"></ul>
        <nav class="navbar navbar-inverse">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">SEO</a>
        </div>
        <div id="patern01" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/admin/top/"><i class="glyphicon glyphicon-home"></i> TOP</a></li>
            <li class="active"><a href="/admin/clientlist/"><i class="glyphicon glyphicon-list-alt"></i> クライアント一覧</a></li>
            <li class="active"><a href="/admin/clientlist/add/"><i class="glyphicon glyphicon-pencil"></i> クライアント登録</a></li>
            {*<li class="active"><a href="/admin/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> アカウント一覧</a></li>*}
            {*<li class="active"><a href="/admin/accountlist/add"><i class="glyphicon glyphicon-pencil"></i> アカウント発行</a></li>*}
          </ul>
          <ul class="nav navbar-nav navbar-right">
            {*<li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-wrench"></i> システム設定<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/admin/system/mailtpl/"><i class="glyphicon glyphicon-envelope"></i> メールテンプレ管理</a></li>
                  <li><a href="/admin/system/categroup_new/"><i class="glyphicon glyphicon-th-list"></i> カテゴリ並替</a></li>
                  <li><a href="/admin/system/categroup_search/"><i class="glyphicon glyphicon-th-list"></i> カテゴリ編集</a></li>
                  <li><a href="/admin/system/backup/"><i class="glyphicon glyphicon-floppy-save"></i> 手動バックアップ</a></li>
                  <li><a href="/admin/system/sess_destroy/"><i class="glyphicon glyphicon-remove-circle"></i> セッション削除</a></li>
              </ul>
            </li>*}
            <li class="active"><a href="/admin/login/logout/"><i class="glyphicon glyphicon-log-out"></i> ログアウト</a></li>
          </ul>
        </div>
        </nav>
    {else}
    <div class="page-header">
      <ul class="list-inline text-right">
        <li><a href="/">TOP</a></li>
        <li><a href="/client/login/">Clientログイン</a></li>
      </ul>

      <nav class="navbar navbar-inverse">
        <div class="navbar-header">toggle="collapse" data-target="#patern05">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <a href="/admin/login/" class="navbar-brand">アドミン管理</a>
        </div>
      </nav>

    </div>
    {/if}

