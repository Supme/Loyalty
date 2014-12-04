/**
 * @package Loyality Portal
 * @author Supme
 * @copyright Supme 2014
 * @license http://opensource.org/licenses/MIT MIT License
 *
 *  THE SOFTWARE AND DOCUMENTATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF
 *	ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 *	IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A PARTICULAR
 *	PURPOSE.
 *
 *	Please see the license.txt file for more information.
 *
 */

function edit(){
    // Contents block
    content = $(".content");

    // News block
    news = $(".news")

    if (content.length >0) editContent(content);
    if (news.length >0) editNews(news);
}

function editNews(news){
    if (news.hasClass('editable')){
        news.removeClass('editable');
        $('#newsTitle').prop('readonly', true);
        $('#newsDate').prop('readonly', true);
        $('.submit').hide();
        tinymce.remove();
    } else {
        news.addClass('editable');
        $('#newsTitle').prop('readonly', false);
        $('#newsDate').prop('readonly', false);
        $('.submit').show();
        tinymce.init({
            inline : true,
            fixed_toolbar_container: ".mce",
            language : 'ru',
            selector:'div.editable',
            toolbar:
                " core |" +
                    " insertfile undo redo |" +
                    " styleselect |" +
                    " bold italic |" +
                    " alignleft aligncenter alignright alignjustify |" +
                    " bullist numlist |" +
                    " link image |" +
                    " forecolor backcolor |" +
                    " textcolor |" +
                    " code",
            plugins: [
                "textcolor","advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualcontents code fullscreen",
                "insertdatetime media table contextmenu paste"
            ],
            theme: "modern"
        });
    }
}

function editContent(content)    {
        if (content.hasClass('editable')){
            content.removeClass('editable');
            tinymce.remove();
        } else {
            content.addClass('editable');
            tinymce.init({
                inline : true,
                fixed_toolbar_container: ".mce",
                language : 'ru',
                selector:'div.editable',
                toolbar:
                    " core |" +
                        " save |" +
                        " insertfile undo redo |" +
                        " styleselect |" +
                        " bold italic |" +
                        " alignleft aligncenter alignright alignjustify |" +
                        " bullist numlist |" +
                        " link image |" +
                        " forecolor backcolor |" +
                        " textcolor |" +
                        " code",
                plugins: [
                    "save","textcolor","advlist autolink lists link image charmap print preview anchor",
                    "searchreplace visualcontents code fullscreen",
                    "insertdatetime media table contextmenu paste"
                ],
                save_enablewhendirty: true,
                save_onsavecallback: function() {
                    $.ajax({
                        type: "POST",
                        url: "#",
                        data: {
                            position: $(".mce-edit-focus").attr("positionId"),
                            text: $(".mce-edit-focus").html(),
                            save: true
                        },
                        success:function( msg ) {
                            console.log('Save result: ' + msg);
                        }
                    });
                },
                theme: "modern"
            });
        }
}

function saveNews(){
    $.ajax({
        type: "POST",
        url: "#",
        data: {
            id: $('.submit').attr('id'),
            title: $('#newsTitle').val(),
            date: $('#newsDate').val(),
            announce: $('[position = "announce"]').html(),
            text: $('[position = "text"]').html(),
            save: true
        },
        success:function( msg ) {
            console.log('Save result: ' + msg);
        }
    })
}

