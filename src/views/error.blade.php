<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Mobicard - Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #000;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: sans-serif;
        }

        .error-container {
            text-align: center;
            padding: 20px;
        }

        .error-icon {
            font-size: 64px;
            color: #f00;
            margin-bottom: 20px;
        }

        .error-message {
            font-size: 18px;
            color: #ccc;
            margin-bottom: 30px;
        }

        .btn-retry {
            background: #0f0;
            color: #000;
            border: none;
            padding: 10px 30px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-retry:hover {
            background: #0c0;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h2>Something went wrong</h2>
        <div class="error-message">{{ isset($error) ? $error : 'An unknown error occurred' }}</div>
        <button class="btn-retry" onclick="location.reload()">Try Again</button>
    </div>
</body>

</html>
