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
  <div class="col-md-offset-11 col-md-1"><input type="button" id="btn_print" value="印刷する"></div>
</div>
{*
 印刷設定で「余白：最小」「オプション > ヘッダーとフッター：チェックを外す」
*}

{* 印刷範囲指定の開始 *}
<div id="printarea">


<H4><p class="bg-success"><u>キーワード&emsp;順位レポート</u></p></H4>

{form_open("{$back_page}/search/{$seach_page_no}/" , 'name="detailForm" class="form-horizontal"')}

  <div class="form-group">
    <br>
    <label class="col-md-2 control-label">対象キーワード設定情報</label>
    <br>
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

  <br><br>

  <div class="form-horizontal col-sm-12">
    <label class="col-md-4 control-label">グラフ表示期間：{$end_date} ～ {$start_date} ({$nisuu}日間)</label>
    <div id="rankchart" style="height:400px;"></div>
  </div>


{include file="../../../../../public/js/my/report_graph.php"}


  <div class="form-horizontal col-sm-12">
    <table class="table table-striped table-hover ">
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
    <div class="col-sm-offset-0 col-sm-1">
      <br><br>
      {$attr['name'] = '_back'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '戻&emsp;&emsp;る' , 'class="btn btn-primary  btn-sm"')}
    </div>
  </div>

{form_close()}

<!-- </form> -->

    </div>
  </section>
</div>

<br><br>
<section class="container">
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}
</section>

<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
