// 「全てにチェック」のチェックボックスをチェックしたら発動
$('#checkbox_all').change(function() {

  // もし「全てにチェック」のチェックが入ったら
  if ($(this).prop('checked')) {

    // チェックを付ける
    $('input[name="checkbox_kwseq[]"]').prop('checked', true);

  // もしチェックが外れたら
  } else {

    // チェックを外す
    $('input[name="checkbox_kwseq[]"]').prop('checked', false);
  }
});
