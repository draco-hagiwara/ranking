{* ヘッダー部分　START *}
  {include file="../header.tpl" head_index="1"}

<body>

{* ヘッダー部分　END *}

<script>

var pw;

pw = prompt("パスワードを入力して下さい。","");

if (/[^\x21-\x7e]+$/.test(pw)){
	alert("半角英数字・記号のみを入力して下さい。");
	location.href = "{base_url()}keyworddetail/detail/"+{$kw_seq}+"/";
}else if(pw == null){
	alert("キャンセルされました。");
	location.href = "{base_url()}keyworddetail/detail/"+{$kw_seq}+"/";
}else if(pw != ""){
	location.href = "{base_url()}keyworddetail/del_pw/"+{$kw_seq}+"/"+pw+"/";
}else{
	location.href = "{base_url()}keyworddetail/detail/"+{$kw_seq}+"/";
}

</script>

<div class="jumbotron">
  <h3>　キーワード削除</h3>
</div>

{* フッター部分　START *}
  {include file="../footer.tpl"}
{* フッター部分　END *}

    </div>
  </section>
</div>
<!-- Bootstrapのグリッドシステムclass="row"で終了 -->

</body>
</html>
