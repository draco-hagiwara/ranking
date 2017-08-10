<!DOCTYPE html>
<html class="no-js" lang="jp">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<title>順位チェックツール &#xB7; SEO-RANK</title>

{* Versionと並び順に注意 *}
<link href="{base_url()}../../css/bootstrap.min.css" rel="stylesheet">


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
            <a href="#" class="navbar-brand">RANKING</a>
        </div>
        <div id="patern05" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/client/top/"><i class="glyphicon glyphicon-home"></i> TOP</a></li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-search"></i> キーワード管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/keywordlist/"><i class="glyphicon glyphicon-list-alt"></i> キーワード一覧</a></li>
                {if $mem_Kw==1}<li><a href="/client/keywordlist/add/"><i class="glyphicon glyphicon-pencil"></i> キーワード登録</a></li>{/if}
                <li role="separator" class="divider"></li>
                <li><a href="/client/data_csv/project/"><i class="glyphicon glyphicon-cloud-upload"></i> KWデータCSV</a></li>
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-tags"></i> タグ管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/taglist/"><i class="glyphicon glyphicon-list-alt"></i> タグ一覧</a></li>
                {if $mem_Tg==1}<li><a href="/client/taglist/add/"><i class="glyphicon glyphicon-pencil"></i> タグ登録＆更新</a></li>{/if}
                {if $mem_Tg==99}<li><a href="/client/taglist/tag_test/"><i class="glyphicon glyphicon-pencil"></i> タグtest</a></li>{/if}
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> グループ管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/grouplist/"><i class="glyphicon glyphicon-list-alt"></i> グループ一覧</a></li>
                {if $mem_Gp==1}<li><a href="/client/grouplist/add/"><i class="glyphicon glyphicon-pencil"></i> グループ登録＆更新</a></li>{/if}
              </ul>
            </li>
            <li class="active"><a href="/client/rootdomainlist/"><i class="glyphicon glyphicon-cloud"></i> ルートドメイン管理</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-wrench"></i> システム管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> アカウント一覧</a></li>
                <li><a href="/client/accountlist/add/"><i class="glyphicon glyphicon-user"></i> アカウント登録</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/rank_create/"><i class="glyphicon glyphicon-signal"></i> 検索 & 順位データ取得</a></li>
                {*<li><a href="/client/redirect_chk/"><i class="glyphicon glyphicon-share"></i> リダイレクト・チェック</a></li>*}
                <li role="separator" class="divider"></li>
                <li><a href="/client/system/backup/"><i class="glyphicon glyphicon-save"></i> 手動バックアップ</a></li>
                <li><a href="/client/system/sess_destroy/"><i class="glyphicon glyphicon-trash"></i> セッション削除</a></li>
                <li><a href="/client/test_data_create/create_kw_ranking/"><i class="glyphicon glyphicon-tent"></i> テストデータの作成</a></li>
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
            <a href="#" class="navbar-brand">RANKING</a>
        </div>
        <div id="patern05" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/client/top/"><i class="glyphicon glyphicon-home"></i> TOP</a></li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-search"></i> キーワード管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/keywordlist/"><i class="glyphicon glyphicon-list-alt"></i> キーワード一覧</a></li>
                {if $mem_Kw==1}<li><a href="/client/keywordlist/add/"><i class="glyphicon glyphicon-pencil"></i> キーワード登録 & 更新</a></li>{/if}
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-tag"></i> タグ管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/taglist/"><i class="glyphicon glyphicon-list-alt"></i> タグ一覧</a></li>
                {if $mem_Tg==1}<li><a href="/client/taglist/add/"><i class="glyphicon glyphicon-pencil"></i> タグ登録＆更新</a></li>{/if}
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> グループ管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/grouplist/"><i class="glyphicon glyphicon-list-alt"></i> グループ一覧</a></li>
                {if $mem_Gp==1}<li><a href="/client/grouplist/add/"><i class="glyphicon glyphicon-pencil"></i> グループ登録＆更新</a></li>{/if}
              </ul>
            </li>
            <li class="active"><a href="/client/rootdomainlist/"><i class="glyphicon glyphicon-cloud"></i> ルートドメイン管理</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-wrench"></i> システム管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> アカウント一覧</a></li>
              </ul>
            </li>
            <li class="active"><a href="/client/login/logout/"><i class="glyphicon glyphicon-log-out"></i> ログアウト</a></li>
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
        <div class="navbar-header">
          <a href="#" class="navbar-brand">RANKING</a>
        </div>
      </nav>

    </div>
    {/if}

    </div>
