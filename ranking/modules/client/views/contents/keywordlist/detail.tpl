{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

  <script src="{base_url()}../../js/chart.min.js"></script>

{*
https://github.com/etimbo/jquery-print-preview-plugin
  <link rel="stylesheet" href="{base_url()}../../js/print/example/css/960.css" type="text/css" media="screen">
  <link rel="stylesheet" href="{base_url()}../../js/print/example/css/screen.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="{base_url()}../../js/print/example/css/print.css" type="text/css" media="print" />
  <script src="{base_url()}../../js/print/print-Preview.js"></script>

  <script src="{base_url()}../../js/print/printPreview.js"></script>
*}

<body>

{* ヘッダー部分　END *}

<H4><p class="bg-success">&emsp;&emsp;キーワード管理&emsp;詳細</p></H4>

{form_open('keywordlist/detailchk/' , 'name="detailForm" class="form-horizontal repeater"')}

  {$mess}

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
    <div class="col-sm-2">
      {$info.kw_group}
    </div>
  </div>
  <div class="form-group">
    <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-9">
      {$info.kw_tag}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_memo" class="col-xs-2 col-md-2 control-label">メ&emsp;&emsp;モ</label>
    <div class="col-md-9">
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


  <div class="form-horizontal col-sm-12">
    <div>
      <canvas id="RankingChart01" height="150" width="300" ></canvas>
    </div>
  </div>


 <p></p>

<div class="form-horizontal col-sm-12">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="text-center">date</th>
          {foreach from=$tbl_x_data{$info.kw_seq} item=head}
          <th class="text-center">{$head}</th>
          {/foreach}
        </tr>
      </thead>

      <tbody>
        <tr>
          <td>
            rank
          </td>
      {foreach from=$tbl_y_data{$info.kw_seq} item=y_data}
          <td class="text-right">
            {$y_data}
          </td>
      {/foreach}
        </tr>
      </tbody>

    </table>
</div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-2">
      {$attr['name'] = '_back'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '戻　　る' , 'class="btn btn-primary"')}
    </div>


{form_close()}

<!-- </form> -->

{* Graph *}
{include file="../../../../../public/js/my/kwlist_detail_graph.php"}


{if $smarty.session.c_memKw==1}

{form_open('/keywordlist/del_pw/' , 'name="reportForm" class="form-horizontal h-adr"')}

  {form_hidden('kw_seq', $info.kw_seq)}

  <!-- Button trigger modal -->
  <div class="col-sm-2 col-sm-offset-4">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal02">削　　除</button>
  </div>
  </div>

  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">キーワード　削除</h4>
        </div>
        <div class="modal-body">
          <p>削除しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
          <button type='submit' name='submit' value='cancel' class="btn btn-sm btn-primary">キャンセル</button>
          {*<button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>*}
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->


{form_close()}
<!-- </form> -->

{/if}




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
