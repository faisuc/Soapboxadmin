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

    $('.datetimepicker').datetimepicker({
        format: 'yyyy-mm-dd HH:ii P',
        showMeridian: true,
        autoclose: true,
        todayBtn: true,
        pickerPosition: "bottom-right",
        todayHighlight: true
    });

    $(document).on('click', '.postnotes-modal .delete_postnote', function(e) {

        e.preventDefault();

        var note_id = $(this).attr('data-postnote-id');
        var btn = $(this);

        if (confirm('Are you sure you want to delete this item?'))
        {
            $.ajax({
                url: 'ajax/delete/post/notes',
                method: 'POST',
                data: {
                    note_id: note_id
                },
                success: function(response) {
                    if (response.success) {
                        btn.parent().remove();
                    }
                }
            });
        }
    });

    $(document).on('click', '.postnotes-modal .add_note', function(e) {
        e.preventDefault();

        var content = $.trim($('.postnotes-modal textarea').val());
        var post_id = $('.postnotes-modal input[name="post_id"]').val();

        $.ajax({
            url: '/ajax/add/post/notes',
            method: 'POST',
            data: {
                content: content,
                post_id: post_id
            },
            success: function(response) {

                if (response.success) {
                    var role = response.role[0].slug;
                    var queueContainer = $('.queue-' + post_id);

                    if (role == 'client') {
                        queueContainer.find('.card').attr('style', 'border: 5px solid #0000FF');
                    } else {
                        queueContainer.find('.card').attr('style', 'border: 5px solid #FFFF00');
                    }

                    $('.post-notes-container .empty').hide();
                    var html = '';
                    html += `<div class="alert alert-info" role="alert">
                                    <p>` + response.collection.content + `</p>
                                    <br /><b>Date Created</b>: ` + response.collection.created_at + `
                                    <br /><b>Posted by</b>: ` + response.collection.first_name + ' ' + response.collection.last_name + `
                                    <a href="#" class="btn btn-flat ink-reaction btn-default delete_postnote" data-postnote-id="` + response.collection.id + `">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>`;
                    $('.postnotes-modal .post-notes-container').append(html);
                    $('.postnotes-modal textarea').val('');
                    $('.postnotes-modal textarea').focus();

                    setTimeout(function() {
                        var div = $('.postnotes-modal .modal-body');
                        div.scrollTop(div.prop('scrollHeight'));
                    }, 500);
                } else {
                    alert(response.message);
                }
            }
        });

    });

    $('.postnotes-modal').on('show.bs.modal', function (event) {

        var button = $(event.relatedTarget);
        var modal = $(this);
        var post_id = button.attr('data-post-id');

        modal.find('input[name="post_id"]').val(button.attr('data-post-id'));

        $('.postnotes-modal .post-notes-container').html('');

        $.ajax({
            url: '/ajax/get/post/notes',
            method: 'GET',
            data: {
                post_id: post_id
            },
            success: function(response) {

                if (response.collections.length > 0) {
                    var html = '';

                    for (var i = 0; i < response.collections.length; i++) {

                        var canDelete = '';

                        if (response.collections[i].user_id == $('meta[name=active_user]').attr('content')) {
                            canDelete = `<a href="#" class="btn btn-flat ink-reaction btn-default delete_postnote" data-postnote-id="` + response.collections[i].id + `">
                                <i class="fa fa-trash"></i>
                            </a>`;
                        }

                        html += `<div class="alert alert-info" role="alert">
                                    <p>` + response.collections[i].content + `</p>
                                    <br /><b>Date Created</b>: ` + response.collections[i].created_at + `
                                    <br /><b>Posted by</b>: ` + response.collections[i].first_name + ' ' + response.collections[i].last_name + canDelete + `
                                </div>`;
                    }

                    $('.postnotes-modal .post-notes-container').append(html);
                    $('.postnotes-modal textarea').val('');
                    $('.postnotes-modal textarea').focus();

                    setTimeout(function() {
                        var div = $('.postnotes-modal .modal-body');
                        div.scrollTop(div.prop('scrollHeight'));
                    }, 500);

                } else {
                    $('.postnotes-modal .post-notes-container').html('<b class="empty">No notes yet.</b>');
                }
            }
        });
    })

    /* Facebook Pages Display For Post */
    $(document).on('change','input[name="facebook_post"]',function() {
        let check = $(this).prop('checked');
        if(check) {
            $('#facebook-pages').show();
        }
        else {
            $('#facebook-pages').hide();
        }
    });
    /* Facebook Pages Display For Post */

    /* Instagram Display For Post */
    $(document).on('change','input[name="instagram_post"]',function() {
        let check = $(this).prop('checked');
        if(check) {
            $('#instagram_user_pass').show();
            $('#instagram_user_pass').find('input[name="insta_username"]').attr('required','required');
            $('#instagram_user_pass').find('input[name="insta_password"]').attr('required','required');
        }
        else {
            $('#instagram_user_pass').hide();
            $('#instagram_user_pass').find('input[name="insta_username"]').removeAttr('required');
            $('#instagram_user_pass').find('input[name="insta_password"]').removeAttr('required');
        }
    });
    /* Instagram Display For Post */

});