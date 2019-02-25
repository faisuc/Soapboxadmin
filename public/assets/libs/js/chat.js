jQuery(document).ready(function($) {
    'use strict';

    function clientExists()
    {

        var count = $('.messaging .inbox_chat .chat_list').length;

        if (count > 0)
        {
            $('.messaging .inbox_chat .chat_list:first-child a').trigger('click');
        }

    }

    function getUserID()
    {
        var url = window.location.href;
        var id = url.substring(url.lastIndexOf('/') + 1);
        var count = $('.messaging .inbox_chat .chat_list').length;

        if (count > 0) {
            return id;
        } else {
            return false;
        }

    }

    function formatAMPM(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'pm' : 'am';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;

        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth() + 1; //January is 0!
        var yyyy = today.getFullYear();

        if (dd < 10) {
        dd = '0' + dd;
        }

        if (mm < 10) {
        mm = '0' + mm;
        }

        today = mm + '/' + dd + '/' + yyyy;

        return strTime + ' | ' + today;
    }

    function clearChat() {

        $('.msg_history').html('');
        $('.write_msg').val('');
        $('.write_msg').focus();

    }


    socket.emit('add user', {'user_id' : $('meta[name=active_user]').attr('content')});

    $(document).on('click', '.messaging .inbox_chat .chat_list a', function(e) {

        e.preventDefault();

        var chatUrl = $(this).attr('href');
        var user_id = $(this).attr('data-user-id');

        history.pushState({
            id: 'messages',
            user_id: user_id
        }, 'Test', chatUrl);
 
        $('.messaging .inbox_chat .chat_list').removeClass('active_chat');
        $('.msg_history').removeAttr('data-user-id');
        $('.msg_history').attr('data-user-id', user_id);

        $(this).parent().addClass('active_chat');

        clearChat();

    });

    clientExists();

    window.addEventListener('popstate', function (event) {
        if (history.state && history.state.id === 'messages') {
            if (history.state.user_id) {
                $('.messaging .inbox_chat .chat_list:first-child a[data-user-id="' + history.state.user_id + '"]').trigger('click');
                $('.msg_history').removeAttr('data-user-id');
                $('.msg_history').attr('data-user-id', history.state.user_id);
                clearChat();
            }
        }
    }, false);

    $(document).on('click', '.msg_send_btn', function(e) {
        e.preventDefault();

        var message = $.trim($('.write_msg').val());

        var count = $('.messaging .inbox_chat .chat_list').length;

        if (count > 0)
        {
            var msg = '';
            msg += `<div class="outgoing_msg">
                        <div class="sent_msg">
                        <p>` + message + `</p>
                        <span class="time_date">` + formatAMPM(new Date) + `</span></div>
                    </div>`;

            $('.msg_history').append(msg);

            $('.write_msg').val('');
            $('.write_msg').focus();

            if (message != '')
            {

                socket.emit('send chat message', {
                    'from_user_id': $('meta[name=active_user]').attr('content'),
                    'send_to_user_id': getUserID(),
                    'message': message,
                    'action': 'outgoing'
                });

            }

            var div = $('.msg_history');
            div.scrollTop(div.prop('scrollHeight'));

        }

    });

    socket.on('send chat message triggered', function (data) {

        var msg = '';
            msg += `<div class="incoming_msg">
            <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
            <div class="received_msg">
              <div class="received_withd_msg">
                <p>` + data.message + `</p>
                <span class="time_date">` + formatAMPM(new Date) + `</span></div>
            </div>
          </div>`;

        $('.msg_history[data-user-id=' + parseInt(data.from_user_id) + ']').append(msg);
        var div = $('.msg_history');
        div.scrollTop(div.prop('scrollHeight'));

    });

    $(document).on('keypress',function(e) {
        if(e.which == 13) {
            $('.msg_send_btn').trigger('click');
        }
    });

});