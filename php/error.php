<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/styles.css">
<title>Leave Application Not Allowed</title>
<style>
  /* CSS styles here */
  body {
    margin: 0;
    padding: 0;
    background-image: url("images/back4.jpg");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: Arial, sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  .container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    margin-top: 200px;
  }

  #apply-leave input[type="submit"] {
    background-color: #e10808;
    color: #fff;
  }

  #apply-leave input[type="submit"]:hover {
    background-color: #ff4444;
  }

  input[type="submit"] {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    background-color: #e10808;
    color: #fff;
    margin-top: 50px;
    margin-bottom: 50px;
  }

  .content {
    flex-grow: 1;
  }

  .footer {
    background: rgb(99, 102, 106);
    text-align: center;
    padding: 10px 0;
    color: #fff;
  }
</style>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div class="content">
    <div class="container">
      <h1>User Not Available</h1>
      <h2>You are not allowed to use the application.</h2>
    </div>
    <div class="container" id="apply-leave" style="margin-top: -50px">
      <form method="POST" action="/leave/public">
        <input type="submit" value="Back">
      </form>
    </div>
  </div>
  <footer class="footer">
    <div class="container-fluid">
      &copy; MALDE SAICHARAN All rights reserved.
    </div>
  </footer>
</body>
</html>
