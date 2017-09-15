<!--
 *
 * ウォッチリスト登録＆解除
 *
-->
<script  language="JavaScript">
$(function(){
  $('.btn_wk').on('click', function(){
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
          type: 'post', 					// getかpostを指定(デフォルトは前者)
          dataType: 'json', 				// 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
          data: { 							// 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
        	  chg_seq: text_kw,
          }
      })
      .done(function (response) {
      })
      .fail(function () {
          //$('#result_wk').val('失敗');
          //$('#detail_wk').val('');
      });
  });
});

// ルートドメイン＆キーワード ウォッチリスト共存
$(function(){
  var sel = 0;

  // ** キーワード
  $(".btn_wd").on('click', function(){

    sel = 1;
    $(this).toggleClass('active');

      if($(this).hasClass('active')){
        var text = $(this).data('rd-clicked');
        $(this).text(text);
        $('[data-rd-clicked]').css('color','orange');
      } else {
        var text = $(this).data('rd-default');
        $(this).text(text);
        $('[data-rd-default]').css('color','black');
      }

      var text_rd = $(this).data('rd-seq');

      // Ajax通信を開始する
      $.ajax({
          url: '/client/top/watchlist_domain/',
          type: 'post', 					// getかpostを指定(デフォルトは前者)
          dataType: 'json', 				// 「json」を指定するとresponseがJSONとしてパースされたオブジェクトになる
          data: { 							// 送信データを指定(getの場合は自動的にurlの後ろにクエリとして付加される)
        	  chg_seq: text_rd,
          }
      })

      .done(function (response) {
          $('#result_wd').val('成功');
          $('#detail_wd').val(response.data);
      })
      .fail(function () {
          $('#result_wd').val('失敗');
          $('#detail_wd').val('');
      });
  });

  // ** ルートドメイン
  $("#kwMenu dt").on("click", function() {

    if (sel == 0) {
      $(this).removeClass("btn_wd");	//除外部分

      $(this).next().slideToggle();
      $(this).toggleClass("active");	//追加部分
    } else {
      sel = 0;
    }
  });

});
</script>