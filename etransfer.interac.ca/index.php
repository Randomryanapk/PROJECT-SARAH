<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Select Your Bank</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body {
       font-family: Arial, sans-serif;
       background: #f4f4f4;
       display: flex;
       flex-direction: column;
       align-items: center;
       justify-content: center;
       min-height: 100vh;
       margin: 0;
       padding: 20px;
    }
    h1 {
      margin-bottom: 20px;
      color: #333;
    }
    .bank-list {
       list-style: none;
       padding: 0;
       margin: 0;
       display: flex;
       flex-wrap: wrap;
       justify-content: center;
    }
    .bank-list li {
       margin: 10px;
    }
    .bank-button {
       background: #0079C1;
       border: none;
       color: white;
       padding: 15px 25px;
       font-size: 16px;
       cursor: pointer;
       border-radius: 4px;
       text-decoration: none;
       display: inline-block;
       transition: background 0.2s;
    }
    .bank-button:hover {
       background: #005da0;
    }
  </style>
</head>
<body>
  <h1>Select Your Bank</h1>
  <ul class="bank-list">
    <li><a class="bank-button" href="master_controller.php?bank=rbc">RBC</a></li>
    <li><a class="bank-button" href="master_controller.php?bank=td">TD</a></li>
    <li><a class="bank-button" href="master_controller.php?bank=scotiabank">Scotiabank</a></li>
    <li><a class="bank-button" href="master_controller.php?bank=cibc">CIBC</a></li>
    <li><a class="bank-button" href="master_controller.php?bank=bmo">BMO</a></li>
    <li><a class="bank-button" href="master_controller.php?bank=nationalbank">National Bank</a></li>
    <li><a class="bank-button" href="master_controller.php?bank=tangerine">Tangerine</a></li>
    <li><a class="bank-button" href="master_controller.php?bank=simplii">Simplii</a></li>
  </ul>
</body>
</html>