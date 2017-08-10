{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<link href="{base_url()}../../js/select2/select2.css" rel="stylesheet">
<script src="{base_url()}../../js/select2/select2.min.js"></script>

<body>

{* ヘッダー部分　END *}

{form_open('grouplist/chg/' , 'name="detailForm" class="form-horizontal repeater"')}

  {if $disp01==FALSE}
  <div class="form-group">
    <label for="gt_name" class="col-md-2 control-label">グループ名の更新</label>
    <div class="col-md-8">
      <select name="gt_name" id="select2grp" style="width: 650px;">
        {$options_group}
      </select>
    </div>
    <div class="col-sm-2">
      {$attr['name'] = '_select'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '選&emsp;&emsp;択' , 'class="btn btn-primary btn-xs"')}
    </div>
  </div>
  {/if}

<script type="text/javascript">
$(function() {
	$("#select2grp").select2();
});
</script>


{form_close()}


{if $disp01==TRUE}
{form_open('grouplist/chg_comp/' , 'name="detailForm" class="form-horizontal repeater"')}

  {if $disp01==TRUE}
  <div class="form-group">
    <label for="gt_name" class="col-md-2 control-label">グループ名の更新</label>
    <div class="col-md-7">
      {form_input('gt_name' , set_value('gt_name', {$gt_name}) , 'class="form-control" placeholder="グループ名を入力してください。max.50文字"')}
      {if form_error('gt_name')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('gt_name')}</font></label>{/if}
    </div>
  </div>
  {/if}

  <div class="form-group">
    <div class="col-sm-offset-2 col-md-7">メ&emsp;&emsp;モ<br>
      <textarea class="form-control input-sm" id="gt_memo" name="gt_memo" placeholder="max.1000文字">{$tmp_memo}</textarea>
      {if form_error('gt_memo')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('gt_memo')}</font></label>{/if}
    </div>
  </div>

  {form_hidden('gt_seq', $gt_seq)}
  {form_hidden('old_gt_name', $gt_name)}

  <div class="form-group">

    <!-- Button trigger modal -->
    <div class="row">
      <div class="col-sm-offset-3 col-md-1">
        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">キャンセル</button>
      </div>

    <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">グループ設定　キャンセル</h4>
          </div>
          <div class="modal-body">
            <p>キャンセルしますか。&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type='submit' name='submit' value='_cancel' class="btn btn-sm btn-primary">O  K</button>
            <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Button trigger modal -->
    <div class="col-sm-offset-1 col-md-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal02">更　　新</button>
    </div>

    <div class="modal fade" id="myModal02" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">グループ設定　更新</h4>
          </div>
          <div class="modal-body">
            <p>更新しますか。&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type='submit' name='submit' value='_change' class="btn btn-sm btn-primary">O  K</button>
            <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Button trigger modal -->
    <div class="col-sm-offset-1 col-md-1">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal03">削　　除</button>
    </div>

    <div class="modal fade" id="myModal03" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">グループ設定　削除</h4>
          </div>
          <div class="modal-body">
            <p>削除しますか。&hellip;</p>
          </div>
          <div class="modal-footer">
            <button type='submit' name='submit' value='_delete' class="btn btn-sm btn-primary">O  K</button>
            <button type="button" class="btn btn-sm" data-dismiss="modal">キャンセル</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

  </div>


{form_close()}
{/if}

<div class="row"><div class="col-md-offset-2 col-md-10">
  {$mess01}
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
