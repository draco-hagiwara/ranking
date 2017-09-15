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

{* FontAwesome *}
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

<script src="{base_url()}../../js/jquery-2.1.4.min.js"></script>
<script src="{base_url()}../../js/bootstrap.min.js"></script>

<div>
  <section class="container">

{* ヘッダー部分　START *}
{*include file="../header.tpl" head_index="1"*}
<ul class="list-inline text-right"><li>{$mem_Name}</li></ul>

</head>

{* ヘッダー部分　END *}

<body>

<div id="contents" class="container">

	{*** header : アカウント追加 ***}
	{* モーダル定義 *}
	<div class="modal fade" id="ac_insert" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">アカウント追加</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_acinsert"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="modal fade" id="ac_insert_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">アカウント追加</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_acinsert_chk"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	{*** header : アカウント編集 ***}
	<div class="modal fade" id="ac_update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">アカウント編集</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_acupdate"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	<div id="modal-result"></div>

	<div class="modal fade" id="ac_update_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">アカウント編集</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_acupdate_chk"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>


  {if $smarty.session.c_memType==0||$smarty.session.c_memType==9}
    <form name="form1">
      <div class="col-md-4">
        <button type="button" id="ac_insert_btn" class="btn btn-success btn-xs" data-toggle="modal">追&emsp;加</button>
      </div>
    </form>
  {else}
    <div class="col-md-4"></div>
  {/if}

  <div class="col-md-offset-6 col-md-2 text-right">
    <a href="#" onClick="window.close(); return false;"><button class="btn btn-success btn-xs">× 閉じる</button></a>
  </div>

  <br>
  <div class="form-group">
    <H4><p class="bg-success"><u>アカウント一覧</u></p></H4>
  </div>

	<div id="result_account_table">

	{* アカウント一覧テーブルのTPL呼び出し *}
	{include file="../topdetail/index_account_table.tpl"}

	</div>

</div>

{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
