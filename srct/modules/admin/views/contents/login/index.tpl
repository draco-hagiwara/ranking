{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>
{* ヘッダー部分　END *}

<div class="jumbotron">
  <h3>ログイン画面　　<span class="label label-danger">アドミン</span></h3>
</div>

{form_open('/login/check/' , 'name="LoginForm" class="form-horizontal"')}

  <div class="form-group">
    <div class=" col-sm-offset-1 col-sm-11">
      {if $err_mess !=''}<span class="label label-danger">Error : </span><label><font color=red>{$err_mess}</font></label><br><br>{/if}
    </div>
    <div class=" col-sm-offset-1 col-sm-6 col-sm-offset-5">
      <label for="ac_id">ログインID　（メールアドレス）</label>
      {form_input('ac_id' , set_value('ac_id', '') , 'class="form-control" placeholder="ログインID（メールアドレス）を入力してください。"')}
      {if form_error('ac_id')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_id')}</font></label>{/if}
  </div>
  </div>
  <div class="form-group">
    <div class=" col-sm-offset-1 col-sm-6 col-sm-offset-5">
      <label for="ac_pw">パスワード</label>
      {form_password('ac_pw' , '' , 'class="form-control" placeholder="パスワードを入力してください。"')}
      {if form_error('ac_pw')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('ac_pw')}</font></label>{/if}
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
