//<script>
$(function(){
    $("#acMenu dt").on("click", function() {
        $(this).next().slideToggle();
        $(this).toggleClass("active");//追加部分
    });
});
//</script>