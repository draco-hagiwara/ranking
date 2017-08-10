{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <link href="{base_url()}../../css/my/top.css" rel="stylesheet">

<style>
.popover {
    max-width: 500px;
}
</style>

<body>
{* ヘッダー部分　END *}

  <script type="text/javascript" src="{base_url()}../../js/my/fmsubmit.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jquery.sparkline.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/my/toggleslide.js"></script>


<div id="contents" class="container">

{form_open('/top/search/' , 'name="searchForm" class="form-horizontal"')}

  <div class="row">
    <div class="col-md-10">
      {form_input('free_keyword' , set_value('free_keyword', {$seach_free_keyword}) , 'class="form-control" placeholder="フリーキーワード"')}
    </div>
    <div class="col-md-2">
      {$attr['name']  = 'submit'}
      {$attr['type']  = 'submit'}
      {$attr['value'] = '_submit'}
      {form_button($attr , '検&emsp;&emsp;索' , 'class="btn btn-default btn-md"')}
    </div>
  </div>

  <dl id="acMenu">
  <dt>
    &emsp;&emsp;詳細検索
  </dt>

  <dd>
  <table class="table table-hover table-bordered">
    <tbody>
      <tr>
        <td class="col-md-1">キーワード</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_keyword' , set_value('kw_keyword', {$seach_kw_keyword}) , 'class="form-control"')}
          {if form_error('kw_keyword')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_keyword')}</font></label>{/if}
        </td>
        <td class="col-md-1">対象URL</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_domain' , set_value('kw_domain', {$seach_kw_domain}) , 'class="form-control"')}
          {if form_error('kw_domain')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_domain')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">グループ名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_group' , set_value('kw_group', {$seach_kw_group}) , 'class="form-control"')}
          {if form_error('kw_group')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_group')}</font></label>{/if}
        </td>
        <td class="col-md-1">タグ名</td>
        <td colspan="3" class="col-md-5 input-group-sm">
          {form_input('kw_tag' , set_value('kw_tag', {$seach_kw_tag}) , 'class="form-control"')}
          {if form_error('kw_tag')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_tag')}</font></label>{/if}
        </td>
      </tr>
      <tr>
        <td class="col-md-1">URLﾏｯﾁﾀｲﾌﾟ</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_matchtype', $options_matchtype, set_value('kw_matchtype', {$seach_matchtype}))}
        </td>
        <td class="col-md-1">検索ｴﾝｼﾞﾝ</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_searchengine', $options_searchengine, set_value('kw_searchengine', {$seach_searchengine}))}
        </td>
        <td class="col-md-1">デバイス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_device', $options_device, set_value('kw_device', {$seach_device}))}
        </td>
        <td colspan="2" class="col-md-1"></td>
        {*<td class="col-md-1">担当者</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_ac_seq', $options_accountlist, set_value('accountlist', {$seach_accountlist}))}
        </td>*}
      </tr>
      <tr>
        <td class="col-md-1">ステータス</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('kw_status', $options_kw_status, set_value('kw_status', {$seach_kw_status}))}
        </td>
        <td class="col-md-1">並び替え</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('orderid', $options_orderid, set_value('orderid', {$seach_orderid}))}
        </td>
        <td class="col-md-1">Kw ｳｫｯﾁﾘｽﾄ</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('watch_kw', $options_watchlist, set_value('watch_kw', {$seach_watch_kw}))}
        </td>
        <td class="col-md-1">D ｳｫｯﾁﾘｽﾄ</td>
        <td class="col-md-2  btn-md">
          {form_dropdown('watch_domain', $options_watchdomain, set_value('watch_domain', {$seach_watch_domain}))}
        </td>
      </tr>
    </tbody>
  </table>
  </dd>
  </dl>

{form_close()}

<ul class="pagination pagination-sm">
  ﾙｰﾄﾄﾞﾒｲﾝ検索結果： {$countall}件<br />
  {$set_pagination}
</ul>



