{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<link href="{base_url()}../../js/select2/select2.css" rel="stylesheet">
<script src="{base_url()}../../js/select2/select2.min.js"></script>

<body>

{* ヘッダー部分　END *}

{form_open('grouplist/add_comp/' , 'name="detailForm" class="form-horizontal repeater"')}

  <div class="form-group">
    <label for="new_name" class="col-md-2 control-label">グループ名の追加</label>
    <div class="col-md-7">
      {form_input('new_name' , set_value('new_name', '') , 'class="form-control" placeholder="グループ名を入力してください。max.50文字"')}
      {if form_error('new_name')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('new_name')}</font></label>{/if}
    </div>
    <div class="col-sm-offset-1 col-sm-2">
      {$attr['name'] = '_add'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '追&emsp;&emsp;加' , 'class="btn btn-primary btn-xs"')}
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-md-7">メ&emsp;&emsp;モ<br>
      <textarea class="form-control input-sm" id="new_memo" name="new_memo" placeholder="max.1000文字">{$tmp_new_memo}</textarea>
      {if form_error('new_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('new_memo')}</font></label>{/if}
    </div>
  </div>

{form_close()}

<div class="row"><div class="col-md-offset-2 col-md-10">
  {$mess02}
</div></div>

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
