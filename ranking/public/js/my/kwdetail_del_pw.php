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