//<!--
// *
// * ウォッチリスト登録＆解除（page毎の作成）
// *
//-->
$('.btn_wk' + page).on('click', function(){

    $(this).toggleClass('active');

    var text_kw = $(this).data('kw-seq');

    if($(this).hasClass('active')){
        $("#watchlist_kw"+text_kw).attr('src', '/images/user/wl_on.png');
    } else {
        $("#watchlist_kw"+text_kw).attr('src', '/images/user/wl.png');
    }

    // Ajax通信を開始する
    $.ajax({
        url: '/srct/topdetail/index_aj_watchlist/',
        type: 'post', 						// getかpostを指定(デフォルトは前者)
        dataType: 'json', 					// 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
        data: { 							// 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
      	  chg_seq: text_kw,
        }
    })
    .done(function (response) {
        //$('#result_wk').val('成功');
        //$('#detail_wk').val(response.data);
    })
    .fail(function () {
        //$('#result_wk').val('失敗');
        //$('#detail_wk').val('');
    });
});
