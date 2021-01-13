jQuery(document).ready(function ($) {

    $('.audit_process_container').fadeIn();

//patch menu urls
    $('a[href="admin.php?page=wphwp_harden_help"]').attr('target', '_blank');
    $('a[href="admin.php?page=wphwp_harden_help"]').attr('href', 'https://www.getastra.com/kb/kb/wp-harden');


    $('a[href="admin.php?page=wphwp_harden_upgrade"]').attr('target', '_blank');
    $('a[href="admin.php?page=wphwp_harden_upgrade"]').attr('href', 'https://www.getastra.com/?ref=wp-harden');


    function validateEmail(email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

// on load open first fixer

//console.log( $('#is_fixer').val() );
    if ($('#is_fixer').val() == '1') {

        $('#recommend_tab .single_status_block:first-child .details_block.fixers_block').show();
        $('#recommend_tab .single_status_block:first-child .show_control').hide();
        $('#recommend_tab .single_status_block:first-child .hide_control').show();
    }

// expand all fixers
    $('body').on('click', '#expand_all', function () {

        $('.single_status_block').each(function () {
            $('.show_control', this).hide();
            $('.hide_control', this).fadeIn();
            $('.details_block', this).fadeIn();
            $('#expand_all').hide();
            $('#collapse_all').show();
        })


    })
// collapse all fixers
    $('body').on('click', '#collapse_all', function () {

        $('.single_status_block').each(function () {
            $('.show_control', this).fadeIn();
            $('.hide_control', this).hide();
            $('.details_block', this).hide();
            $('#expand_all').show();
            $('#collapse_all').hide();
        })


    });

// alpha check
    function isAlphaOrParen(str) {
        return /^[a-zA-Z0-9-_]+$/.test(str);
    }

// fixers processing
    $('body').on('click', '.trace_switch', function (e) {

        e.stopPropagation();


        var parent_pnt = $(this).parents('.whp-switch-wrap');
        if ($(this).prop('checked') == true) {
         


         if ($(this).attr('id') == 'report_email') {

            var data = {
                value: 'on',
                id: $(this).attr('id'),
                custom_admin_report_email: $('#custom_admin_report_email').val(),
                action: 'process_fixer',
                security: whp_local_data.nonce
            }

         }else{ 
            var data = {
                value: 'on',
                id: $(this).attr('id'),
                custom_admin_slug: $('#custom_admin_slug').val(),
                action: 'process_fixer',
                security: whp_local_data.nonce
            }
}
            if ($(this).attr('id') == 'change_login_url') {
                var string = $('#custom_admin_slug').val();

                if (!isAlphaOrParen(string)) {
                    e.preventDefault();
                    alert(whp_local_data.wrong_admin);
                    $('#change_login_url').click();
                    return true;
                }

                var new_login_url = whp_local_data.home_url + (whp_local_data.permalink_structure.length == 0 ? '?' : '') + $('#custom_admin_slug').val();

                $('#whp-login-change-success').addClass('show');
                $('#whp-login-change-success').fadeIn();
                $('#whp-login-change-success span.msg').html('Your login page is now accessible at: ' + new_login_url.link(new_login_url) + '. Bookmark this page!');

                $('#custom_admin_slug').attr('readonly', true);
            }



             if ($(this).attr('id') == 'report_email') {
                var string = $('#custom_admin_report_email').val();

                // if (!isAlphaOrParen(string)) {
                //     e.preventDefault();
                //    // alert(whp_local_data.wrong_admin);
                //     $('#report_email').click();
                //     return true;
                // }

                //var new_login_url = whp_local_data.home_url + (whp_local_data.permalink_structure.length == 0 ? '?' : '') + $('#custom_admin_report_email').val();

                $('#whp-login-change-success').addClass('show');
                $('#whp-login-change-success').fadeIn();
                //$('#whp-login-change-success span.msg').html('Your login page is now accessible at: ' + new_login_url.link(new_login_url) + '. Bookmark this page!');

                $('#custom_admin_report_email').attr('readonly', true);
            }




        } else {

            var data = {
                value: 'off',
                id: $(this).attr('id'),
                custom_admin_slug: $('#custom_admin_slug').val(),
                action: 'process_fixer',
                security: whp_local_data.nonce
            }
            if ($(this).attr('id') == 'change_login_url') {
                $('#custom_admin_slug').attr('readonly', false);
                $('#whp-login-change-success').removeClass('show');
                $('#whp-login-change-success').fadeOut();
            }

            if ($(this).attr('id') == 'report_email') {
                $('#custom_admin_report_email').attr('readonly', false);
                $('#whp-login-change-success').removeClass('show');
                $('#whp-login-change-success').fadeOut();
            }
        }

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function (msg) {
                jQuery('body').prepend('<div class="big_loader"></div>');
            },
            success: function (msg) {


                //console.log( msg );

                jQuery('.big_loader').replaceWith('');

                var obj = jQuery.parseJSON(msg);

                if (obj.result == 'success') {
                    // update fixers number
                    $('#active_fixers').html(obj.is_on);
                    $('#unactive_fixers').html(obj.is_off);
                } else {

                }

            },
            error: function (msg) {
                jQuery('.big_loader').replaceWith('');
            }
        });
    })


// expand functionality 
    $('body').on('click', '.single_status_block .issue_name, .single_status_block .issue_status, .single_status_block .row_control, .single_status_block .fixer_name', function () {

        var parent = $(this).parents('.single_status_block');

        if (!$(parent).hasClass('opened')) {
            $(parent).addClass('opened');
            $('.show_control', parent).hide();
            $('.hide_control', parent).fadeIn();
            $('.details_block', parent).fadeIn();

            return true;
        }

        if ($(parent).hasClass('opened')) {

            $(parent).removeClass('opened');
            $('.hide_control', parent).hide();
            $('.show_control', parent).fadeIn();
            $('.details_block', parent).fadeOut();
        }
    })

    /*
    // show hide details
    $('body').on( 'click', '.show_control', function( e ){
        var parent = $(this).parents('.single_status_block');
        parent.addClass('opened');
        $('.show_control', parent).hide();
        $('.hide_control', parent).fadeIn();
        $('.details_block', parent).fadeIn();
    })

    // hide details
    $('body').on( 'click', '.hide_control', function( e ){
        var parent = $(this).parents('.single_status_block');
        parent.removeClass('opened');
        $('.hide_control', parent).hide();
        $('.show_control', parent).fadeIn();
        $('.details_block', parent).fadeOut();
    })
    */

// button link click
    $('body').on('click', '.button_link', function (e) {
        var url = $(this).attr('data-url');
        $('#fake_link').attr('href', url);
        $('#fake_link').click();
        document.getElementById('fake_link').click();
        $('#fake_link')[0].click();
        $('#fake_link').trigger('click');
    })
    $('body').on('click', '.button_link_local', function (e) {
        var url = $(this).attr('data-url');
        window.location.href = url;
    })

// tabs navigation
    $('body').on('click', '.tab_link', function (e) {
        var this_link = $(this).attr('href');
        $('.single_tab').hide();
        $(this_link).fadeIn();
        $('.head_tab').removeClass('active');
        $(this).parents('.head_tab').addClass('active');

    })

// start audit
    $('body').on('click', '#start_new_audit', function (e) {

        var data = {
            action: 'start_audit',
            security: whp_local_data.nonce
        }
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function (msg) {
                jQuery('body').prepend('<div class="big_loader"></div>');
            },
            success: function (msg) {


                //console.log( msg );

                jQuery('.big_loader').replaceWith('');

                var obj = jQuery.parseJSON(msg);

                if (obj.result == 'success') {

                    $('#recommend_tab').html(obj.error);
                    $('#passed_tab').html(obj.success);
                    $('#site_health_table').html(obj.table);

                } else {

                }

            },
            error: function (msg) {
                jQuery('.big_loader').replaceWith('');
            }
        });

    })

