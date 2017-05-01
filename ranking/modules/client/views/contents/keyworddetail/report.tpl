{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link rel="stylesheet" href="{base_url()}../../js/print/morris/morris.css" type="text/css" media="screen">

  <script src="{base_url()}../../js/chart.min.js"></script>
  <script src="{base_url()}../../js/print/jQuery.jPrintArea.js"></script>

  <script src="{base_url()}../../js/print/morris/raphael.min.js"></script>
  <script src="{base_url()}../../js/print/morris/morris.min.js"></script>

<body>

{* ヘッダー部分　END *}

<script text="javascript/text">
  $(function(){
  $('#btn_print').click(function(){
  $.jPrintArea("#printarea");
  });
});
</script>

<div class="row">
  <div class="col-md-offset-10 col-md-2"><input type="button" id="btn_print" value="印刷する"></div>
</div>
{*
 印刷設定で「余白：最小」「オプション > ヘッダーとフッター：チェックを外す」
*}

{* 印刷範囲指定の開始 *}
<div id="printarea">


<H4><p class="bg-success">&emsp;&emsp;キーワード&emsp;順位レポート</p></H4>

{form_open('keyworddetail/report/' , 'name="detailForm" class="form-horizontal repeater"')}

  <div class="form-group">
    <label class="col-md-2 control-label">対象キーワード設定情報</label>
    <div class="col-md-offset-1 col-md-11">■ 検索キーワード：{$info.kw_keyword}</div>
    <div class="col-md-offset-1 col-md-11">■ 対象URL：{$info.kw_url|unescape:"url"}</div>
    <div class="col-md-offset-1 col-md-11">■ URLマッチタイプ：
      {if $info.kw_matchtype==0}完全一致{/if}
      {if $info.kw_matchtype==1}前方一致{/if}
      {if $info.kw_matchtype==2}ドメイン一致{/if}
      {if $info.kw_matchtype==3}ルートドメイン一致 (サブドメイン含む){/if}
    </div>
    <div class="col-md-offset-1 col-md-11">■ 検索エンジン選択：{if $info.kw_searchengine==0}Google{else}Yahoo!{/if}</div>
    <div class="col-md-offset-1 col-md-11">■ 取得対象デバイス：{if $info.kw_device==0}ＰＣ版{else}モバイル版{/if}</div>
    <div class="col-md-offset-1 col-md-11">■ ロケーション指定：{$info.kw_location_name}</div>
  </div>

  <div class="form-horizontal col-sm-12">
    <div id="rankchart" style="height:400px;"></div>
  </div>

<script>
new Morris.Line({
	  // ID of the element in which to draw the chart.
	  element: 'rankchart',
	  // Chart data records -- each entry in this array corresponds to a point on
	  // the chart.
	  data: {$graph_data},
	  // The name of the data record attribute that contains x-values.
	  xkey: 'date',
	  // A list of names of data record attributes that contain y-values.
	  ykeys: ['rank'],
	  // Labels for the ykeys -- will be displayed when you hover over the
	  // chart.
	  labels: ['Rank'],
	  //ymin: 300,
	  //ymax: 0,
	  ymin: 301,
	  ymax: 1,
	  xLabels: 'decade',
	  hideHover: true,
	  smooth: false,
	  parseTime: true,
	  postUnits: "位"
	});
</script>

  <div class="form-horizontal col-sm-12">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="text-center">date</th>
          {foreach from=$tbl_x_data item=head}
          <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>
            rank
          </td>
      {foreach from=$tbl_y_data item=y_data}
          <td class="text-right">
            {$y_data}
          </td>
      {/foreach}
        </tr>
      </tbody>
    </table>
  </div>

</div>
{* 印刷範囲指定の終了 *}

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-2">
      {$attr['name'] = '_back'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '戻　　る' , 'class="btn btn-primary"')}
    </div>
  </div>

{form_close()}

<!-- </form> -->

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
