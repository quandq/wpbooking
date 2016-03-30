jQuery(document).ready(function( $ ){
/**
 * Condition tags
 * @type {string}
 */
    $('#traveler-layout-id').change(function(){
        var container = $(this).parent().parent();
        var value = $(this).val();
        var url = container.find('.url_layout').data('url');
        url = url.replace(/__layout__id/g, value);
        container.find('.url_layout').attr('href',url);
        console.log(url);
    });

    $(".btn_del_layout").click(function(){
        var $msg = $(this).data('msg');
        var r = confirm($msg);
        if (r == true) {

        } else {
            return false;
        }
    });

    $('#traveler_field_form_build').prop('selectedIndex',0);

    $("#traveler_field_form_build").change(function(){
        var container  = $(this).parent().parent();
        var value = $(this).val();
        var html = container.find('#item-'+value).html();
        var title = container.find('#item-'+value).data('title');
        var div_control = container.find('.select-control');
        div_control.find('.head').html(title);
        div_control.find('.content-flied-control').html(html);
        div_control.find('#traveler-shortcode-flied').val("");
        if(value != ""){
            container.find(".div-content-control").show(500);
        }else{
            container.find(".div-content-control").hide(500);
        }


        // add shortcode
        var name_shortcode = container.find('#item-'+value).data('name-shortcode');
        if(name_shortcode != undefined){
            var shortcode = "["+name_shortcode;
            container.find('.div-content-control .item').each(function(){
                var item_name =$(this).attr('name');
                var item_value = $(this).val();

                if(item_value != ""){
                    shortcode += " "+item_name+"="+item_value
                }
            });
            shortcode += " ]";
            $("#traveler-shortcode-flied").val(shortcode);
        }
    });


    // add shortcode
    $(document).on('keyup change','.content-flied-control .item',function(){
        var container  = $(this).parent().parent().parent();
        var name_shortcode = $(this).data('name-shortcode');
        var shortcode = "["+name_shortcode;
        container.find('.item').each(function(){

            var item_name =$(this).attr('name');
            var item_value = $(this).val();

            if(item_value != ""){
                shortcode += " "+item_name+"="+item_value
            }
        });
        shortcode += " ]";
        $("#traveler-shortcode-flied").val(shortcode);
    });
    $(document).on('change','.content-flied-control .group-checkbox .item_check_box',function(){
        var value = '';
        var container =  $(this).parent().parent().parent().parent();
        container.find('.item_check_box').each(function(){
            if($(this).attr('checked')) {
                value +=  $(this).val()+',';
            }
        });
        container.find('.item').val(value.substring(0,value.length - 1));
        container.find('.item').change();
    });


});