{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link href="{base_url()}../../js/select2/select2.css" rel="stylesheet">
  <link rel="stylesheet" href="{base_url()}../../js/jqPlot/jquery.jqplot.min.css" type="text/css" media="screen">

  <link href="{base_url()}../../css/my/top.css" rel="stylesheet">

  <script type="text/javascript" src="{base_url()}../../js/jqPlot/jquery.jqplot.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.cursor.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.highlighter.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>

  {*<script type="text/javascript" src="{base_url()}../../js/my/toggleslide.js"></script>*}

  <script src="{base_url()}../../js/select2/select2.min.js"></script>

  <script src="{base_url()}../../js/bottom/jquery.bottom-1.0.js"></script>

<script>
function open_lolist() {

	console.log("on_click");

    window.open("about:blank","location-list","width=650,height=600,scrollbars=yes");
    var form = document.form1;
    form.target = "location-list";
    form.method = "post";
    form.action = "/srct/topdetail/location_list/";
    form.submit();
}
</script>

<body>

<div class="container-fluid">
  <div class="row">

    <form name="form1">
    <div class="col-md-7">
      {if $smarty.session.c_memKw==1}
      <button type="button" id="kw_insert_btn" class="btn btn-success" data-toggle="modal">キーワード追加</button>
      <button type="button" id="kw_update_btn" class="btn btn-success" data-toggle="modal">キーワード編集</button>
      <button type="button" id="kw_delete_btn" class="btn btn-success" data-toggle="modal">キーワード削除</button>
      {/if}
      <button type="button" id="kw_report_btn" class="btn btn-success" data-toggle="modal">レポート</button>
    </div>
    <div class="col-md-3 text-right">

	  <button class="btn btn-success dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
		CSVファイル
		<span class="caret"></span>
	  </button>
	  <ul class="dropdown-menu" role="menu">
		<li role="presentation" id="csv_download"><a href="#">■ キーワード情報 ダウンロード</a></li>
		<li role="presentation" id="csv_upload_btn" data-toggle="modal"><a href="#">■ キーワード情報 アップロード</a></li>
		<li role="presentation" id="location_list" onclick="open_lolist();"><a href="#">■ 地域（ロケーション）リスト</a></li>
	  </ul>

      <button type="button" id="account_btn" class="btn btn-success" data-toggle="modal">アカウント情報</button>
    </div>
    </form>

    <form name="form2">
    <div class="col-md-2 text-right">
      <button class="btn btn-success" formaction="/srct/login/logout/">ログアウト</button>
    </div>
    </form>

	{* メニューボタン：モーダルのTPL呼び出し *}
	{include file="../topdetail/index_modal.tpl"}

	<hr>

	{*** 左サイド ***}
    <div class="col-md-3" style="height:800px;background-color:#f5f5f5;">

	  {* tabsメニュー *}
      <div class="form-group noprint">
        <ul class="nav nav-tabs">
          <li role="presentation" {if $tabs=="rd"}class="active"{/if}><a href="/srct/top/index/rd/">ルートドメイン</a></li>
          <li role="presentation" {if $tabs=="gr"}class="active"{/if}><a href="/srct/top/index/gr/">グループ</a></li>
        </ul>
      </div>

	  <div id="contents" class="container" style="width:250px;height:750px;background-color:#f5f5f5;overflow: auto;">

	    <div id="result_list_tb">

	    {* 左サイド：「ルートドメイン一覧」＆「グループ一覧」テーブルのTPL呼び出し *}
	    {include file="../topdetail/index_l_table.tpl"}

        </div>

      </div>
    </div>

    {*** 右サイド ***}
    <div class="col-md-9" style="background-color:#f5f5f5;overflow:auto;">

	  {* フリーキーワード検索 *}
      {form_open('/top/search/' , 'name="searchForm" class="form-horizontal"')}

	    <div class="row">
	      <div class="col-md-9">
	        {form_input('free_keyword' , set_value('free_keyword', {$seach_free_keyword}) , 'class="form-control form-control input-sm f_keyword" placeholder="フリーキーワード"')}
	      </div>
	      <div class="col-md-1">
	        {$attr['name']  = 'submit'}
	        {$attr['type']  = 'submit'}
	        {$attr['value'] = '_submit'}
	        {form_button($attr , '検索' , 'class="btn btn-default btn-md"')}
	      </div>
	    </div>
	  {form_close()}

	  <p class="kwcnt_rank_tb text-right">KW件数： {$list_cnt}件</p>

    </div>

	{* キーワード一覧テーブル *}
	<div id="result_rank_tb">

	  {* 右サイド：キーワード一覧テーブルのTPL呼び出し *}
	  {include file="../topdetail/index_r_table.tpl"}

	</div>

  </div>
</div>

<br>
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>