// submit email
    $('body').on('click', '#subscribe_secure', function (e) {

        // verify email
        var email = $('#user_subscribe_email').val();
        if (email == '' || !validateEmail(email)) {
            $('#user_subscribe_email').addClass('input_error');
            return true;
        } else {
            $('#user_subscribe_email').removeClass('input_error');
        }

        var data = {
            email: $('#user_subscribe_email').val(),
            action: 'secure_subscribe',
            security: whp_local_data.nonce
        }
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function (msg) {
                jQuery('body').prepend('<div class="big_loader"></div>');
            },
            success: function (msg) {


                //console.log( msg );

                jQuery('.big_loader').replaceWith('');

                var obj = jQuery.parseJSON(msg);

                if (obj.result == 'success') {
                    $('.get_secure_notice').fadeOut();

                } else {

                }

            },
            error: function (msg) {
                jQuery('.big_loader').replaceWith('');
            }
        });

    })

    //nothanks action
    $('body').on('click', '#no_subscription', function (e) {

        var data = {
            action: 'no_subscribe',
            security: whp_local_data.nonce
        }
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: data,
            beforeSend: function (msg) {
                jQuery('body').prepend('<div class="big_loader"></div>');
            },
            success: function (msg) {


                //console.log( msg );

                jQuery('.big_loader').replaceWith('');

                var obj = jQuery.parseJSON(msg);

                if (obj.result == 'success') {
                    $('.get_secure_notice').fadeOut();
                } else {

                }

            },
            error: function (msg) {
                jQuery('.big_loader').replaceWith('');
            }
        });
    })

    $('#whp-login-change-success button.close').on('click', function(){
        $('#whp-login-change-success').removeClass('show');
        $('#whp-login-change-success').fadeOut();
    });

});