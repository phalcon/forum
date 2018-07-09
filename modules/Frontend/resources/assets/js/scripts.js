$(document).ready(function () {
    $('.sidebar-links a').on('click', function () {
        $('.sidebar-links li').removeClass('active');
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
