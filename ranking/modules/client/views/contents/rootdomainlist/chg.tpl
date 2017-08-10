{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<link href="{base_url()}../../js/select2/select2.css" rel="stylesheet">
<script src="{base_url()}../../js/select2/select2.min.js"></script>

<body>

{* ヘッダー部分　END *}

<H4><p class="bg-success">&emsp;&emsp;ルートドメイン管理&emsp;編集</p></H4>

{form_open('rootdomainlist/chg_chk/' , 'name="detailForm" class="form-horizontal repeater"')}

  <br>
  {$mess}

  <div class="form-group">
    <label for="rd_rootdomain" class="col-xs-2 col-md-2 control-label">ルートドメイン名</label>
    <div class="col-md-9">
      {$info.rd_rootdomain}
    </div>
  </div>
  <br>

  <div class="form-group">
    <label for="rd_sitename" class="col-xs-2 col-md-2 control-label">サイト名</label>
    <div class="col-md-9">
      {form_input('rd_sitename' , set_value('rd_sitename', $info.rd_sitename) , 'class="form-control" placeholder="サイト名を入力してください。max.100文字"')}
      {if form_error('rd_sitename')}<span class="label label-danger">Error : </span><label><font color=red>{form_error('rd_sitename')}</font></label>{/if}
    </div>
  </div>

  {* Group ＆ Tag設定 *}
  {include file="../../../../../public/js/my/rdlist_chg_grouptag.php"}

  {form_hidden('rd_seq', $info.rd_seq)}
  {form_hidden('rd_cl_seq', $info.rd_cl_seq)}
  {form_hidden('rd_rootdomain', $info.rd_rootdomain)}

{*
  <br>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
      <button type="button" onclick="submit();">確&emsp;&emsp;認</button>
    </div>
  </div>
*}

  <br>
  <!-- Button trigger modal -->
  <div class="row">
    <div class="col-sm-2 col-sm-offset-2">
      <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal01">編&emsp;&emsp;集</button>
    </div>

  <div class="modal fade" id="myModal01" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">ルートドメイン管理　編集</h4>
        </div>
        <div class="modal-body">
          <p>この内容で、編集しますか。&hellip;</p>
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

{*
{form_open("rootdomainlist/search/{$seach_page_no}/" , 'name="detailForm" class="form-horizontal"')}

    <div class="col-sm-offset-5 col-sm-1">
      {$attr['name'] = '_back'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '戻&emsp;&emsp;る' , 'class="btn btn-primary btn-sm"')}
    </div>
  </div>

{form_close()}
*}


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
