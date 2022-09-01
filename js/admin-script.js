function __(OBJ) { console.log(OBJ); }
jQuery(document).ready(function () {

    jQuery('#root-directory-id').on('keyup', function () {

        console.log("working");

    });

});


/**
 * 
 * Form manager plugin for jQuery mobile
 * 
 * */
var JQ = jQuery.noConflict(); // Using JQ as jQuery for no conflict and shortcut
JQ.fn.fManager = function (data) {

    data = JQ.parseJSON(data); // string to json conversion
    __(data.menu_options);

    // Working with menu options
    STR = '<ul>';

    JQ.each(data.menu_options, function (index, value) {

        //~ __("Index: " + index + ", Value: " + value);
        STR += "<li>" + value + "</li>";


    });

    STR += '</ul>';

    JQ("#fmp_permission_wrapper_id").html(STR);

}

FMP = {};


(function ($) {
    $(document).ready(function () {

        // Tooltip section
        TIPPY = new Tippy('.tippy');

        FMP.banned_notification = function (id, role) {
            if ($('#' + id + ':checked').size() > 0) if (!confirm("Are you sure you want to ban " + role + " from all activities.")) $('#' + id).attr('checked', false);

        }

        // Commonfolder name editor shower.
        $('#folder_options_single_id').on('click', function () {
            if ($('#folder_options_single_id:checked').size() > 0) {
                $('#public-folder-wrapper').show('slow');
            } else $('#public-folder-wrapper').hide('slow');
        });

    });
})(jQuery)