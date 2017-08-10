<!--
 * モーダル画面での各種操作script
 *
 * 追加、編集、削除、レポート表示
 *
-->
<script>
// チェックボックスクリック ＆ キーワード追加ボタン で追加画面表示
$(function () {

	$('#kw_insert_btn').on('click', function () {

		console.log("insert_btn");

		// Modal画面の起動
		$('#kw_insert').modal();

		// Modalに追加画面を表示
		$('#result_kwinsert').load('/srct/topdetail/index_aj_kwinsert/',{
		});
	 });
});
</script>


<script>
// チェックボックスクリック ＆ キーワード編集ボタン で編集画面表示
$(function () {

	$('#kw_update_btn').on('click', function () {

		var checkbox_kwseq = [];
		var kw_seq = "";
		var check_count = $('input[name="checkbox_kwseq[]"]:checked').length;

		//console.log(check_count);

		if (check_count == 0 ){
			alert('編集対象となるキーワードにチェックを入れてください。');
		} else {
			// Modal画面の起動
		    $('#kw_update').modal();

			// チェックされたcheckboxからkw_seqを配列で取得
		 	$('input[name="checkbox_kwseq[]"]:checked').each(function() {
		 		checkbox_kwseq.push($(this).val());
		 	});

		 	//console.log(checkbox_kwseq);

			// Modalに編集画面を表示
		    $('#result_kwupdate').load('/srct/topdetail/index_aj_kwupdate/',{
			    'kw_seq': checkbox_kwseq,
			});
		}
	 });
});
</script>


<script>
// チェックボックスクリック ＆ キーワード削除ボタン で削除画面表示
$(function () {

	$('#kw_delete_btn').on('click', function () {

		var checkbox_kwseq = [];
		var kw_seq = "";
		var check_count = $('input[name="checkbox_kwseq[]"]:checked').length;

		console.log(check_count);

		if (check_count == 0 ){
			alert('対象となるキーワードにチェックを入れてください。');
		} else {
			// Modal画面の起動
		    $('#kw_delete').modal();

			// チェックされたcheckboxからkw_seqを配列で取得
		 	$('input[name="checkbox_kwseq[]"]:checked').each(function() {
		 		checkbox_kwseq.push($(this).val());
		 	});

		 	console.log(checkbox_kwseq);

			// Modalに削除画面を表示
			$('#result_kwdelete').load('/srct/topdetail/index_aj_kwdelete/',{
			    'kw_seq': checkbox_kwseq,
			});
		}
	});
});
</script>

<script>
// チェックボックスクリック ＆ レポート作成ボタン でレポート画面表示
$(function () {

	$('#kw_report_btn').on('click', function () {

		var checkbox_kwseq = [];
		var kw_seq = "";
		var check_count = $('input[name="checkbox_kwseq[]"]:checked').length;

		console.log(check_count);

		if (check_count == 0 ){
			alert('対象となるキーワードにチェックを入れてください。');
		} else {
			// チェックされたcheckboxからkw_seqを配列で取得
		 	$('input[name="checkbox_kwseq[]"]:checked').each(function() {
		 		checkbox_kwseq.push($(this).val());
		 	});

		 	console.log(checkbox_kwseq);



			window.open('','preview');

			var form = document.form1;

			/* エレメント作成&データ設定&要素追加 */
			var e1 = document.createElement('input');
			e1.setAttribute('type', 'hidden');
			e1.setAttribute('name', "kw_seq");
			e1.setAttribute('value', checkbox_kwseq);
			form.appendChild(e1);

			form.action = 'https://srct.dev.local/srct/topdetail/report/';
			form.target = 'preview';
			form.method = 'post';
			form.submit();
			return true;
		}
	});
});
</script>
