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
    $('body').append($('<div/>', {
        class: 'mce'
    }));
    $( ".mce" ).draggable();
    content = $(".content");
    if (content.length > 0) editContent(content);
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
                    "searchreplace code fullscreen",
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
                            csrf: $(".csrf").attr("value"),
                            save: true
                        },
                        success:function( msg ) {
                            console.log('Save result: ' + msg);
                        }
                    });
                },
                theme: "modern",
                file_browser_callback : fm
            });
        }


    function fm(field_name, url, type, win) {
        var roxyFileman = '/fm';
        if (roxyFileman.indexOf("?") < 0) {
            roxyFileman += "?type=" + type;
        }
        else {
            roxyFileman += "&type=" + type;
        }
        roxyFileman += '&input=' + field_name + '&value=' + win.document.getElementById(field_name).value;
        if(tinyMCE.activeEditor.settings.language){
            roxyFileman += '&langCode=' + tinyMCE.activeEditor.settings.language;
        }
        tinyMCE.activeEditor.windowManager.open({
            file: roxyFileman,
            title: 'Roxy Fileman',
            width: 850,
            height: 650,
            resizable: "yes",
            plugins: "media",
            inline: "yes",
            close_previous: "no"
        }, {     window: win,     input: field_name    });
        return false;
    }
}


