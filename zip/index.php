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
    
    <!-- Mobile Header -->
    <div class="mobile-header d-md-none bg-success text-white p-3 d-flex justify-content-between align-items-center">
        <button class="btn btn-success sidebar-toggle" type="button">
            <i class="fas fa-bars"></i>
        </button>
        <h5 class="mb-0">ChatApp</h5>
        <div class="dropdown">
            <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-user"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text">Hello <?php echo $username; ?></span></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="login.php">Login page</a></li>
                <li><a class="dropdown-item" href="registration.php">Registration page</a></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="container-fluid main">
        <div class="row h-100">
            <!-- Sidebar -->
            <div class="col-md-4 col-lg-3 sidebar-container px-0">
                <div class="sidebar-overlay d-md-none"></div>
                <div class="sidebar">
                    <!-- Desktop Header -->
                    <div class="pages bg-success d-none d-md-flex">
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
                                    <div class="reg-index">Registration page</div>
                                </a>
                                <a href="logout.php">
                                    <div class="reg-index">Logout</div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Users List -->
                    <div class="allUsers">
                        <div class="users-header d-md-none bg-light p-2 border-bottom">
                            <h6 class="mb-0">Select User to Chat</h6>
                        </div>
                        <div id="receiver">
                            <div class="users-list">
                                <?php while ($user = $users->fetch_assoc()): ?>
                                    <div class="user-item" data-username="<?= $user['username'] ?>">
                                        <div class="user-avatar">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name"><?= $user['username'] ?></div>
                                            <div class="user-status">Online</div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chat Area -->
            <div class="col-md-8 col-lg-9 chat-container px-0">
                <!-- Chat Header -->
                <div class="chat-header bg-success text-white p-3 d-none d-md-block">
                    <h5 class="mb-0">ChatApp</h5>
                </div>
                
                <!-- Chat Messages -->
                <div class="chat-content">
                    <div class="welcome-message text-center p-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Select a user to start chatting</h5>
                        <p class="text-muted">Choose someone from the sidebar to begin your conversation</p>
                    </div>
                    <div class="chatMsg"></div>
                </div>
                
                <!-- Message Input -->
                <div class="message-input-container">
                    <div class="input-group">
                        <input type="text" id="input" class="form-control" placeholder="Type your message..." autocomplete="off">
                        <button class="btn btn-success" id="send" type="button">
                            <i class="fas fa-paper-plane"></i>
                            <span class="d-none d-sm-inline ms-1">Send</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require("./required/footer.php") ?>
    <?php require("./required/js.php") ?>

    <script>
        // Sidebar toggle for mobile
        $('.sidebar-toggle').click(function() {
            $('.sidebar-container').addClass('show');
            $('.sidebar-overlay').addClass('show');
        });

        $('.sidebar-overlay').click(function() {
            $('.sidebar-container').removeClass('show');
            $('.sidebar-overlay').removeClass('show');
        });

        // Desktop dropdown
        $('.dropDownToggle').click(function(e) {
            e.stopPropagation();
            $('.dropDownContent').toggle();
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

        let selectedReceiver = "";
        let fetchInterval = "";

        $(document).on('click', '.user-item', function() {
            selectedReceiver = $(this).data('username');
            $(".user-item").removeClass("active");
            $(this).addClass('active');

            // Hide welcome message and show chat
            $('.welcome-message').hide();
            $('.chatMsg').show();

            // Close sidebar on mobile after selection
            if ($(window).width() < 768) {
                $('.sidebar-container').removeClass('show');
                $('.sidebar-overlay').removeClass('show');
            }

            // Fetch messages
            fetchMessages();

            if (fetchInterval) {
                clearInterval(fetchInterval);
            }

            fetchInterval = setInterval(fetchMessages, 5000);
        });

        function message() {
            var input = $('#input').val().trim();
            var currentTime = getCurrentTime();
            
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
                    }
                });
                
                $('#input').val("");
                fetchMessages();
                scrollToBottom();
            }
        }

        function fetchMessages() {
            if (!selectedReceiver) return;
            
            $.ajax({
                url: 'fetch_messages.php?receiver=' + encodeURIComponent(selectedReceiver),
                method: 'GET',
                success: function(response) {
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

        // Auto-resize input on mobile
        $(window).resize(function() {
            if ($(window).width() < 768) {
                $('.sidebar-container').removeClass('show');
                $('.sidebar-overlay').removeClass('show');
            }
        });

        setInterval(fetchMessages, 50000);
    </script>
</body>
</html>
