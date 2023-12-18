
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="/css/styles.css">
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <title>Forgot Password</title>
    <style>
        body {
            background-image: url("images/back4.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .logo {
            position: absolute;
            top: 110px;
            left: calc(50% - 125px); /* Center the logo */
            width: 250px;
        }

        .container {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            background-color: rgba(255, 255, 255, 0.8);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color:red;
        }

        p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 10px;
        }

        input[type="email"] {
            padding: 10px;
            font-size: 16px;
            width: 90%;
            border: 2px solid red;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center; /* Align text in the email input */
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #000;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #ff0000;
        }
    </style>
</head>
<body>
    <img class="logo" src="images/back7.png" alt="Logo">
    <div class="container">
        <div class="footer">
        <h1>Login Failed</h1>
       
            <form action="../public/student_login.html">
                <label for="email">Please retry Login</label>
                <input type="submit" value="Back to Login">
            </form>
    </div>
    </div>
</body>
</html>
