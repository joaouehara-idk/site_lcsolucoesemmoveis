<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> | Admin LC Soluções em Móveis</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --accent: #1A1714;
            --accent-deep: #1A1714;
            --bg: #F6F1EB;
            --bg-alt: #EDE7DF;
            --surface: #FFFFFF;
            --ink: #1A1714;
            --ink-soft: #4A4540;
            --ink-muted: #8A8580;
            --ink-faint: #B5B0AA;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--ink);
            margin: 0;
        }
        .admin-layout { display: flex; min-height: 100vh; }
        .sidebar {
            width: 260px;
            background: var(--ink);
            padding: 32px 24px;
            position: sticky;
            top: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-brand-icon {
            width: 36px;
            height: 36px;
            background: var(--bg);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'DM Serif Display', serif;
            font-size: 15px;
            color: var(--ink);
        }
        .sidebar-brand h2 {
            font-family: 'DM Serif Display', serif;
            color: #F6F1EB;
            font-size: 1.1rem;
            font-weight: 400;
        }
        .sidebar nav { flex: 1; }
        .sidebar nav ul { list-style: none; padding: 0; }
        .sidebar nav ul li { margin-bottom: 4px; }
        .sidebar nav ul li a {
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 10px;
            transition: all 0.25s ease;
            font-size: 13px;
            font-weight: 500;
        }
        .sidebar nav ul li a i { width: 18px; text-align: center; font-size: 14px; }
        .sidebar nav ul li a:hover {
            background: rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.8);
        }
        .sidebar nav ul li a.active {
            background: rgba(246,241,235,0.12);
            color: var(--bg);
        }
        .sidebar-footer {
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
            margin-top: auto;
        }
        .main-content { flex: 1; padding: 40px 48px; overflow-y: auto; }
        h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 2rem;
            font-weight: 400;
            color: var(--ink);
            margin-bottom: 32px;
        }
        .card {
            background: var(--surface);
            padding: 28px;
            border-radius: 16px;
            border: 1px solid rgba(26,23,20,0.06);
            margin-top: 20px;
            box-shadow: 0 1px 3px rgba(26,23,20,0.04);
        }
        .btn-add {
            background: var(--ink);
            color: #F6F1EB;
            padding: 10px 22px;
            border-radius: 100px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-add:hover {
            background: var(--ink);
            color: var(--bg);
            transform: translateY(-1px);
        }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { text-align: left; padding: 14px 16px; border-bottom: 1px solid rgba(26,23,20,0.06); font-size: 13px; }
        table th {
            color: var(--ink-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.06em;
        }
        table td { color: var(--ink-soft); }
        .action-btns { display: flex; gap: 12px; }
        .btn-edit { color: var(--accent); text-decoration: none; font-weight: 500; font-size: 13px; }
        .btn-edit:hover { color: var(--accent-deep); }
        .btn-delete { color: #B84A4A; text-decoration: none; background: none; border: none; cursor: pointer; padding: 0; font-family: inherit; font-size: 13px; font-weight: 500; }
        .btn-delete:hover { color: #8C3A3A; }

        /* Form Styles */
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 6px; color: var(--ink-soft); font-size: 13px; font-weight: 500; }
        .form-control {
            width: 100%; padding: 12px 16px;
            border-radius: 10px;
            border: 1.5px solid rgba(26,23,20,0.08);
            background: var(--bg);
            color: var(--ink);
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(184,147,90,0.08);
            background: var(--surface);
        }
        .form-control::placeholder { color: var(--ink-faint); }
        .btn-save {
            background: var(--ink);
            color: #F6F1EB;
            border: none;
            padding: 12px 30px;
            border-radius: 100px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Inter', sans-serif;
        }
        .btn-save:hover {
            background: var(--accent);
            color: var(--ink);
            transform: translateY(-1px);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238A8580' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        textarea.form-control { min-height: 120px; resize: vertical; }
    </style>
</head>
<body>
    <div class="admin-layout">
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">LC</div>
                <h2>Admin</h2>
            </div>
            <nav>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/admin/dashboard"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/blog"><i class="fas fa-blog"></i> Blog</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/portfolio"><i class="fas fa-images"></i> Portfólio</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/contatos"><i class="fas fa-inbox"></i> Contatos</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/usuarios"><i class="fas fa-users"></i> Usuários</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/admin/emails-autorizados"><i class="fas fa-envelope"></i> Emails Autorizados</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>/" target="_blank"><i class="fas fa-external-link-alt"></i> Ver Site</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/logout"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                </ul>
            </div>
        </aside>
        <main class="main-content">
