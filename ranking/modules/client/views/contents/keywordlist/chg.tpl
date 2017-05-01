{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<link href="{base_url()}../../js/select2/select2.css" rel="stylesheet">
<script src="{base_url()}../../js/select2/select2.min.js"></script>

<body>

{* ヘッダー部分　END *}

<H4><p class="bg-success">&emsp;&emsp;キーワード管理&emsp;編集</p></H4>

{form_open('keywordlist/chg_conf/' , 'name="detailForm" class="form-horizontal repeater"')}

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
      {form_input('kw_url' , set_value('kw_url', $info.kw_url) , 'class="form-control" placeholder="対象URLを入力してください。max.510文字"')}
      {if form_error('kw_url')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_url')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ URLマッチタイプ<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
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
      {form_dropdown('kw_maxposition', $options_kw_maxposition, set_value('kw_maxposition', $info.kw_maxposition))}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 1日の取得回数<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {form_dropdown('kw_trytimes', $options_kw_trytimes, set_value('kw_trytimes', $info.kw_trytimes))}
    </div>
  </div>


  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
    <div class="col-md-9">
      <select name="kw_group" id="select2group" style="width: 500px;">
        {$options_group}
      </select>
    </div>
  </div>

<script type="text/javascript">
$(function() {
	$("#select2group").select2();
});
</script>

  <div class="form-group">
    <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-9">
      <select multiple="multiple" name="kw_tag[]" id="select2tag" style="width: 500px;">
        {$options_tag}
      </select>
    </div>
  </div>

<script type="text/javascript">
$(function() {
$("#select2tag").select2({
	tags: true,
});
});
</script>







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
            <br>{$me.me_create_date}
            <br>{$me.me_memo}
          </td>
        </tr>
      </tbody>
      {/foreach}
    </div>
  </div>


  {form_hidden('kw_keyword', $info.kw_keyword)}
  {form_hidden('kw_searchengine', $info.kw_searchengine)}
  {form_hidden('kw_device', $info.kw_device)}
  {form_hidden('kw_location_id', $info.kw_location_id)}
  {form_hidden('kw_location_name', $info.kw_location_name)}
  {form_hidden('kw_seq', $info.kw_seq)}
  {form_hidden('kw_cl_seq', $info.kw_cl_seq)}
  {form_hidden('kw_ac_seq', $info.kw_ac_seq)}



  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
      <button type="button" onclick="submit();">確&emsp;&emsp;認</button>
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