{* ウォッチリスト追加削除処理 *}
<SCRIPT language="JavaScript">
<!--
$(function(){
  $('.btn_wk').on('click', function(){
    $(this).toggleClass('active');

      if($(this).hasClass('active')){
        var text = $(this).data('text-clicked');
        $(this).text(text);
        $('[data-text-clicked]').css('color','orange');
        //$('[data-text-clicked]').css('background-color','orange');
      } else {
        var text = $(this).data('text-default');
        $(this).text(text);
        $('[data-text-default]').css('color','black');
        //$('[data-text-default]').css('background-color','green');
      }
      var text_kw = $(this).data('kw-seq');

      // Ajax通信を開始する
      $.ajax({
          url: '/client/top/watchlist_kw/',
          type: 'post', 					// getかpostを指定(デフォルトは前者)
          dataType: 'json', 				// 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
          data: { 							// 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
        	  chg_seq: text_kw,
              //kwseq : $('#kwseq').val()
          }
      })

      // ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
      .done(function (response) {
          $('#result').val('成功');
          $('#detail').val(response.data);
      })
      // ・サーバからステータスコード400以上が返ってきたとき
      // ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
      // ・通信に失敗したとき
      .fail(function () {
          $('#result').val('失敗');
          $('#detail').val('');
      });
  });
});

// ルートドメイン＆キーワード ウォッチリスト共存
$(function(){
  var sel = 0;

  // ** キーワード
  $(".btn_wd").on('click', function(){

    sel = 1;
    $(this).toggleClass('active');

      if($(this).hasClass('active')){
        var text = $(this).data('rd-clicked');
        $(this).text(text);
        $('[data-rd-clicked]').css('color','orange');
        //$('[data-rd-clicked]').css('background-color','green');
      } else {
        var text = $(this).data('rd-default');
        $(this).text(text);
        $('[data-rd-default]').css('color','black');
        //$('[data-rd-clicked]').css('background-color','green');
      }

      var text_rd = $(this).data('rd-seq');

      // Ajax通信を開始する
      $.ajax({
          url: '/client/top/watchlist_domain/',
          type: 'post', 					// getかpostを指定(デフォルトは前者)
          dataType: 'json', 				// 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
          data: { 							// 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
        	  chg_seq: text_rd,
              //kwseq : $('#kwseq').val()
          }
      })

      // ・ステータスコードは正常で、dataTypeで定義したようにパース出来たとき
      .done(function (response) {
          $('#result').val('成功');
          $('#detail').val(response.data);
      })
      // ・サーバからステータスコード400以上が返ってきたとき
      // ・ステータスコードは正常だが、dataTypeで定義したようにパース出来なかったとき
      // ・通信に失敗したとき
      .fail(function () {
          $('#result').val('失敗');
          $('#detail').val('');
      });
  });

  // ** ルートドメイン
  $("#kwMenu dt").on("click", function() {

    if (sel == 0) {
      $(this).removeClass("btn_wd");//除外部分

      $(this).next().slideToggle();
      $(this).toggleClass("active");//追加部分
    } else {
      sel = 0;
    }
  });

});
// -->
</SCRIPT>



