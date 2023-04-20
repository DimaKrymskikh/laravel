<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <style>
        body {
            margin: 0;
        }

        .container {
            max-width: 860px;
            margin: auto;
            background-color: rgb(255, 237, 213);
            color: rgb(67, 20, 7);
        }

        .main {
            padding: 0 1rem;
            margin: 2rem 0 1rem;
        }

        .header {
            text-align: center;
            background-color: rgb(254, 215, 170);
            color: rgb(154, 52, 18);
            padding: 2rem 0;
            font-size: 1.5rem;
        }

        .footer {
            color: rgb(154, 52, 18);
            padding: 2rem 0; 
            margin: 0 2rem;
            text-align: center;
            border-top: 1px rgb(254, 215, 170) solid;
        }

        .h1 {
            color: rgb(194, 65, 12);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .button {
            padding: 1rem 2rem;
            background-color: rgb(154, 52, 18);
            color: rgb(254, 215, 170);
            text-decoration: none;
            font-size: 1.2rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{ config('app.name') }}
        </div>
    
        {{ $slot }}
    
        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}. Все права защищены.
        </div>
    </div>
</body>
</html>
