<script>
// アカウント追加ボタン で追加画面表示
$(function () {

	$('#ac_insert_btn').on('click', function () {

		// Modal画面の起動
		$('#ac_insert').modal();

		// Modalに追加画面を表示
		$('#result_acinsert').load('/srct/topdetail/index_aj_acinsert/',{
		});
	 });
});
</script>


<script>
// アカウント編集ボタン で編集画面表示
$(function () {

	$("#ac_update_btn{$ac.ac_seq}").on('click', function () {

		var ac_seq = {$ac.ac_seq};

		// Modal画面の起動
	    $('#ac_update').modal();

		// Modalに編集画面を表示
	    $('#result_acupdate').load('/srct/topdetail/index_aj_acupdate/',{
		    	'ac_seq': ac_seq,
		});
	 });
});
</script>