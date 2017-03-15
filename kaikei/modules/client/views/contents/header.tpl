<!DOCTYPE html>
<html class="no-js" lang="jp">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="">
<title>請求書発行システム &#xB7; INVOICE</title>

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
      {if $mem_Type==1}{*管理者用メニュー*}
        <ul class="list-inline text-right"></ul>
        <nav class="navbar navbar-inverse">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">INVOICE</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/client/top/"><i class="glyphicon glyphicon-home"></i> TOP</a></li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> 顧客情報<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/customerlist/"><i class="glyphicon glyphicon-list-alt"></i> 顧客一覧</a></li>
                <li><a href="/client/customerlist/add/"><i class="glyphicon glyphicon-pencil"></i> 顧客登録</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/data_csvup/customer/"><i class="glyphicon glyphicon-duplicate"></i> 顧客データCSV</a></li>
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-list-alt"></i> 受注案件<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/projectlist/"><i class="glyphicon glyphicon-list-alt"></i> 受注案件一覧</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/projectlist/project_renew/"><i class="glyphicon glyphicon-refresh"></i> 契約延長処理</a></li>
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-list-alt"></i> 請求書<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/invoicelist/"><i class="glyphicon glyphicon-list-alt"></i> 請求書一覧</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/invo_create_fix/"><i class="glyphicon glyphicon-save-file"></i> 請求書 データ作成</a></li>
              </ul>
            </li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-stats"></i> 売上情報<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/saleslist/"><i class="glyphicon glyphicon-yen"></i> 売上情報一覧</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/sales_graph/monthly/"><i class="glyphicon glyphicon-signal"></i> 月次売上表</a></li>
                <li><a href="/client/sales_graph/salesman/"><i class="glyphicon glyphicon-signal"></i> 担当営業別売上表</a></li>
                <li><a href="/client/sales_graph/accounting/"><i class="glyphicon glyphicon-signal"></i> 課金方式別売上表</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/sales_create/"><i class="glyphicon glyphicon-save-file"></i> 売上 データ作成</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-barcode"></i> 支払情報<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/shokailist/"><i class="glyphicon glyphicon-user"></i> 支払先情報一覧</a></li>
                <li><a href="/client/shokailist/add/"><i class="glyphicon glyphicon-pencil"></i> 支払先情報登録</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/shokaifee_create/"><i class="glyphicon glyphicon-usd"></i> 紹介料計算</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/shokaifeelist/"><i class="glyphicon glyphicon-yen"></i> 支払紹介料一覧</a></li>
              </ul>
            </li>
            <li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-wrench"></i> システム管理<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/accountlist/"><i class="glyphicon glyphicon-list-alt"></i> アカウント一覧</a></li>
                <li><a href="/client/accountlist/add/"><i class="glyphicon glyphicon-pencil"></i> アカウント登録</a></li>
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
            <a href="#" class="navbar-brand">INVOICE</a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/client/top/"><i class="glyphicon glyphicon-home"></i> TOP</a></li>
            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-user"></i> 顧客情報<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/customerlist/"><i class="glyphicon glyphicon-list-alt"></i> 顧客一覧</a></li>
                <li><a href="/client/customerlist/add/"><i class="glyphicon glyphicon-pencil"></i> 顧客登録</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/data_csvup/customer/"><i class="glyphicon glyphicon-cloud-upload"></i> 顧客データCSV</a></li>
              </ul>
            </li>
            <li class="active"><a href="/client/projectlist/"><i class="glyphicon glyphicon-list-alt"></i> 受注案件一覧</a></li>
            <li class="active"><a href="/client/invoicelist/"><i class="glyphicon glyphicon-list-alt"></i> 請求書一覧</a></li>

            <li class="active"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-stats"></i> 売上情報<b class="caret"></b></a>
              <ul class="dropdown-menu right">
                <li><a href="/client/saleslist/"><i class="glyphicon glyphicon-yen"></i> 売上データ一覧</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="/client/sales_graph/monthly/"><i class="glyphicon glyphicon-signal"></i> 月次売上表</a></li>
                <li><a href="/client/sales_graph/salesman/"><i class="glyphicon glyphicon-signal"></i> 担当営業別売上表</a></li>
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
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
        <div class="navbar-header">toggle="collapse" data-target="#patern05">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <a href="/admin/login/" class="navbar-brand">INVOICE</a>
        </div>
      </nav>

    </div>
    {/if}

    </div>
