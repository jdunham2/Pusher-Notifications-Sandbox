<!DOCTYPE html>
<html>
<head>
    <title>Real-Time Laravel with Pusher</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <link href="//fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400,600,700,200italic,300italic"
          rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="http://d3dhju7igb20wy.cloudfront.net/assets/0-4-0/all-the-things.css"/>

    <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://js.pusher.com/4.2/pusher.min.js"></script>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">

    <script>
        // Ensure CSRF token is sent with AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Added Pusher logging
        Pusher.log = function (msg) {
            console.log(msg);
        };
    </script>
</head>
<body>

<div class="stripe no-padding-bottom numbered-stripe">
    <div class="fixed wrapper">
        <ol class="strong" start="1">
            <li>
                <div class="hexagon"></div>
                <h2><b>Real-Time Notifications</b>
                    <small>Let users know what's happening.</small>
                </h2>
            </li>
        </ol>
    </div>
</div>

<section class="blue-gradient-background splash">
    <div class="container center-all-container">
        <input type="text" id="notify_text" name="notify_text"
               placeholder="What's the notification?" minlength="3" maxlength="140" required/>
        <button type="submit" id="notify_form">Go</button>
    </div>
</section>

<script type="text/javascript">

    var options = {
        cluster: "{{ env('PUSHER_APP_CLUSTER') }}"
    };

    var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', options);

    var channel = pusher.subscribe('notifications');
    channel.bind('new-notification', function (data) {
        showNotification(data);
    });

    function notifyInit() {
        // set up form submission handling
        $('#notify_form').click(notifySubmit);
        $('#notify_text').keyup(notifyTyping);
    }

    // Handle the form submission
    function notifySubmit() {
        var notifyText = $('#notify_text').val();
        if (notifyText.length < 3) {
            return;
        }

        socket_id = pusher.connection.socket_id;
        console.log(socket_id);

        // Build POST data and make AJAX request
        var data = {notify_text: notifyText, socket_id: socket_id};
        $.post('/notifications/notify', data).success(notifySuccess);

        // Ensure the normal browser event doesn't take place
        return false;
    }

    function notifyTyping() {
        var notifyText = "User is typing";

        socket_id = pusher.connection.socket_id;
        console.log(socket_id);

        // Build POST data and make AJAX request
        var data = {notify_text: notifyText, socket_id: socket_id};
        $.post('/notifications/notify', data).success(notifySuccess);

        // Ensure the normal browser event doesn't take place
        return false;
    }

    // Handle the success callback
    function notifySuccess() {
        console.log('notification submitted');
    }

    // Use toastr to show the notification
    function showNotification(data) {
        console.log(data);

        // TODO: get the text from the event data
        var text = data.message;
        // TODO: use the text in the notification
        toastr.success(text, null, {"positionClass": "toast-bottom-left"});
    }

    $(notifyInit);

</script>

</body>
</html>
