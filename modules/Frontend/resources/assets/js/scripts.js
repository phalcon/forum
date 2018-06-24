$(document).ready(function () {

//+++++++++++++++++++++++++++++++++++++++++++
//     Jquery Code Start
//+++++++++++++++++++++++++++++++++++++++++++     

    $('.menuLinks a').click(function () {
        $('.menuLinks li').removeClass('active');
        $(this).parent("li").addClass('active');
        var tagid = $(this).data('tag');
        $('.forum-block').removeClass('activeBox').addClass('hideBox');
        $('#' + tagid).addClass('activeBox').removeClass('hideBox');
    });
    $(".leftMenu").hide();
    $('.mobail-bar').click(function () {
        $('.leftMenu').slideToggle('');
    });
});