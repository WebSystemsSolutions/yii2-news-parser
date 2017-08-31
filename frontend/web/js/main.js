/**
 * Created by kastiel on 5/3/17.
 */

$(document).ready(function(){

    function initNiceScroll(remove) {

        var scroll = $('.scroll');

        if (remove) {

            $(".nicescroll-rails").remove();
            scroll.niceScroll().remove();
        }

        scroll.niceScroll({
            cursorcolor: "#1FB5AD",
            cursorwidth: "6px",
            autohidemode: false
        });
    }


    $('.pjax[data-pjax-container]').on('pjax:complete', function() {
        initNiceScroll(true);
    });

    initNiceScroll();
});