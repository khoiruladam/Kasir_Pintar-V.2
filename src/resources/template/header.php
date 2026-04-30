<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kasir Pintar</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">

    <style>
        :root {
            --primary: #6366f1;
            --primary-lt: #ede9fe;
            --nav-bg: #1e293b;
            --body-bg: #f0f0f7;
            --card-radius: 20px;
            --header-h: 62px;
            --bnav-h: 72px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--body-bg);
            padding-top: var(--header-h);
            padding-bottom: calc(var(--bnav-h) + 8px);
            -webkit-font-smoothing: antialiased;
        }

        /* =====================
           HEADER
        ===================== */
        .header-main {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-h);
            background: #fff;
            border-bottom: 1px solid #ececf5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 18px;
            z-index: 1030;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 9px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-icon {
            width: 34px;
            height: 34px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-icon i {
            color: #fff;
            font-size: 16px;
        }

        .brand-text {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -0.3px;
            color: var(--primary);
        }

        .brand-text span {
            color: #1e293b;
        }

        /* Avatar + dropdown */
        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-meta {
            text-align: right;
        }

        .user-meta .um-label {
            font-size: 10px;
            color: #94a3b8;
            line-height: 1.2;
        }

        .user-meta .um-name {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            max-width: 110px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .avatar-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid var(--primary-lt);
            object-fit: cover;
            cursor: pointer;
            display: block;
            flex-shrink: 0;
        }

        .dropdown-menu {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(99, 102, 241, .15);
            min-width: 160px;
            margin-top: 10px !important;
            padding: 6px;
        }

        .dropdown-item {
            font-size: 13px;
            font-weight: 500;
            padding: 10px 14px;
            border-radius: 10px;
        }

        .dropdown-item:hover {
            background: var(--primary-lt);
            color: var(--primary);
        }

        .dropdown-item.text-danger:hover {
            background: #fff0f0;
            color: #ef4444;
        }

        /* =====================
           BOTTOM NAV
        ===================== */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: var(--bnav-h);
            background: var(--nav-bg);
            border-radius: 24px 24px 0 0;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0 8px 6px;
            z-index: 1040;
            max-width: 600px;
            margin: 0 auto;
        }

        .nav-item-custom {
            text-decoration: none;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            flex: 1;
            padding: 8px 4px 4px;
            position: relative;
            -webkit-tap-highlight-color: transparent;
            transition: color .2s;
        }

        .nav-item-custom i {
            font-size: 20px;
            line-height: 1;
        }

        .nav-item-custom span {
            font-size: 10px;
            font-weight: 600;
        }

        .nav-item-custom.active {
            color: #fff;
        }

        .nav-item-custom.active::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 20px;
            height: 3px;
            background: var(--primary);
            border-radius: 0 0 4px 4px;
        }

        /* =====================
           DESKTOP (≥992px)
        ===================== */
        @media (min-width: 992px) {
            :root {
                --header-h: 68px;
            }

            body {
                padding-bottom: 32px;
            }

            .header-main {
                padding: 0 32px;
            }

            .bottom-nav {
                position: static;
                height: auto;
                background: transparent;
                border-radius: 0;
                padding: 0;
                gap: 4px;
                flex-shrink: 0;
                max-width: none;
                margin: 0;
            }

            .nav-item-custom {
                color: #64748b;
                padding: 8px 14px;
                border-radius: 10px;
                flex: none;
                flex-direction: row;
                gap: 6px;
            }

            .nav-item-custom i {
                font-size: 16px;
            }

            .nav-item-custom span {
                font-size: 13px;
            }

            .nav-item-custom.active {
                color: var(--primary);
                background: var(--primary-lt);
            }

            .nav-item-custom.active::before {
                display: none;
            }

            /* Reorder desktop: logo | nav | avatar */
            .bottom-nav {
                margin-left: auto;
                margin-right: 16px;
            }
        }
    </style>
</head>

<body>

    <header class="header-main">
        <a href="../resources/views/admin/dashboard.php" class="brand-logo">
            <div class="brand-icon"><i class="bi bi-cart-fill"></i></div>
            <span class="brand-text">Kasir<span>Pintar</span></span>
        </a>

        <?php
        $current_page = basename($_SERVER['PHP_SELF']);
        $level_user = strtolower($_SESSION['Level'] ?? '');
        ?>

        <nav class="bottom-nav" aria-label="Navigasi utama">

            <!-- 1. HOME (Tampil untuk semua role) -->
            <a href="dashboard.php" class="nav-item-custom <?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i><span>Home</span>
            </a>

            <?php if ($level_user === 'petugas' || $level_user === 'kasir'): ?>

                <a href="penjualan.php" class="nav-item-custom <?= $current_page == 'penjualan.php' ? 'active' : '' ?>">
                    <i class="bi bi-cart-fill"></i><span>Kasir</span>
                </a>

                <a href="pelanggan.php" class="nav-item-custom <?= $current_page == 'pelanggan.php' ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i><span>Pelanggan</span>
                </a>

            <?php elseif (in_array($level_user, ['administrator', 'owner', 'manager'])): ?>

                <a href="stok.php" class="nav-item-custom <?= $current_page == 'stok.php' ? 'active' : '' ?>">
                    <i class="bi bi-box2-fill"></i><span>Stok</span>
                </a>

                <a href="produk.php" class="nav-item-custom <?= $current_page == 'produk.php' ? 'active' : '' ?>">
                    <i class="bi bi-tags-fill"></i><span>Produk</span>
                </a>

                <a href="user.php"
                    class="nav-item-custom <?= in_array($current_page, ['users.php', 'tambah_user.php', 'edit_user.php']) ? 'active' : '' ?>">
                    <i class="bi bi-person-badge-fill"></i><span>Users</span>
                </a>

            <?php endif; ?>
        </nav>

        <div class="header-right">
            <div class="user-meta d-none d-sm-block">
                <div class="um-label">Logged in as</div>
                <div class="um-name"><?= htmlspecialchars($_SESSION['Username'] ?? 'User') ?></div>
            </div>
            <div class="dropdown">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['Username'] ?? 'U') ?>&background=6366f1&color=fff&size=72"
                    class="avatar-btn dropdown-toggle" data-bs-toggle="dropdown" alt="Avatar" width="36" height="36">
            </div>
        </div>
    </header>

    <div class="container">