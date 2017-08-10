{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<H3><p class="bg-success">　　アカウント情報　更新</p></H3>

{form_open('/accountlist/detailchk/' , 'name="accountDetailForm" class="form-horizontal"')}

  {$mess}
  {if $smarty.session.c_memType==1}
  <div class="form-group">
    <label for="ac_type" class="col-sm-3 control-label">アカウント種類選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('ac_type', $options_ac_type, set_value('ac_type', $info.ac_type))}
      {if form_error('ac_type')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_type')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_status" class="col-sm-3 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('ac_status', $options_ac_status, set_value('ac_status', $info.ac_status))}
      {if form_error('ac_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_status')}</font></label>{/if}
    </div>
  </div>
  {else}
    {form_hidden('ac_type', $info.ac_type)}
    {form_hidden('ac_status', $info.ac_status)}
  {/if}

  <div class="form-group">
    <label for="ac_department" class="col-sm-3 control-label">所属部署</label>
    <div class="col-sm-8">
      {form_input('ac_department' , set_value('ac_department', $info.ac_department) , 'class="form-control" placeholder="所属部署を入力してください。max.50文字"')}
      {if form_error('ac_department')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_department')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_name" class="col-sm-3 control-label">担当者<font color=red> *</font></label>
    <div class="col-sm-4">
      {form_input('ac_name01' , set_value('ac_name01', $info.ac_name01) , 'class="form-control" placeholder="担当者姓を入力してください。max.50文字"')}
      {if form_error('ac_name01')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_name01')}</font></label>{/if}
    </div>
    <div class="col-sm-4">
      {form_input('ac_name02' , set_value('ac_name02', $info.ac_name02) , 'class="form-control" placeholder="担当者名を入力してください。max.50文字"')}
      {if form_error('ac_name02')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_name02')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_id" class="col-sm-3 control-label">ログインID</label>
    <div class="col-sm-8">
      {$info.ac_id}
    </div>
  </div>

  {if $smarty.session.c_memSeq==$info.ac_seq}
  <div class="form-group">
    <label for="ac_pw" class="col-sm-3 control-label">パスワード</label>
    <div class="col-sm-8">
      {form_password('ac_pw' , set_value('ac_pw', '') , 'class="form-control" placeholder="パスワード　(半角英数字・記号：８文字以上)。max.50文字"')}
      <p class="redText"><small>※お客様のお名前や、生年月日、またはその他の個人情報など、推測されやすい情報は使用しないでください</small></p>
      {if form_error('ac_pw')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_pw')}</font></label>{/if}
    </div>
  </div>
  {/if}

  {form_hidden('ac_seq', $info.ac_seq)}
  {form_hidden('ac_id', $info.ac_id)}



  {if $smarty.session.c_memType==1}
  <div class="form-group">
    <label class="col-md-3 control-label">権限の付与</label>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-3">■&emsp;キーワード</div>
    <div class="col-md-offset-3 col-md-9">
      <label class="radio-target1">
        <input type="radio" name="ac_keyword" id="radio-keyword1" value="1" {if $info.ac_keyword==1}checked{/if}> 権限あり
      </label>
      <label class="radio-target2">&emsp;&emsp;
        <input type="radio" name="ac_keyword" id="radio-keyword0" value="0" {if $info.ac_keyword==0}checked{/if}> 権限なし
      </label>
      {if form_error('ac_keyword')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_keyword')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-3">■&emsp;グループ</div>
    <div class="col-md-offset-3 col-md-9">
      <label class="radio-target2">
        <input type="radio" name="ac_group" id="radio-group1" value="1" {if $info.ac_group==1}checked{/if}> 権限あり
      </label>
      <label class="radio-target2">&emsp;&emsp;
        <input type="radio" name="ac_group" id="radio-group0" value="0" {if $info.ac_group==0}checked{/if}> 権限なし
      </label>
      {if form_error('ac_group')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_group')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-3">■&emsp;タグ</div>
    <div class="col-md-offset-3 col-md-9">
      <label class="radio-target">
        <input type="radio" name="ac_tag" id="radio-_tag1" value="1" {if $info.ac_tag==1}checked{/if}> 権限あり
      </label>
      <label class="radio-target">&emsp;&emsp;
        <input type="radio" name="ac_tag" id="radio-_tag0" value="0" {if $info.ac_tag==0}checked{/if}> 権限なし
      </label>
      {if form_error('ac_tag')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_tag')}</font></label>{/if}
    </div>
  </div>
  {else}
  <div class="form-group">
    <label class="col-md-3 control-label">権限の付与</label>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-3">■&emsp;キーワード：{if $info.ac_keyword==1}権限あり{else}権限なし{/if}</div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-3">■&emsp;グループ&emsp;：{if $info.ac_group==1}権限あり{else}権限なし{/if}</div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-3 col-md-3">■&emsp;タ&emsp;&emsp;グ&emsp;：{if $info.ac_tag==1}権限あり{else}権限なし{/if}</div>
  </div>

  {form_hidden('ac_keyword', $info.ac_keyword)}
  {form_hidden('ac_group', $info.ac_group)}
  {form_hidden('ac_tag', $info.ac_tag)}

  {/if}

  <br><br>

  <!-- Button trigger modal -->
  <div class="row">
  <div class="col-sm-4 col-sm-offset-3">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">更新する</button>
  </div>
  </div>

  <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">アカウント情報　更新</h4>
        </div>
        <div class="modal-body">
          <p>更新しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='submit' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}
<!-- </form> -->

<br><br>
{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
