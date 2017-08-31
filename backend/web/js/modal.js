/**
 * Created by kastiel on 5/11/17.
 */

$.fn.exists = function () {
    return this.length !== 0;
};

$(document).ready(function(){

    var modal = $('#modal-popup');
    if(modal.exists()) {

        $(document).on('click', '.popup', function (e) {

            var url = $(this).attr('href');
            if (url) {

                $.post(url, {}, function (response) {

                    var content = $(response);
                    var header = content.find('h1').first();

                    if (header && header.length) {

                        modal.find('.modal-header:not(:has(h1))').each(function () {
                            $(this).append($('<h1>'));
                        });

                        modal.find('.modal-header').find('h1').html(header.html());
                        header.remove();
                    }


                    modal.find('.modal-body').html(content);
                    modal.modal('show');

                });
            }

            e.preventDefault();
        });
    }
});
