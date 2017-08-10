{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script type="text/javascript" src="{base_url()}../../js/jqPlot/jquery.jqplot.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.cursor.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.dateAxisRenderer.min.js"></script>
  <script type="text/javascript" src="{base_url()}../../js/jqPlot/plugins/jqplot.highlighter.min.js"></script>

  <script src="{base_url()}../../js/my/fmsubmit.js"></script>

  <link rel="stylesheet" href="{base_url()}../../js/jqPlot/jquery.jqplot.min.css" type="text/css" media="screen">
  <link rel="stylesheet" href="{base_url()}../../css/my/print.css" type="text/css" media="print" />

<SCRIPT language="JavaScript">
<!--
$(function(){
  $('.btn_wk').on('click', function(){
    $(this).toggleClass('active');

      if($(this).hasClass('active')){
        var text = $(this).data('text-clicked');
        $(this).text(text);
        //$('[data-text-clicked]').css('color','orange');
        $('[data-text-clicked]').css('background-color','#f0ad4e');
      } else {
        var text = $(this).data('text-default');
        $(this).text(text);
        //$('[data-text-default]').css('color','black');
        $('[data-text-default]').css('background-color','#5cb85c');
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
// -->
</SCRIPT>

<body>

{* ヘッダー部分　END *}

{form_open("{$back_page}/search/{$seach_page_no}/" , 'name="detailForm" class="form-horizontal"')}

<div class="form-group noprint">
  <div class="col-md-1">
    {$attr['name'] = '_back'}
    {$attr['type'] = 'submit'}
    {form_button($attr , '戻&emsp;&emsp;る' , 'class="btn btn-success btn-xs"')}
  </div>

{form_close()}

{form_open('topdetail/detail/' , 'name="headerForm" class="form-horizontal"')}
  <div class="col-md-1 text-right">
    {* 印刷設定で「余白：最小」「オプション > ヘッダーとフッター：チェックを外す」 *}
    <input type="button" value="印刷する" class="btn btn-success btn-xs" onclick="window.print();" />
  </div>
  <div class="col-md-offset-7 col-md-3 text-right">
    {*<button type="button" class="btn {if $wt_seq}btn-warning{else}btn-success{/if} btn-xs" onclick="fmSubmit('headerForm', '/client/top/watchlist/', 'POST', '{$info.kw_seq}', 'chg_seq');">★ウォッチ</button>*}
    {*<button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('headerForm', '/client/topdetail/report/', 'POST', '{$info.kw_seq}', 'chg_seq');">report</button>*}
    <button type="button" class="btn btn-success btn-xs btn_wk {if $wt_seq}active{/if}" data-text-default="☆ウォッチ" data-text-clicked="★ウォッチ" data-kw-seq={$info.kw_seq} {if $wt_seq}style="background-color:orange;"{/if}>{if $wt_seq}★ウォッチ{else}☆ウォッチ{/if}</button>
    {if $smarty.session.c_memKw==1}
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('headerForm', '/client/topdetail/chg/', 'POST', '{$info.kw_seq}', 'chg_seq');">編&emsp;集</button>
    {/if}
  </div>
</div>
{form_close()}
<!-- </form> -->

<div class="form-group">
  <H4 class="bg-success">&emsp;&emsp;キーワード順位情報&emsp;詳細</H4>
</div>

{form_open('topdetail/detail/' , 'name="detailForm" class="form-horizontal repeater"')}

  {form_hidden('chg_seq', $info.kw_seq)}

  {* グラフ表示領域 *}
  <div class="form-horizontal col-sm-12">
    <div id="jqPlot-targetPlot" style="height: 500px; width: 1000px;"></div>
    <div id="jqPlot-controllerPlot" style="height: 100px; width: 1000px;"></div>
  </div>



  <div class="form-group col-md-12"><hr></div>


  <div class="form-group">
    <label class="col-xs-2 col-md-2 control-label">対象キーワード設定情報</label>
  </div>

  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ ステータス：{if $info.kw_status==0}無効{else}有効{/if}</div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 検索キーワード：{$info.kw_keyword}</div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 対象URL：<a href='{$info.kw_url|unescape:"url"}' target="_blank">{$info.kw_url|unescape:"url"}</a></div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ URLマッチタイプ：
      {if $info.kw_matchtype==0}完全一致{/if}
      {if $info.kw_matchtype==1}前方一致{/if}
      {if $info.kw_matchtype==2}ドメイン一致{/if}
      {if $info.kw_matchtype==3}ルートドメイン一致 (サブドメイン含む){/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 検索エンジン選択：{if $info.kw_searchengine==0}Google{else}Yahoo!{/if}</div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 取得対象デバイス：{if $info.kw_device==0}ＰＣ版{else}モバイル版{/if}</div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ ロケーション指定：{$info.kw_location_name}</div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 最大取得順位：
      {if $info.kw_maxposition==0}100件{/if}
      {if $info.kw_maxposition==1}200件{/if}
      {if $info.kw_maxposition==2}300件{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 1日の取得回数：
      {if $info.kw_trytimes==0}1回{/if}
      {if $info.kw_trytimes==1}2回{/if}
      {if $info.kw_trytimes==2}3回{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
    <div class="col-sm-10">
      {$info.kw_group}
    </div>
  </div>
  <div class="form-group">
    <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-10">
      {$info.kw_tag}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_memo" class="col-xs-2 col-md-2 control-label">メ&emsp;&emsp;モ</label>
    <div class="col-md-10">
      {foreach from=$info_me item=me}
      <tbody>
        <tr>
          <td>
            <br>{$me.me_create_date}
            <br>{$me.me_memo}
          </td>
        </tr>
      </tbody>
      {/foreach}
    </div>
  </div>

{form_close()}
<!-- </form> -->

{* Graph *}
{*include file="../../../../../public/js/my/topdetail_graph.php"*}



<script>
jQuery( function() {
	plot_data = {$plot_data{$info.kw_searchengine}}
    //sampleData = [ [ '2012-01-01 0:00AM', 65 ], [ '2012-02-01 0:00AM', 96 ], [ '2012-03-01 0:00AM', 74 ], [ '2012-04-01 0:00AM', 63 ], [ '2012-05-01 0:00AM', 85 ], [ '2012-06-01 0:00AM', 90 ] ]
    targetPlot = jQuery . jqplot(
        'jqPlot-targetPlot',
        [
        	plot_data
        ],
        {
            axes: {
                xaxis: {
                    renderer: jQuery . jqplot . DateAxisRenderer,
                    min: "{$plot_start_date} 0:00AM",
                    max: "{$plot_end_date} 0:01AM",
                    //min: '2017-05-03 0:00AM',
                    //max: '2017-06-01 0:00AM',
                    //tickInterval: '1 days',
                    tickOptions: {
                        formatString: '%m/%d'
                        //formatString: '%D'
                    },
                    pad: 0,
                    //ticks: 10,
                },
                yaxis:{
                    min: 300,
                    max: 1,
                    pad: 10,
                    ticks: [ '300', '250', '200', '150', '100', '50', '1' ],
                    //ticks: [ '300', '275', '250', '225', '200', '175', '150', '125', '100', '75', '50', '25', '1' ],
                    tickOptions: {
                        formatString: '%g'
                    },
                },
            },
            cursor: {
                show: true,
                showTooltip: false,
                zoom: true,
            },
            highlighter:{
                show: true,
                sizeAdjust: 7.5 // ハイライト時の円の大きさ
            },
            //seriesDefaults: {
            //	showMarker: false,
            //    fill: true,
            //	fillAlpha: 0.5,
            //},
        }
    );
    controllerPlot = jQuery . jqplot(
        'jqPlot-controllerPlot',
        [
        	plot_data
        ],
        {
            seriesDefaults: {
                showMarker: false
            },
            axes: {
                xaxis: {
                    renderer: jQuery . jqplot . DateAxisRenderer,
                    min: "{$plot_start_date} 0:00AM",
                    max: "{$plot_end_date} 0:01AM",
                    //min: '2017-05-03 0:00AM',
                    //max: '2017-06-01 0:00AM',
                    tickOptions: {
                        formatString: '%m/%d'
                    },
                },
                yaxis: {
                    min: 300,
                    max: 1,
                }
            },
            cursor: {
                show: true,
                showTooltip: false,
                zoom: true,
                constrainZoomTo: 'x'
            }
        }
    );
    jQuery . jqplot . Cursor . zoomProxy( targetPlot, controllerPlot );
} );
</script>







<!-- </form> -->

<br><br>

{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
