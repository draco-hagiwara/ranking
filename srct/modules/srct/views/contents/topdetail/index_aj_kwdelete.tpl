<span>以下のキーワード情報を削除します。<br>よろしいですか。</span><br><br>

{section name=item loop=$list}
  {section name=unit loop=$list[item]}
    <ul>
	  <li type="sqare">【キーワード】{$list[item][unit].kw_keyword}, 【URL】{$list[item][unit].kw_url}</li>
    </ul>
  {/section}
{/section}

{*form_open('topdetail/delchk/' , 'name="accountForm" class="form-horizontal repeater"')*}

  <input type="hidden" name="kw_seq" value={$arr_kw_seq}>

  <div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	<button type='submit' name='_submit' value='save' class="btn btn-sm btn-primary">キーワード削除</button>
  </div>

{*form_close()*}

