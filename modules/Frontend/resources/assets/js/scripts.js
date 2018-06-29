$(document).ready(function () {
    $('.menuLinks a').on('click', function () {
        $('.menuLinks li').removeClass('active');
        $(this).parent("li").addClass('active');

        var tagid = $(this).data('tag');

        $('.forum-block').removeClass('activeBox').addClass('hideBox');
        $('#' + tagid).addClass('activeBox').removeClass('hideBox');
    });

    // $(".leftMenu").hide();

    // TODO: There is no mobile-bar yet
    $('.mobile-bar').on('click', function () {
        $('.leftMenu').slideToggle('');
    });
});