<p>ランク表示期間：{$end_date} ～ {$start_date} (31日間)</p>
{form_open('/top/detail/' , 'name="detailForm" class="form-horizontal"')}


  <dl id="kwMenu">

  {$old_rootdomain = ""}
  {$cnt=0}

  {foreach from=$list item=kw name="cnt"}
  {if $old_rootdomain != $kw.kw_rootdomain}
    {$old_rootdomain = $kw.kw_rootdomain}
    </dd><dt style="font-size:16px;color:#000000;">　
     <label class="btn_wd {if $kw.wt_rd_seq!=""}active{/if}" data-rd-default="☆" data-rd-clicked="★" data-rd-seq={$kw.rd_seq}>{if $kw.wt_rd_seq}<font color="orange">★</font>{else}<font color="black">☆</font>{/if}</label>
     {$kw.kw_rootdomain}
    </dt><dd class="btn_w">





    <div class="row">
      <div class="col-md-3">
        <span class="label" style="background-color:#006400;">UP:{$up_cnt{$cnt}}</span><span class="label" style="background-color:#d2691e;">DW:{$down_cnt{$cnt}}</span>
        <span id="sparkpie{$cnt}">Loading..</span><br><small>{$pie_cont{$cnt}}</small>
      </div>
      <div class="col-md-3">
        <small>TOP3</small>
        <table border=0 frame="hsides">
          <tr><th width="200"><small>Keyword</small></th><th width="35"><small>Rank</small></th><th><small>+/-</small></th></tr>
          {for $loop=0 to 2}
            {if isset($ranking_top[$cnt][$loop].kw_seq)}
              {if (($ranking_top[$cnt][$loop].start_position>=1)&&($ranking_top[$cnt][$loop].start_position<=300))}
                <tr><td><small>・{$ranking_top[$cnt][$loop].kw_seq}:{$ranking_top[$cnt][$loop].kw_keyword}</small></td>
                <td align="center"><small>{$ranking_top[$cnt][$loop].start_position}</small></td>
                {if ($ranking_top[$cnt][$loop].chg_position<=300)&&($ranking_top[$cnt][$loop].chg_position>=-300)}
                  <td align="center"><small>{$ranking_top[$cnt][$loop].chg_position_fugo}</small></td>
                {else}
                  <td align="center"><small>-</small></td>
                {/if}
                </tr>
              {/if}
            {/if}
          {/for}
        </table>
      </div>
      <div class="col-md-3">
        <small>上昇キーワード (前日比)</small>
        <table border=0 frame="hsides">
          <tr><th width="200"><small>Keyword</small></th><th width="35"><small>Rank</small></th><th><small>+/-</small></th></tr>
          {for $loop=0 to 2}
            {if isset($ranking_up[$cnt][$loop].kw_seq)}
              {if $ranking_up[$cnt][$loop].chg_position>0}
                <tr><td><small>・{$ranking_up[$cnt][$loop].kw_seq}:{$ranking_up[$cnt][$loop].kw_keyword}</small></td>
                <td align="center"><small>{$ranking_up[$cnt][$loop].start_position}</small></td>
                  {if $ranking_up[$cnt][$loop].chg_position<=300}
                    <td align="center"><small>{$ranking_up[$cnt][$loop].chg_position_fugo}</small></td>
                  {else}
                    <td align="center"><small>-</small></td>
                  {/if}
                </tr>
              {/if}
            {/if}
          {/for}
        </table>
      </div>
      <div class="col-md-3">
        <small>下降キーワード (前日比)</small>
        <table border=0 frame="hsides">
          <tr><th width="200"><small>Keyword</small></th><th width="35"><small>Rank</small></th><th><small>+/-</small></th></tr>
          {for $loop=0 to 2}
            {if isset($ranking_down[$cnt][$loop].kw_seq)}
              {if $ranking_down[$cnt][$loop].chg_position<0}
                <tr><td><small>・{$ranking_down[$cnt][$loop].kw_seq}:{$ranking_down[$cnt][$loop].kw_keyword}</small></td>
                  {if $ranking_down[$cnt][$loop].start_position<=300}
                    <td align="center"><small>{$ranking_down[$cnt][$loop].start_position}</small></td>
                    <td align="center"><small>{$ranking_down[$cnt][$loop].chg_position_fugo}</small></td>
                  {else}
                    <td align="center"><small>-</small></td>
                    <td align="center"><small>-</small></td>
                  {/if}
                </tr>
              {/if}
            {/if}
          {/for}
        </table>
      </div>
    </div>

<script type="text/javascript">
  $("#sparkpie{$cnt}").sparkline([{$pie_data{$cnt}}], {
	    type: 'pie',
	    width: '80',
	    height: '80',
	    sliceColors: ['#3366cc','#109618','#ff9900','#808080'],
	    offset: 0
  });
