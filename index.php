<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Compiler</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login-style.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #4caf50;
            color: white;
            padding: 15px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            font-size: 1.1em;
            font-weight: 500;
            text-align: center;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="branding">
                <h1>Hello, Portfolio Compiler! 👋</h1>
                <p>Upload and showcase your school projects. Enhance your learning journey with ease and efficiency!</p>
            </div>
        </div>
        <div class="right-panel">
            <div class="login-box">
                <h2>Welcome Back!</h2>
                <p>Don't have an account? <a href="#" id="create-account-link">Create a new account now</a>, it's FREE! Takes less than a minute.</p>
                <form action="login.php" method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <div class="show-password">
                        <input type="checkbox" id="show-password">
                        <label for="show-password">Show Password</label>
                    </div>
                    <button type="submit" name="login">Login Now</button>
                </form>
                <a href="#" class="forgot-password">Forgot password? Click here</a>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Create Account</h2>
            <form action="register.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" id="new-password" name="password" placeholder="Password" required>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>
                <div class="show-password">
                    <input type="checkbox" id="show-password-modal">
                    <label for="show-password-modal">Show Password</label>
                </div>
                <select name="role" required>
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                </select>
                <button type="submit">Create Account</button>
            </form>
        </div>
    </div>

    <div id="popup" class="popup"></div>

    <script>
        const modal = document.getElementById('modal');
        const createAccountLink = document.getElementById('create-account-link');
        const closeButton = document.querySelector('.close-button');
        const popup = document.getElementById('popup');

        createAccountLink.addEventListener('click', function(event) {
            event.preventDefault();
            modal.style.display = 'flex';
        });

        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        document.getElementById('show-password').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            passwordField.type = this.checked ? 'text' : 'password';
        });

        document.getElementById('show-password-modal').addEventListener('change', function() {
            const newPasswordField = document.getElementById('new-password');
            const confirmPasswordField = document.getElementById('confirm-password');
            const type = this.checked ? 'text' : 'password';
            newPasswordField.type = type;
            confirmPasswordField.type = type;
        });

        // Check for success message
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            popup.textContent = urlParams.get('success');
            popup.style.display = 'block';
            setTimeout(() => {
                popup.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>