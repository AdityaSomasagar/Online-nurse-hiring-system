<head>
    <!-- Include Bootstrap and Font Awesome if not already -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        .custom-navbar {
            background: linear-gradient(to right, #4c6ef5, #5f8df9);
            padding: 15px 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .custom-navbar .navbar-brand {
            font-size: 28px;
            font-weight: 600;
            color: #fff;
            transition: color 0.3s ease-in-out;
        }

        .custom-navbar .navbar-brand:hover {
            color: #e0e0e0;
        }

        .custom-navbar .nav-link {
            color: #ffffff;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        .custom-navbar .nav-link:hover {
            color: #ffdd57;
            text-shadow: 0px 0px 5px rgba(255,255,255,0.5);
        }

        .custom-navbar .nav-item.active .nav-link {
            font-weight: 700;
            border-bottom: 2px solid #fff;
        }

        @media (max-width: 768px) {
            .custom-navbar .navbar-brand {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
        <a class="navbar-brand" href="index.php">
            Online Nurse Connekt
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <ul class="navbar-nav text-center">
                <li class="nav-item active mx-2">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="admin/index.php">Admin</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                </li>   
            </ul>
        </div>
    </nav>
</header>
</body>
