<!--
 * TOP：CSV処理 script
 *
 * ・キーワード情報 アップロード
 * ・キーワード情報 ダウンロード
 *
-->
<script>
// キーワード情報 アップロード
$(function () {

	$('#csv_upload_btn').on('click', function () {

		// Modal画面の起動
	    $('#kw_csvupload').modal();

		// Modalに編集画面を表示
	    $('#result_csvupload').load('/srct/data_csv/toplist_csvup/',{
		});

	});
});
</script>

<script>
// キーワード情報 ダウンロード
$(function () {

	$('#csv_download').on('click', function () {

		var checkbox_kwseq = [];
		var kw_seq = "";
		var check_count = $('input[name="checkbox_kwseq[]"]:checked').length;

		if (check_count == 0 ){
			alert('対象となるキーワードにチェックを入れてください。');
		} else {

			// チェックされたcheckboxからkw_seqを配列で取得
		 	$('input[name="checkbox_kwseq[]"]:checked').each(function() {
		 		checkbox_kwseq.push($(this).val());
		 	});

			var formName = "form1";
			var method   = "POST";
			var url      = "/srct/data_csv/toplist_csvdown/";

			var f1 = document.forms[formName];

			/* エレメント作成&データ設定&要素追加 */
			var e1 = document.createElement('input');
			e1.setAttribute('type', 'hidden');
			e1.setAttribute('name', "kw_seq");
			e1.setAttribute('value', checkbox_kwseq);
			f1.appendChild(e1);

			/* サブミットするフォームを取得 */
			f1.method = method;                                   // method(GET or POST)を設定する
			f1.action = url;                                      // action(遷移先URL)を設定する
			f1.submit();                                          // submit する
			return true;

		}
	 });
});
</script>