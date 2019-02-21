jQuery(document).ready(function($) {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('select.form-control').select2();

    $(document).on('change', '#inputSelectRoles', function() {
        var role = $(this).val();

        if (role == 'client')
        {
            $('#inputSelectManager').removeAttr('disabled');
            $('.selectManagerInput').show();
            $('.selectManagerInput .select2-container').attr('style', 'width: 100%');
        }
        else
        {
            $('#inputSelectManager').prop('disabled', false);
            $('.selectManagerInput').hide();
            $('.selectManagerInput .select2-container').attr('style', 'width: 100%');
        }

    });

    $(document).on('click', '.confirmDeleteButton', function(e) {

        e.preventDefault();

        if (confirm('Are you sure you want to delete this item?'))
        {
            window.location.href = $(this).attr('href');
        }

    });

});