</script>


    {$cnt=$cnt+1}






  {/if}

  <br>
  <div class="row">
    <div class="col-md-9">
      {if $kw.kw_status == "1"}<span class="label label-primary">有効</span>
      {elseif $kw.kw_status == "0"}<span class="label label-default">無効</span>
      {else}エラー
      {/if}
      {if $kw.kw_searchengine==0}<span class="label" style="background-color:#0000ff;">Google</span>{elseif $kw.kw_searchengine==1}<span class="label" style="background-color:#dc143c;">Yahoo!</span>{else}error{/if}
      ID:{$kw.kw_seq}
      <label class="btn_wk {if $kw.wt_seq}active{/if}" data-text-default="☆" data-text-clicked="★" data-kw-seq={$kw.kw_seq}>{if $kw.wt_seq}<font color="orange">★</font>{else}<font color="black">☆</font>{/if}</label>
      【{$kw.kw_keyword}】
      <button type="button" class="btn btn-success btn-xs" data-container="body" data-toggle="popover" data-placement="right"
        data-content="<b>【ルートドメイン】</b>{$kw.kw_rootdomain}<br>
                      <b>【URL一致方式】</b>{if $kw.kw_matchtype==0}完全一致{elseif $kw.kw_matchtype==1}前方一致{elseif $kw.kw_matchtype==2}ドメイン一致{elseif $kw.kw_matchtype==3}ルートドメイン一致{else}error{/if}<br>
                      <b>【デバイス】</b>{if $kw.kw_device==0}PC{elseif $kw.kw_device==1}Mobile{else}error{/if}<br>
                      <b>【ロケーション】</b>{$kw.kw_location_name}<br>
                      <b>【最大取得順位】</b>{if $kw.kw_maxposition==0}100件{elseif $kw.kw_maxposition==1}200件{elseif $kw.kw_maxposition==2}300件{else}error{/if}<br>
                      <b>【データ取得回数】</b>{if $kw.kw_trytimes==0}1回{elseif $kw.kw_trytimes==1}2回{elseif $kw.kw_trytimes==2}3回{else}error{/if}<br>
                      <b>【設定グループ】</b>{$kw.kw_group}<br>
                      <b>【設定タグ】</b>{$kw.kw_tag}<br>
                      <b>【メモ】</b>{if isset($me_list[$kw.kw_seq])}{foreach from=$me_list[$kw.kw_seq] item=me}<tbody><tr><td><br>{$me}</td></tr></tbody>{/foreach}{/if}<br>
                     ">
        設定情報
      </button>
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/detail/', 'POST', '{$kw.kw_seq}', 'chg_seq');">順位詳細</button>
      {*<button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/report/', 'POST', '{$kw.kw_seq}', 'chg_seq');">report</button>*}
      <button type="button" class="btn btn-success btn-xs" onclick="target='_blank', fmSubmit('detailForm', '/client/topdetail/report/', 'POST', '{$kw.kw_seq}', 'chg_seq');">report</button>
    </div>
    <div class="col-md-3 text-right">
      {if $mem_Kw==1}<button type="button" class="btn btn-success btn-xs" onclick="target='_blank', fmSubmit('detailForm', '/client/topdetail/chg/', 'POST', '{$kw.kw_seq}', 'chg_seq');">編&emsp;集</button>{/if}
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 textOverflow">
      <a href="{$kw.kw_url}" target="_blank">{$kw.kw_url}</a>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-11">

    <table class="table table-striped table-hover table-condensed">
      <thead>
        <tr>
          <th class="text-center">日付</th>
          {foreach from=$tbl_x_data{$kw.kw_seq} item=head}
            <th class="text-center">{$head}</th>
          {/foreach}
          <th></th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>rank</td>
          {$comp_y_data=0}
          {foreach from=$tbl_y_data{$kw.kw_seq} item=y_data}
            <td class="text-center">
              <small>{if ($y_data < $comp_y_data) && (is_numeric($y_data))}<font color="#ff0000">{$y_data}</font>{else}{$y_data}{/if}</small>
            </td>
            {$comp_y_data=$y_data}
          {/foreach}
        </tr>
      </tbody>
    </table>

    </div>
    <div class="col-md-1">
      <span id="sparkline{$kw.kw_seq}">Loading..</span>
    </div>
  </div>




<script type="text/javascript">
  $("#sparkline{$kw.kw_seq}").sparkline([{$y_min_data{$kw.kw_seq}}], {
	    type: 'line',
	    width: '70',
	    height: '60',
	    spotColor: '#',
	    chartRangeMin: 200,
	    chartRangeMax: 300
  });
</script>

  {foreachelse}
    検索結果はありませんでした。
  {/foreach}

</dl>

{form_close()}



{form_open('/data_csv/toplist_csvdown/' , 'name="detailForm" class="form-horizontal"')}

  <div class="col-md-12">
    {$attr['name'] = '_submit'}
    {$attr['type'] = 'submit'}
    {form_button($attr , '↓ 設定情報のダウンロード' , 'class="btn btn-warning btn-xs"')}
  </div>

{form_close()}




<ul class="pagination pagination-sm">
  {$set_pagination}
</ul>

</div>

<script type="text/javascript">
  $('.sparklines').sparkline('html');
</script>


{* POP-Over *}
<script src="{base_url()}../../js/my/popover.js"></script>

{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
