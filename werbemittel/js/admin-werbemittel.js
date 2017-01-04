jQuery(document).ready( function( $ ) {

    $('#werbemittel-bild-button').click(function() {

        formfield = $('#werbemittel-bild').attr('name');
        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
        return false;
    });

    window.send_to_editor = function(html) {

        $('body').append('<div id="temp_image">' + html + '</div>');

        var img = $('#temp_image').find('img');

        imgurl   = img.attr('src');
        imgclass = img.attr('class');
        imgid = parseInt(imgclass.replace(/\D/g, ''), 10);

        $('#werbemittel-bild').val(imgid);

        $('img#book_image').attr('src', imgurl);

        try{tb_remove();}catch(e){};

        $('#temp_image').remove();


    }

    insertShortcode = function(name) {
        var win = window.dialogArguments || opener || parent || top;
        var shortcode='[testcode name='+name+']';
        win.send_to_editor(shortcode);
    }

    $(function () {
        $('#insert_shortcode').bind('click',function() {
            var name = $('#name').val();
            insertShortcode(name);
        });
    });

});