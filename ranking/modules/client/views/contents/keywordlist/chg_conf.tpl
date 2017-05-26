{* ヘッダー部分　START *}
    {include file="../header.tpl" head_index="1"}

<body>

{* ヘッダー部分　END *}

<H4><p class="bg-success">&emsp;&emsp;キーワード管理&emsp;編集</p></H4>

{form_open('keywordlist/chg_comp/' , 'name="detailForm" class="form-horizontal repeater"')}

  {$mess}
  <div class="form-group">
    <label for="kw_status" class="col-sm-2 control-label">ステータス選択<font color=red> *</font></label>
    <div class="col-sm-2 btn-lg">
      {if $info.kw_status==0}無効{else}有効{/if}
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
      {$info.kw_url}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ URLマッチタイプ<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_matchtype==0}完全一致
      {elseif $info.kw_matchtype==1}前方一致
      {elseif $info.kw_matchtype==2}ドメイン一致
      {elseif $info.kw_matchtype==3}ルートドメイン一致 (サブドメイン含む)
      {else}error{/if}
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
      {if $info.kw_maxposition==0}100件{elseif $info.kw_maxposition==1}200件{elseif $info.kw_maxposition==2}300件{else}error{/if}
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-offset-2 col-md-9">■ 1日の取得回数<font color=red> *</font>：</div>
    <div class="col-md-offset-2 col-md-9">
      {if $info.kw_trytimes==0}１回{elseif $info.kw_trytimes==1}２回{elseif $info.kw_trytimes==2}３回{else}error{/if}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">グループ設定</label>
    <div class="col-sm-9">
      {$info.kw_group}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_tag" class="col-xs-2 col-md-2 control-label">タグ設定</label>
    <div class="col-md-9">
      {if $info.kw_tag!=""}
        {foreach from=$info.kw_tag item=tag}{$tag}<br>{/foreach}
      {/if}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_memo" class="col-xs-2 col-md-2 control-label">メモ</label>
    <div class="col-md-9">
      {$info.kw_memo}
    </div>
  </div>

  <div class="form-group">
    <label for="kw_group" class="col-xs-2 col-md-2 control-label">他キーワードへの反映</label>
    <div class="col-sm-10">
      「最大取得順位」「1日の取得回数」「グループ設定」「タグ設定」を同一ドメイン内に反映させたい場合は以下を選択してください。
      {form_dropdown('reflection', $options_reflection, set_value('reflection', ''))}
    </div>
  </div>


  {form_hidden('info', $info)}


  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-8">
      {$attr01['name'] = 'back'}
      {$attr01['type'] = 'submit'}
      {$attr01['value'] = '_back'}
      {form_button($attr01 , '戻　　る' , 'class="btn btn-primary"')}

      {$attr['name'] = 'submit'}
      {$attr['type'] = 'submit'}
      {form_button($attr , '実　　行' , 'class="btn btn-primary"')}
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
