<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Time Tracker</title>
    <style>
        body { 
            font-family: sans-serif; 
            margin: 0; 
            padding: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            margin: 0;
            font-size: 28px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .login-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .login-btn:hover {
            transform: translateY(-2px);
        }
        .error-msg {
            color: #dc3545;
            text-align: center;
            margin-top: 15px;
            font-weight: 500;
        }
        .success-msg {
            color: #28a745;
            text-align: center;
            margin-top: 15px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Time Tracker</h2>
            <p style="color: #666; margin-top: 10px;">Please login to continue</p>
        </div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <div id="response"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const responseDiv = document.getElementById('response');
        
        // Send credentials to server for authentication
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            data: { 
                action: 'authenticate', 
                username: username, 
                password: password 
            },
            success: function(res) {
                if (res.trim() === 'success') {
                    responseDiv.innerHTML = '<p class="success-msg">Login successful! Redirecting...</p>';
                    
                    // Redirect to dashboard after 1 second
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    responseDiv.innerHTML = '<p class="error-msg">Invalid username or password</p>';
                }
            },
            error: function() {
                responseDiv.innerHTML = '<p class="error-msg">Server error. Please try again.</p>';
            }
        });
    });
    </script>
</body>
</html>
