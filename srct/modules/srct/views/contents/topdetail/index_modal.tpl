
{*** header : キーワード追加 ***}
<div class="modal fade" id="kw_insert" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード追加</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_kwinsert"></div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="kw_insert_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード追加</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_kwinsert_chk"></div>
        </form>
      </div>
    </div>
  </div>
</div>


{*** header : キーワード編集 ***}
<div class="modal fade" id="kw_update" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード編集</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_kwupdate"></div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="modal-result"></div>

<div class="modal fade" id="kw_update_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード編集</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_kwupdate_chk"></div>
        </form>
      </div>
    </div>
  </div>
</div>


{*** header : キーワード削除 ***}
<div class="modal fade" id="kw_delete" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード削除</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_kwdelete"></div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="kw_delete_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード削除チェック</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_kwdelete_chk"></div>
        </form>
      </div>
    </div>
  </div>
</div>


{*** header : キーワード情報：CSVアップロード処理 ***}
<div class="modal fade" id="kw_csvupload" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード情報：CSVアップロード</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_csvupload"></div>
        </form>
      </div>
    </div>
  </div>
</div>

<div id="modal-result"></div>

<div class="modal fade" id="kw_csvupload_chk" tabindex="-1" role="dialog" aria-labelledby="ModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">キーワード情報：CSVアップロード</h4>
      </div>
      <div class="modal-body">
        <form>
          <div id="result_csvupload_chk"></div>
        </form>
      </div>
    </div>
  </div>
</div>


{* モーダル（編集/削除/レポート）処理 *}
{include file="../../../../../public/js/my/top_modal.php"}
{* キーワード情報 アップロード & ダウンロード処理 *}
{include file="../../../../../public/js/my/top_csv.php"}





