<style>
#loading{
    display: none ;
    position : absolute ;
    left : 50% ;
    top : 20% ;
    margin-left : -30px ;
}
</style>


<form>
  <div class="form-group">
    <div">
      {form_upload('kw_csv_data', '', 'id="file-path"')}
    </div>
  </div>

  <div class="form-group">
    <div">
      <br>・一回にアップロードできる件数は100件です。
      <br>・ファイルフォーマットは「キーワード情報ダウンロード」より作成されるCSVファイルに準じます。
      <br>・一行目は項目欄（必須）となります。
      <br>・一列目の「seq」にID番号が設定されている場合は既存情報を更新します。<br>&emsp;設定されていない(スペース)場合は追加処理となります。
      <br>・更新の対象となる項目は以下の通りです。<br>&emsp;&emsp;"ステータス(U)"，"対象URL(U)"，"URL一致方式(U)"，"設定グループ(U)"
      <br>・
      <button type="button" class="btn btn-default  btn-sm" data-container="body" data-toggle="popover" data-placement="right"
        data-content="<b>【ステータス】</b><br>&emsp;1：有効，0：無効<br>
                      <b>【URL一致方式】</b><br>&emsp;0：完全一致，<br>&emsp;1：前方一致，<br>&emsp;2：ドメイン一致，<br>&emsp;3：ルートドメイン一致
                     ">
        項目コードの説明
      </button>
    </div>
  </div>

  <br>

  {* 処理待ちローディングアニメーション *}
  <div id="loading"><img src="/images/user/loading.gif"></div>

  <div id="result_loading">
    <div id="result_csvup"></div>
  </div>

  <div class="modal-footer">
	{*<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>*}
	<div id="csv-upload-close" class="btn btn-sm btn-default">Close</div>
	<div id="csv-upload" class="btn btn-sm btn-primary">↑&emsp;アップロード</div>
  </div>
</form>
<!-- </form> -->

{* POP-Over *}
<script src="{base_url()}../../js/my/popover.js"></script>

<script>
// キーワード情報 CSVアップロード処理
$(function () {

	// UPLOAD処理
	$('#csv-upload').on('click', function () {

	    // 処理待ちローディングアニメーションの表示
        $("#loading").fadeIn();

	    // コンテンツ（結果メッセージ）の非表示
        $('#result_loading').css('display', 'none');

		var fd = new FormData($('#csv-form').get(0));

		fd.append('body', $('#file-path').val());

	    $.ajax({
	        url           : '/srct/data_csv/toplist_csvup_chk/',
	        type          : 'post',
	        dataType      : 'jsonp',
	        jsonpCallback : 'callback',
	        data          : fd,

	        cache         : false,
	        contentType   : false,
	        processData   : false,
	    })
	    .done(function(response) {

	    	// 処理完了したのでアニメーションはフェードアウトさせる
    	    $("#loading").fadeOut();

	    	// コンテンツをフェードインさせる
    	    $("#result_loading").fadeIn();

	    	for(var i=0; i<response.length; i++) {
	    	   	if (response[i].title == "up_mess_err") {

	    	   		$('#result_csvup').empty();
	    	   		$('#result_csvup').append(response[i].message);
	    	   		$("#result_csvup").css("color","red");
	    	   	} else {
		    	   	$('#result_csvup').empty();
	    	   		$('#result_csvup').append(response[i].message);
	    	   		$("#result_csvup").css("color","blue");
	    	   	}
	    	}
	    })
	    .fail(function() {
	        alert('通信エラー');
	    });

	});

	// 画面の削除 & リロード
	$('#csv-upload-close').on('click', function () {

	   	// Modal画面の削除
		$('#kw_csvupload_chk').modal('hide');

		// 画面のリロード
		location.reload();

	});
});
</script>