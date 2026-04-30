<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Kasir Pintar</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #6366f1;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        body {
            /* Background dengan filter blur samar */
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                url('image/bg.jpeg') no-repeat center center/cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            padding: 20px;
        }

        /* Login Card Glassmorphism */
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            animation: fadeInScale 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .login-logo {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            display: flex;
            justify-content: center;
            align-items: center;
            background: var(--primary-color);
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
            color: #fff;
            font-size: 2rem;
        }

        .text-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
            margin-left: 5px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 16px;
            padding: 12px 15px 12px 45px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 68%;
            transform: translateY(-50%);
            color: #64748b;
            z-index: 10;
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            border: none;
            border-radius: 16px;
            font-weight: 700;
            padding: 14px;
            color: #fff;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
            filter: brightness(1.1);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 10px;
            border-radius: 12px;
            font-size: 0.85rem;
            text-align: center;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>

<body>

    <div class="login-card text-center">
        <div class="login-logo">
            <i class="bi bi-cart-fill"></i>
        </div>

        <h3 class="mb-1 text-gradient">Kasir Pintar</h3>
        <p class="text-muted small mb-4">Silakan masuk untuk mengelola toko</p>

        <?php if (isset($_GET['pesan'])): ?>
            <div class="error-msg">
                <i class="bi bi-exclamation-circle me-1"></i>
                <?php
                if ($_GET['pesan'] == "password_salah")
                    echo "Password yang Anda masukkan salah.";
                else if ($_GET['pesan'] == "user_tidak_ada")
                    echo "Username tidak terdaftar.";
                else if ($_GET['pesan'] == "belum_login")
                    echo "Anda harus login terlebih dahulu.";
                ?>
            </div>
        <?php endif; ?>

        <form action="../auth/proses_login.php" method="POST">
            <div class="input-group-custom text-start">
                <label class="form-label">Username</label>
                <i class="bi bi-person input-icon"></i>
                <input type="text" class="form-control" name="username" placeholder="Masukkan username" required
                    autocomplete="off">
            </div>

            <div class="input-group-custom text-start">
                <label class="form-label">Password</label>
                <i class="bi bi-lock input-icon"></i>
                <input type="password" class="form-control" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-login w-100 mt-2">
                Masuk Sekarang <i class="bi bi-arrow-right-short ms-1"></i>
            </button>
        </form>

        <p class="mt-4 text-muted" style="font-size: 11px;">
            &copy; Kasir Pintar <br> Developed by <b>Adam</b>
        </p>
    </div>

</body>

</html>