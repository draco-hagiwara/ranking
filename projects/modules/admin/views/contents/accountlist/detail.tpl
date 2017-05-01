{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<div class="jumbotron">
  <h3>アカウント情報　　<span class="label label-success">更新</span></h3>
</div>

{form_open('/accountlist/detailchk/' , 'name="accountDetailForm" class="form-horizontal"')}

  {$mess}
  {if $smarty.session.a_memSeq==1}
  <div class="form-group">
    <label for="ac_type" class="col-sm-4 control-label">アカウント種類選択<font color=red>【必須】</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('ac_type', $options_ac_type, set_value('ac_type', $info.ac_type))}
      {if form_error('ac_type')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_type')}</font></label>{/if}
    </div>
  </div>
  {else}
    {form_hidden('ac_type', $info.ac_type)}
  {/if}
  {if $smarty.session.a_memType==2}
  <div class="form-group">
    <label for="ac_status" class="col-sm-4 control-label">ステータス選択<font color=red>【必須】</font></label>
    <div class="col-sm-2 btn-lg">
      {form_dropdown('ac_status', $options_ac_status, set_value('ac_status', $info.ac_status))}
      {if form_error('ac_status')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_status')}</font></label>{/if}
    </div>
  </div>
  {else}
    {form_hidden('ac_status', $info.ac_status)}
  {/if}
  <div class="form-group">
    <label for="ac_department" class="col-sm-4 control-label">所属部署</label>
    <div class="col-sm-8">
      {form_input('ac_department' , set_value('ac_department', $info.ac_department) , 'class="form-control" placeholder="所属部署を入力してください。max.50文字"')}
      {if form_error('ac_department')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_department')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_name" class="col-sm-4 control-label">担当者<font color=red>【必須】</font></label>
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
    <label for="ac_id" class="col-sm-4 control-label">ログインID<font color=red>【必須】</font></label>
    <div class="col-sm-8">
      {$info.ac_id}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_mail" class="col-sm-4 control-label">メールアドレス<font color=red>【必須】</font></label>
    <div class="col-sm-8">
      {form_input('ac_mail' , set_value('ac_mail', $info.ac_mail) , 'class="col-sm-4 form-control" placeholder="メールアドレスを入力してください。max.100文字"')}
      {if form_error('ac_mail')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_mail')}</font></label>{/if}
      {if $err_email==TRUE}<span class="label label-danger">Error : </span><label><font color=red>「メールアドレス」欄で入力したアドレスは既に他で使用されています。再度他のアドレスを入力してください。</font></label>{/if}
    </div>
  </div>

  {if $smarty.session.a_memSeq==$info.ac_seq}
  <div class="form-group">
    <label for="ac_pw" class="col-sm-4 control-label">パスワード<font color=red>【必須】</font></label>
    <div class="col-sm-8">
      {form_password('ac_pw' , set_value('ac_pw', '') , 'class="form-control" placeholder="パスワード　(半角英数字・記号：８文字以上)。max.50文字"')}
      <p class="redText"><small>※お客様のお名前や、生年月日、またはその他の個人情報など、推測されやすい情報は使用しないでください</small></p>
      {if form_error('ac_pw')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_pw')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="retype_password" class="col-sm-4 control-label">パスワード再入力<font color=red>【必須】</font></label>
    <div class="col-sm-8">
      {form_password('retype_password' , set_value('retype_password', '') , 'class="form-control" placeholder="パスワード再入力　(半角英数字・記号：８文字以上)"')}
      <p><small>確認のため、もう一度入力してください。</small></p>
      {if form_error('retype_password')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('retype_password')}</font></label>{/if}
      {if $err_passwd==TRUE}<span class="label label-danger">Error : </span><label><font color=red>「パスワード」欄で入力した文字と違います。再度入力してください。</font></label>{/if}
    </div>
  </div>
  {/if}

  <div class="form-group">
    <label for="ac_tel" class="col-sm-4 control-label">担当者電話番号</label>
    <div class="col-sm-8">
      {form_input('ac_tel' , set_value('ac_tel', $info.ac_tel) , 'class="form-control" placeholder="担当者電話番号を入力してください"')}
      {if form_error('ac_tel')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_tel')}</font></label>{/if}
    </div>
  </div>
  <div class="form-group">
    <label for="ac_mobile" class="col-sm-4 control-label">担当者携帯番号</label>
    <div class="col-sm-8">
      {form_input('ac_mobile' , set_value('ac_mobile', $info.ac_mobile) , 'class="form-control" placeholder="担当者携帯番号を入力してください"')}
      {if form_error('ac_mobile')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_mobile')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('ac_seq', $info.ac_seq)}
  {form_hidden('ac_id', $info.ac_id)}

  <!-- Button trigger modal -->
  <div class="row">
  <div class="col-sm-4 col-sm-offset-4">
    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">更新する</button>
    {if $info.ac_status==0}<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">メール再発行</button>{/if}
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
  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">アカウント情報　メール再発行</h4>
        </div>
        <div class="modal-body">
          <p>メールを再発行しますか。&hellip;</p>
        </div>
        <div class="modal-footer">
          <button type='submit' name='submit' value='re_mail' class="btn btn-sm btn-primary">O  K</button>
          <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

{form_close()}
<!-- </form> -->

<br><br>
{* フッター部分　START *}
    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

{include file="../footer.tpl"}
{* フッター部分　END *}

</body>
</html>
