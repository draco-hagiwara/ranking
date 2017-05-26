{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<link href="{base_url()}../../js/select2/select2.css" rel="stylesheet">
<script src="{base_url()}../../js/select2/select2.min.js"></script>

{*
<link href="{base_url()}../../js/redmond/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
<script src="{base_url()}../../js/redmond/jquery-ui-1.10.4.custom.min.js"></script>
<script src="{base_url()}../../js/repeater/jquery.repeater.min.js"></script>
*}

<body>

{* ヘッダー部分　END *}

<H4><p class="bg-success">&emsp;&emsp;キーワード管理&emsp;新規登録</p></H4>

{form_open('keywordlist/addchk/' , 'name="accountForm" class="form-horizontal repeater"')}

  {$mess}
  <div class="form-group">
    <label class="col-xs-2 col-md-2 control-label">対象キーワード設定</label>
    <div class="col-md-offset-2 col-md-9">■ 検索キーワード<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      <select multiple="multiple" name="kw_keyword[]" id="select2keyword" style="width: 600px;">
        {$options_kw}
      </select>
      {if form_error('kw_keyword[]')}<span class="label label-danger"><br>Error : </span><label><font color=red>{form_error('kw_keyword[]')}</font></label>{/if}
    </div>
    <div class="col-md-offset-2 col-md-9">
      <small>※複数指定が可能。キーワード入力後、確定するにはENTERキーを押下してください。</small>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 対象URL<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {form_input('kw_url' , set_value('kw_url', '') , 'class="form-control" placeholder="対象URLを入力してください。max.510文字。http://～"')}
      {*form_input('kw_url' , set_value('kw_url', '') , 'id="id_url" class="form-control" placeholder="対象URLを入力してください。max.510文字。http://～"')*}
      {*form_input('kw_url' , set_value('kw_url', '') , 'id="id_urlBox" class="form-control" placeholder="対象URLを入力してください。max.510文字。http://～"')*}
      {if form_error('kw_url')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_url')}</font></label>{/if}
    </div>
  </div>





<script>
$(function() {
  // 入力されたURLの存在有無をチェック
  $('#id_url').blur(function(e) {
    console.log($('#id_url').val());

    var in_url = $("#id_url").val();
    console.log(in_url);

    $.ajax({
      url: in_url,
      type:'GET',
      dataType: 'jsonp',
    }).done(function(data) {
      alert("ok");
    })
    .fail(function(jqXHR, textStatus, errorThrown) {
      $("#XMLHttpRequest").html("XMLHttpRequest : " + jqXHR.status);
      $("#textStatus").html("textStatus : " + textStatus);
      $("#errorThrown").html("errorThrown : " + errorThrown);
    })
    .always(function() {
      alert("finishi");
    })

  });
});
</script>

<script>
$(function() {
  // 入力されたURLの存在有無をチェック
  $('#id_urlBox').blur(function(e) {
    console.log($('#id_urlBox').val());

    var in_url = $("#id_urlBox").val();
    //var in_url = "Origin: " + $("#id_urlBox").val();
    console.log(in_url);

    $.ajax({
      url: in_url,
      type: 'GET',
      //type: 'PATCH',
      //dataType: 'json',
      //headers: { 'X-Greeting': 'hello, world' },
      success: function(data){
        console.log(data);
        alert('成功');
      },
      error: function(){
    	  alert('URLが存在しません');
      }
    });
  });
});
</script>







  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ URLマッチタイプ<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      <label class="radio-match">
        <input type="radio" name="kw_matchtype" id="radio-match0" value="0" {if $url_match==0}checked{/if}> 完全一致
      </label>
      <label class="radio-match">&emsp;&emsp;
        <input type="radio" name="kw_matchtype" id="radio-match1" value="1" {if $url_match==1}checked{/if}> 前方一致
      </label>
      <label class="radio-match">&emsp;&emsp;
        <input type="radio" name="kw_matchtype" id="radio-match2" value="2" {if $url_match==2}checked{/if}> ドメイン一致
      </label>
      <label class="radio-match">&emsp;&emsp;
        <input type="radio" name="kw_matchtype" id="radio-match3" value="3" {if $url_match==3}checked{/if}> ルートドメイン一致 (サブドメイン含む)
      </label>
      <br>{if form_error('kw_matchtype')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_matchtype')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 検索エンジン選択<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      <label>{form_checkbox('chkengine[]','0',set_checkbox('chkengine[]','0'))} google.co.jp</label>
      &emsp;&emsp;<label>{form_checkbox('chkengine[]','1',set_checkbox('chkengine[]','1'))} yahoo.co.jp</label>
      <br>{if form_error('chkengine[]')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('chkengine[]')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 取得対象デバイス<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      <label>{form_checkbox('chkdevice[]','0',set_checkbox('chkdevice[]','0'))} ＰＣ版</label>
      &emsp;&emsp;<label>{form_checkbox('chkdevice[]','1',set_checkbox('chkdevice[]','1'))} モバイル版</label>
      <br>{if form_error('chkdevice[]')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('chkdevice[]')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ ロケーション指定<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-7">
      <select multiple="multiple" name="kw_location[]" id="select2location" style="width: 600px;">
        {$options_location}
      </select>
      {if form_error('kw_location[]')}<span class="label label-danger"><br>Error : </span><label><font color=red>{form_error('kw_location[]')}</font></label>{/if}
    </div>
    <div class="col-md-offset-2 col-md-9">
      <small>※複数指定が可能。</small>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 最大取得順位<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {form_dropdown('kw_maxposition', $options_kw_maxposition, set_value('kw_maxposition', ''))}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 1日の取得回数<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {form_dropdown('kw_trytimes', $options_kw_trytimes, set_value('kw_trytimes', ''))}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
    <div class="col-md-9">
      <select multiple="multiple" name="kw_group[]" id="select2group" style="width: 500px;">
        {$options_group}
      </select>
    </div>
  </div>

  <div class="form-group">
    <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-9">
      <select multiple="multiple" name="kw_tag[]" id="select2tag" style="width: 500px;">
        {$options_tag}
      </select>
    </div>
  </div>

{*
  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
    <div class="col-md-9">
      <select name="kw_group" id="select2group" style="width: 500px;">
        {$options_group}
      </select>
    </div>
  </div>
  <div class="form-group">
    <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-9">
      <select multiple="multiple" name="kw_tag[]" id="select2tag" style="width: 500px;">
        {$options_tag}
      </select>
    </div>
    <div class="col-md-offset-2 col-md-9">
      <small>※複数指定が可能。</small>
    </div>
  </div>
*}

  <div class="form-group">
    <label for="kw_memo" class="col-xs-2 col-md-2 control-label">メ&emsp;&emsp;モ</label>
    <div class="col-md-9">
      <textarea class="form-control input-sm" id="kw_memo" name="kw_memo" placeholder="max.1000文字">{$tmp_memo}</textarea>
      {if form_error('kw_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('kw_memo')}</font></label>{/if}
    </div>
  </div>


  {*form_hidden('pj_cm_seq', $pj_cm_seq)*}

  <br>
  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-2">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">登&emsp;&emsp;録</button>
    </div>
  </div>

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">案件情報　登録</h4>
        </div>
        <div class="modal-body">
          <p>登録しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}

<!-- </form> -->


<script src="{base_url()}../../js/my/kwlist_select2.js"></script>

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
