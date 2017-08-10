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



<body>

<div class="container-fluid">
  <div class="row">


    <form name="form1">
    <div class="col-md-10">
      <button type="button" id="kw_insert_btn" class="btn btn-primary" data-toggle="modal">キーワード追加</button>
      <button type="button" id="kw_update_btn" class="btn btn-primary" data-toggle="modal">キーワード編集</button>
      <button type="button" id="kw_delete_btn" class="btn btn-primary" data-toggle="modal">キーワード削除</button>
      <button type="button" id="kw_report_btn" class="btn btn-primary" data-toggle="modal">レポート</button>
    </div>
    <div class="col-md-2 text-right">
      <button class="btn btn-primary" formaction="/srct/login/logout/">ログアウト</button>
    </div>
    </form>




{* モーダル（編集/削除/レポート）処理 *}
{include file="../../../../../public/js/my/top_modal.php"}



	{*** header : キーワード追加 ***}
	<div class="modal fade" id="kw_insert" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">キーワード追加</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_kwinsert"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="modal fade" id="kw_insert_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">キーワード追加</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_kwinsert_chk"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>






	{*** header : キーワード編集 ***}
	<div class="modal fade" id="kw_update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">キーワード編集</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_kwupdate"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

	<div id="modal-result"></div>

	<div class="modal fade" id="kw_update_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">キーワード編集</h4>
	            </div>
	            <div class="modal-body">
	                <form>

	                    <div id="result_kwupdate_chk"></div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>





	{*** header : キーワード削除 ***}
	<div class="modal fade" id="kw_delete" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">キーワード削除</h4>
	            </div>
	            <div class="modal-body">
	                {form_open('topdetail/delchk/' , 'name="accountForm" class="form-horizontal repeater"')}
	                    <div id="result_kwdelete"></div>

	                {form_close()}
	            </div>
	        </div>
	    </div>
	</div>

	<div class="modal fade" id="kw_delete_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
	    <div class="modal-dialog" role="document">
	        <div class="modal-content">
	            <div class="modal-header">
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                <h4 class="modal-title">キーワード削除チェック</h4>
	            </div>
	            <div class="modal-body">
	                <form>
	                    <div id="result_kwdelete_chk"></div>

	                    <div class="modal-footer">
	        				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        				<button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">O  K</button>
	      				</div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>







	<hr>









	{*** 左サイド ***}
    <div class="col-md-3" style="height:800px;background-color:#f5f5f5;">

    <div class="form-group noprint">
      <ul class="nav nav-tabs">
        <li role="presentation" {if $tabs=="rd"}class="active"{/if}><a href="/srct/top/index/rd/">ルートドメイン</a></li>
        <li role="presentation" {if $tabs=="gr"}class="active"{/if}><a href="/srct/top/index/gr/">グループ</a></li>
      </ul>
    </div>

	{if $tabs=="rd"}
		{* ルートドメイン一覧 *}
		<div id="contents" class="container" style="overflow: auto;">

			{$old_rootdomain = ""}
			{$flg=0}

			{$cnt=0}
			{foreach from=$list_kw item=kw name="cnt"}
			  {if $old_rootdomain != $kw.kw_rootdomain}
			    {$old_rootdomain = $kw.kw_rootdomain}
			    <p class="text-left" id='rd_name{$cnt}'>{$kw.kw_rootdomain}</p>
			  {/if}


<script>
// ルートドメイン選択から右ブロックにキーワード情報を表示
$(function () {
    // 「#execute」をクリックしたとき
    $("#rd_name{$cnt}").click(function () {
    	var rootdomain = $("#rd_name{$cnt}").text();
    	console.log(rootdomain);

    	$('#result_rank_tb').load('/srct/topdetail/index_aj_rd/',{
    		'kw_rootdomain': rootdomain,
    	});
    });
});
</script>


			  {$cnt=$cnt+1}
			{foreachelse}
			  ルートドメイン情報がありません。
			{/foreach}

	    </div>

	{else}
		{* グループ一覧 *}
		<div id="contents" class="container" style="overflow: auto;">

			{$old_gtname = ""}
			{$flg=0}

			{$cnt=0}
			{foreach from=$list_kw item=kw name="cnt"}
			  {if $old_gtname != $kw.kw_group}
			    {$old_gtname = $kw.kw_group}
			    <p class="text-left" id='gt_name{$cnt}'>{$kw.kw_group}</p>
			  {/if}


<script>
// ルートドメイン選択から右ブロックにキーワード情報を表示
$(function () {
    // 「#execute」をクリックしたとき
    $("#gt_name{$cnt}").click(function () {
    	var group = $("#gt_name{$cnt}").text();
    	console.log(group);

    	$('#result_rank_tb').load('/srct/topdetail/index_aj_group/',{
    		'kw_group': group,
    	});
    });
});
</script>


			  {$cnt=$cnt+1}
			{foreachelse}
			  ルートドメイン情報がありません。
			{/foreach}

	    </div>

	{/if}




    </div>


























    {*** 右サイド ***}
    <div class="col-md-9" style="height:800px;background-color:#f5f5f5;overflow:auto;">

    {form_open('/top/search/' , 'name="searchForm" class="form-horizontal"')}

	  <div class="row">
	    <div class="col-md-9">
	      {form_input('free_keyword' , set_value('free_keyword', {$seach_free_keyword}) , 'class="form-control form-control input-sm" placeholder="フリーキーワード"')}
	    </div>
	    <div class="col-md-1">
	      {$attr['name']  = 'submit'}
	      {$attr['type']  = 'submit'}
	      {$attr['value'] = '_submit'}
	      {form_button($attr , '検索' , 'class="btn btn-default btn-md"')}
	    </div>
	  </div>

	{form_close()}



	<div id="result_rank_tb">

	{* 右サイド：キーワード一覧テーブルのTPL呼び出し *}
	{include file="../topdetail/index_r_table.tpl"}

    </div>
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

