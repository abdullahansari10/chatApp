<?php
include('./required/config.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('location: login.php');
    exit();
}

$username = $_SESSION['username'];
$users = $conn->query("SELECT username FROM register WHERE username != '$username'");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <?php require("./required/css.php") ?>
</head>

<body>
    <?php require("./required/header.php") ?>
    <div class="container-fluid main">
        <div class="row h-100">
            <div class="col-lg-3 border-start border-end sideBar px-1">
                <div class="pages bg-success">
                    <div class="username-index">
                        <?php echo "Hello " . $username; ?>
                    </div>
                    <div class="dropDown">
                        <button class="dropDownToggle">
                            <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                        </button>
                        <div class="dropDownMenu dropDownContent">
                            <a href="login.php">
                                <div class="login-index">Login page</div>
                            </a>
                            <a href="registration.php">
                                <div class="reg-index">registration page</div>
                            </a>
                            <a href="logout.php">
                                <div class="reg-index">logout</div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="allUsers">
                    <div id="receiver">
                        <ol>
                            <?php while ($user = $users->fetch_assoc()): ?>
                                <li class="users" data-username="<?= $user['username'] ?>">
                                    <?= $user['username'] ?>
                                </li>
                            <?php endwhile; ?>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 px-1 h-100" style="padding: 0;">
                <h4 class="text-center text-white bg-success p-2 border position-fixed w-75"
                    style="z-index: 999; top: 0;">
                    ChatApp
                </h4>
                <p class="mt-3 mb-4">How can I assist you?</p>

                <div class="chatMsg" style="overflow-y: auto;"></div>

                <input type="text" id="input" placeholder="say something..." autocomplete="auto">
                <button class="bg-success text-white" id="send">Send</button>
            </div>
        </div>
    </div>

    <?php require("./required/footer.php") ?>
    <?php require("./required/js.php") ?>


    <script>
        $('.dropDownToggle').click(function(e) {
            e.stopPropagation();
            $('.dropDownContent').toggle()
        });

        $(document).click(function() {
            $('.dropDownMenu').hide();
        });

        function getCurrentTime() {
            return new Date().toLocaleTimeString([], {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        // $('.users').click(function(e){
        //     e.stopPropagation()
        //     console.log('receiver: ', $(this).data('username'));
        // })
        let selectedReceiver = ""
        let fetchInterval = ""
        $(document).on('click', '.users', function() {
            selectedReceiver = $(this).data('username');
            $(".users").removeClass("activeUser");
            $(this).addClass('activeUser');

            // Immediately fetch messages for selected user
            fetchMessages();

            // Clear old interval if any
            if (fetchInterval) {
                clearInterval(fetchInterval);
            }

            // Start a new interval to fetch messages every 5 seconds
            fetchInterval = setInterval(fetchMessages, 5000);
        });

        function message() {
            var input = $('#input').val().trim();
            console.log('selectedReceiver ', selectedReceiver);
            // var receiver = $('#receiver').val().trim(); // Get from input or dropdown
            var currentTime = getCurrentTime();
            console.log('input ', input);
            if (input && selectedReceiver) {
                $.ajax({
                    url: "data.php",
                    type: "POST",
                    data: {
                        input_data: input,
                        receiver: selectedReceiver
                    },
                    success: function(response) {
                        console.log("Message sent:", response);
                        // console.log("Message sent:", response, input, selectedReceiver);
                        // append messages to chatMsg if needed
                    }
                });
                // $('.chatMsg').append( `<
                //     div class="userMsg">
                //     ${input} <span id="userTime"> ${currentTime}</span> </div>` );
                // $('.chatMsg').append( `
                // <div class="botMsg">
                //     <span id="botTime">${currentTime} </span>
                // </div> <br>`);
                $('#input').val(""); // Clear input
                fetchMessages()
                scrollToBottom();

            }
        }


        function fetchMessages() {
            $.ajax({
                url: 'fetch_messages.php?receiver=' + encodeURIComponent(selectedReceiver),
                method: 'GET',
                success: function(response) {
                    console.log("response:\n", response);
                    $('.chatMsg').html(response);
                    scrollToBottom();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to fetch messages:', error);
                }
            });
        }

        function scrollToBottom() {
            $('.chatMsg').scrollTop($('.chatMsg')[0].scrollHeight);
        }

        // Event handlers
        $('#input').on("keypress", function(e) {
            if (e.key == "Enter") {
                e.preventDefault();
                message();
            }
        });

        $('#send').click(message);

        // Fetch messages on load and every 5 seconds
        fetchMessages();
        // setInterval(fetchMessages, 50000);
        setInterval(fetchMessages, 5000);
    </script>
</body>