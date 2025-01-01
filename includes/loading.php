<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page with Loading Animation</title>
    <style>
        /* Loader styles from Uiverse.io by satyamchaudharydev */
        #loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader {
            display: block;
            --height-of-loader: 4px;
            --loader-color: #0071e2;
            width: 130px;
            height: var(--height-of-loader);
            border-radius: 30px;
            background-color: rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .loader::before {
            content: "";
            position: absolute;
            background: var(--loader-color);
            top: 0;
            left: 0;
            width: 0%;
            height: 100%;
            border-radius: 30px;
            animation: moving 1s ease-in-out infinite;
        }

        @keyframes moving {
            50% {
                width: 100%;
            }

            100% {
                width: 0;
                right: 0;
                left: unset;
            }
        }
    </style>
</head>

<body>

    <!-- Loader -->
    <div id="loader">
        <div class="loader"></div>
    </div>

    <!-- Your page content here -->
    <div id="content" style="display:none;">
        <h1>Welcome to the page!</h1>
        <p>This is your content.</p>
    </div>

    <script>
        // Show loader until the page fully loads
        window.addEventListener('load', function () {
            // Hide the loader after the page is fully loaded
            document.getElementById('loader').style.display = 'none';
            // Display the content
            document.getElementById('content').style.display = 'block';
        });
    </script>

</body>

</html>