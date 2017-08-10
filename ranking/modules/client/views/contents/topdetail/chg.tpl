{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<link href="{base_url()}../../js/select2/select2.css" rel="stylesheet">
<script src="{base_url()}../../js/select2/select2.min.js"></script>

<script src="{base_url()}../../js/my/fmsubmit.js"></script>


<script>
$(function() {
  // 入力されたURLの存在有無をチェック
  $('#set_url').blur(function(e) {

    $.ajax({
        url: "/client/check_url/",
        type: 'POST',
        timeout : 10000,																// タイムアウト:10秒
        data: {
            check_url: $("#set_url").val(),
        },

        /**
         * Ajax通信が成功した場合に呼び出されるメソッド
         */
        success: function(data, dataType)
        {
            //successのブロック内は、Ajax通信が成功した場合に呼び出される
            console.log(data);

            //PHPから返ってきたデータの表示
            http_code = data.split("/");

            //console.log(http_code[0]);											// curl 実行時のステータスコード取得
            //console.log(http_code[1]);											// http ステータスコード取得

            // curl_code で判定
            // https://curl.haxx.se/libcurl/c/libcurl-errors.html
            if (http_code[0] != 0) {
            	if (http_code[0] == 6) {
            		alert("情報：入力された対象URLが見つかりません。\n　　　このままでよろしいですか？\n\ncurl_code : Couldn't resolve host. The given remote host was not resolved.");
            	} else {
            		alert("情報：入力された対象URLが見つかりません。\n　　　このままでよろしいですか？\n\ncurl_code : " + http_code[0]);
            	}
            }

            // http_code で判定
            if ((http_code[1] != 200) && (http_code[1] != 0)) {

            	if (http_code[1] == 301) {
            		alert("情報：入力された対象URLが見つかりません。\n　　　このままでよろしいですか？\n\n" + "301 Moved Permanently");
            	} else if (http_code[1] == 302) {
            		alert("情報：入力された対象URLが見つかりません。\n　　　このままでよろしいですか\n\n" + "302 Found");
            	} else if (http_code[1] == 404) {
            		alert("情報：入力された対象URLが見つかりません。\n　　　このままでよろしいですか？\n\n" + "404 Not Found");
            	} else if (http_code[1] == 500) {
            		alert("情報：入力された対象URLでエラーが発生しています。\n　　　このままでよろしいですか？\n\n" + "500 Internal Server Error");
            	} else {
            		alert("情報：入力された対象URLが見つかりません。\n　　　このままでよろしいですか？\n\nHTTPレスポンスコード : " + http_code[1]);
            	}

            }
        },
        /**
         * Ajax通信が失敗した場合に呼び出されるメソッド
         */
        error: function(XMLHttpRequest, textStatus, errorThrown)
        {
            //通常はここでtextStatusやerrorThrownの値を見て処理を切り分けるか、単純に通信に失敗した際の処理を記述します。

            //this;
            //thisは他のコールバック関数同様にAJAX通信時のオプションを示します。

            //エラーメッセージの表示
            alert('Error : ' + errorThrown + "\n順位チェックツールサーバとの通信異常が発生しています。");
        }
	});
  });
});
</script>


<body>

{* ヘッダー部分　END *}

<H4><p class="bg-success">&emsp;&emsp;キーワード管理&emsp;編集</p></H4>

{form_open('topdetail/chg_chk/' , 'name="detailForm" class="form-horizontal repeater"')}

  {$mess}
  <div class="form-group">
    <label for="kw_status" class="col-sm-2 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('kw_status', $options_kw_status, set_value('kw_status', $info.kw_status))}
      {if form_error('kw_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_status')}</font></label>{/if}
    </div>
  </div>

  <div class="form-group">
    <label class="col-xs-2 col-md-2 control-label">対象キーワード設定</label>
  </div>

  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 検索キーワード<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {$info.kw_keyword}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 対象URL<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_status == 1}
        {form_input('kw_url' , set_value('kw_url', $info.kw_url) , 'id="set_url" class="form-control" placeholder="日本語URLの場合はエンコードしてから入力してください。http(s)://～ max.510文字。"')}
        ※URLを変更する場合、旧URLの順位データを引き継ぎます。<br>
        {if form_error('kw_url')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_url')}</font></label>{/if}
      {else}
        {$info.kw_url}{form_hidden('kw_url', $info.kw_url)}
      {/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ URLマッチタイプ<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_status == 1}
        <label class="radio-match">
          <input type="radio" name="kw_matchtype" id="radio-match0" value="0" {if $info.kw_matchtype==0}checked{/if}> 完全一致
        </label>
        <label class="radio-match">&emsp;&emsp;
          <input type="radio" name="kw_matchtype" id="radio-match1" value="1" {if $info.kw_matchtype==1}checked{/if}> 前方一致
        </label>
        <label class="radio-match">&emsp;&emsp;
          <input type="radio" name="kw_matchtype" id="radio-match2" value="2" {if $info.kw_matchtype==2}checked{/if}> ドメイン一致
        </label>
        <label class="radio-match">&emsp;&emsp;
          <input type="radio" name="kw_matchtype" id="radio-match3" value="3" {if $info.kw_matchtype==3}checked{/if}> ルートドメイン一致 (サブドメイン含む)
        </label>
        <br>{if form_error('kw_matchtype')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_matchtype')}</font></label>{/if}
      {else}
        {if $info.kw_matchtype==0}完全一致{elseif $info.kw_matchtype==1}前方一致{elseif $info.kw_matchtype==2}ドメイン一致{elseif $info.kw_matchtype==3}ルートドメイン一致{else}{/if}
        {form_hidden('kw_matchtype', $info.kw_matchtype)}
      {/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 検索エンジン選択<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_searchengine==0}Google{else}Yahoo!{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 取得対象デバイス<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_device==0}ＰＣ版{else}モバイル版{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ ロケーション指定<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {$info.kw_location_name}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 最大取得順位<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_status == 1}
        {form_dropdown('kw_maxposition', $options_kw_maxposition, set_value('kw_maxposition', $info.kw_maxposition))}
      {else}
        {if $info.kw_maxposition==0}100件{elseif $info.kw_maxposition==1}200件{elseif $info.kw_maxposition==2}300件{else}{/if}
        {form_hidden('kw_maxposition', $info.kw_maxposition)}
      {/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 1日の取得回数<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_status == 1}
        {form_dropdown('kw_trytimes', $options_kw_trytimes, set_value('kw_trytimes', $info.kw_trytimes))}
      {else}
        {if $info.kw_trytimes==0}1回{elseif $info.kw_trytimes==1}2回{elseif $info.kw_trytimes==2}3回{else}{/if}
        {form_hidden('kw_trytimes', $info.kw_trytimes)}
      {/if}
    </div>
  </div>

  {* Group ＆ Tag設定 *}
  {if $info.kw_status == 1}
    {include file="../../../../../public/js/my/kwlist_chg_grouptag.php"}
  {else}
    <div class="form-group">
      <label for="kw_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
      <div class="col-md-9">
        {$info.kw_group}{form_hidden('kw_group[0]', $info.kw_group)}
      </div>
    </div>
    <div class="form-group">
      <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
      <div class="col-md-9">
        {$info.kw_tag}{form_hidden('kw_tag', $info.kw_tag)}
      </div>
    </div>
  {/if}

  <div class="form-group">
    <label for="kw_memo" class="col-xs-2 col-md-2 control-label">メ&emsp;&emsp;モ</label>
    <div class="col-md-9">
      <textarea class="form-control input-sm" id="kw_memo" name="kw_memo" placeholder="max.1000文字">{$tmp_memo}</textarea>
      {if form_error('kw_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_memo')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">
      {foreach from=$info_me item=me}
      <tbody>
        <tr>
          <td>
            <br>-- {$me.me_create_date} --&emsp;
            <button type="button" class="btn btn-success btn-xs" onclick="fmSubmit('detailForm', '/client/topdetail/chg/', 'POST', '{$me.me_seq}', 'chg_seq');">メモ削除</button>
            <br>{$me.me_memo}<hr>
          </td>
        </tr>
      </tbody>
      {/foreach}
    </div>
  </div>

  {if $info.kw_status == 1}
  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">他キーワードへの反映</label>
    <div class="col-sm-10">
      「最大取得順位」「1日の取得回数」「グループ設定」「タグ設定」を同一ドメイン内に反映させたい場合は以下を選択してください。
      {form_dropdown('reflection', $options_reflection, set_value('reflection', ''))}
    </div>
  </div>
  {/if}

  {form_hidden('kw_keyword', $info.kw_keyword)}
  {form_hidden('kw_searchengine', $info.kw_searchengine)}
  {form_hidden('kw_device', $info.kw_device)}
  {form_hidden('kw_location_id', $info.kw_location_id)}
  {form_hidden('kw_location_name', $info.kw_location_name)}
  {form_hidden('kw_seq', $info.kw_seq)}
  {form_hidden('kw_cl_seq', $info.kw_cl_seq)}
  {form_hidden('kw_ac_seq', $info.kw_ac_seq)}

  <br>

  <!-- Button trigger -->
  {form_hidden('back_page', $back_page)}
  {form_hidden('seach_page_no', $seach_page_no)}
  <div class="row">
    <div class="col-sm-2 col-sm-offset-2">
      {$attr['name'] = '_submit'}
      {$attr['value'] = '_back'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '一覧へ戻る' , 'class="btn btn-primary btn-sm"')}
    </div>
  <!-- / -->

{if $smarty.session.c_memKw==1}
  <!-- Button trigger modal -->
    <div class="col-sm-offset-4 col-sm-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">更&emsp;&emsp;新</button>
    </div>

    <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">キーワード管理　編集</h4>
          </div>
          <div class="modal-body">
            <p>この内容で、更新しますか。&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">O  K</button>
            <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
{else}
  </div>
{/if}

{form_close()}

<!-- </form> -->

{if $smarty.session.c_memKw==1}
{form_open('/topdetail/del_pw/' , 'name="reportForm" class="form-horizontal h-adr"')}

  {form_hidden('kw_seq', $info.kw_seq)}

  <!-- Button trigger modal -->
  <div class="col-sm-offset-1 col-sm-2">
    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal02">削&emsp;&emsp;除</button>
  </div>
  </div>

  <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">キーワード&emsp;削除</h4>
        </div>
        <div class="modal-body">
          <p>このキーワード情報を削除しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='submit' class="btn btn-sm btn-primary">O&emsp;&emsp;K</button>
          <button type='submit' name='submit' value='cancel' class="btn btn-sm btn-primary">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}
<!-- </form> -->

{else}
  </div>
{/if}



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
