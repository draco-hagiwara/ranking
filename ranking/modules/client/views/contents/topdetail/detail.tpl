{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>

  <script src="{base_url()}../../js/my/fmsubmit.js"></script>

<body>

{* ヘッダー部分　END *}

{form_open('topdetail/detail/' , 'name="headerForm" class="form-horizontal repeater"')}
<div class="form-group">
  <div class="col-md-offset-9 col-md-3 text-right">
    {if $smarty.session.c_memKw==1}
      <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('headerForm', '/client/keywordlist/chg/', 'POST', '{$info.kw_seq}', 'chg_seq');">編　集</button>
    {/if}
    <button type="button" class="btn {if $wt_seq}btn-warning{else}btn-success{/if} btn-xs" onclick="fmSubmit('headerForm', '/client/top/watchlist/', 'POST', '{$info.kw_seq}', 'chg_seq');">★ウォッチ</button>
    <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('headerForm', '/client/topdetail/report/', 'POST', '{$info.kw_seq}', 'chg_seq');">report</button>
  </div>
</div>
{form_close()}
<!-- </form> -->

<div class="form-group">
  <H4 class="bg-success">&emsp;&emsp;キーワード順位情報&emsp;詳細</H4>
</div>

{form_open('topdetail/detail/' , 'name="detailForm" class="form-horizontal repeater"')}

  <br>{$mess}

  <div class="form-group">
    <div class="col-md-offset-1 col-md-2">
      {form_dropdown('gp_term', $options_term, set_value('gp_term', {$gp_term}))}

      {$attr['name']  = 'submit'}
      {$attr['type']  = 'submit'}
      {$attr['value'] = '_submit'}
      {form_button($attr , 'グラフ切替' , 'class="btn btn-primary btn-xs"')}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-1 col-md-9">ランク表示期間：{$end_date} ～ {$start_date}</div>
  </div>
  {form_hidden('chg_seq', $info.kw_seq)}




  {* グラフ表示領域 *}
  <div class="form-horizontal col-sm-12">
    <div>
      <canvas id="RankingChart01" height="150" width="300" ></canvas>
    </div>
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
    <div class="col-md-offset-2 col-md-9">■ 対象URL：{$info.kw_url}</div>
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
{include file="../../../../../public/js/my/topdetail_graph.php"}
{*
<script id="topdetail_graph" src="{base_url()}../../js/my/topdetail_graph.js"
    x_data = {$x_data{$info.kw_seq}}
    y_data = {$y_data{$info.kw_seq}}
></script>
*}


{form_open("{$back_page}/search/{$seach_page_no}/" , 'name="detailForm" class="form-horizontal"')}

  <div class="form-group">
    <div class="col-sm-offset-1 col-sm-1">
      <br><br>
      {$attr['name'] = '_back'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '戻&emsp;&emsp;る' , 'class="btn btn-primary  btn-sm"')}
    </div>
  </div>

{form_close()}

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
