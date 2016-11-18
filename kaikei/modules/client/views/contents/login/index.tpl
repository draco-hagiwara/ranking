{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<div class="jumbotron">
  <h3><span class="label label-info">請求書発行システム</span>　　ログイン画面</h3>
</div>

{form_open('/login/check/' , 'name="LoginForm" class="form-horizontal"')}


  <div class="form-group">
    <div class=" col-sm-offset-1 col-sm-6 col-sm-offset-5">
      {if $err_mess !=''}<span class="label label-danger">Error : </span><label><font color=red>{$err_mess}</font></label>{/if}
    </div>
    <div class=" col-sm-offset-1 col-sm-6 col-sm-offset-5">
      <label for="cl_id">ログインID</label>
      {form_input('cl_id' , set_value('cl_id', '') , 'class="form-control" placeholder="ログインIDを入力してください。"')}
      {if form_error('cl_id')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_id')}</font></label>{/if}
  </div>
  </div>
  <div class="form-group">
    <div class=" col-sm-offset-1 col-sm-6 col-sm-offset-5">
      <label for="cl_pw">パスワード</label>
      {form_password('cl_pw' , '' , 'class="form-control" placeholder="パスワードを入力してください。"')}
      {if form_error('cl_pw')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('cl_pw')}</font></label>{/if}
  </div>
  </div>

  <div class="form-group">
    <div class=" col-sm-offset-1 col-sm-6 col-sm-offset-5">
      {$attr['name'] = 'submit'}
      {$attr['type'] = 'submit'}
      {form_button($attr , 'ログイン' , 'class="btn btn-default"')}
    </div>
  </div>

{form_close()}


{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    <!-- Bootstrapのグリッドシステムclass="row"で終了 -->
    </div>
  </section>
</div>

</body>
</html>
