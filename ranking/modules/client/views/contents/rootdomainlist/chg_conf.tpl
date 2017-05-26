{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>

{* ヘッダー部分　END *}

<H4><p class="bg-success">&emsp;&emsp;ルートドメイン管理&emsp;編集</p></H4>

{form_open('rootdomainlist/chg_comp/' , 'name="detailForm" class="form-horizontal repeater"')}

  <div class="form-group">
    <label for="rd_rootdomain" class="col-sm-2 control-label">ルートドメイン名</label>
    <div class="col-sm-2 btn-lg">
      {$info.rd_rootdomain}
    </div>
  </div>

  <div class="form-group">
    <label for="rd_sitename" class="col-sm-2 control-label">サイト名</label>
    <div class="col-sm-10">
      {$info.rd_sitename}
    </div>
  </div>

  <div class="form-group">
    <label for="rd_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
    <div class="col-sm-9">
      {if $info.rd_group!=""}
        {foreach from=$info.rd_group item=group}{$group}<br>{/foreach}
      {/if}
    </div>
  </div>

  <div class="form-group">
    <label for="rd_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-9">
      {if $info.rd_tag!=""}
        {foreach from=$info.rd_tag item=tag}{$tag}<br>{/foreach}
      {/if}
    </div>
  </div>


  {form_hidden('info', $info)}

  <br><br>
  <div class="form-group">
    <div class="col-sm-offset-1 col-sm-1">
      {$attr01['name'] = 'back'}
      {$attr01['type'] = 'submit'}
      {$attr01['value'] = '_back'}
      {form_button($attr01 , '戻&emsp;&emsp;る' , 'class="btn btn-primary"')}
    </div>
    <div class="col-sm-offset-1 col-sm-1">
      {$attr['name'] = 'submit'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '実&emsp;&emsp;行' , 'class="btn btn-primary"')}
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
