<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Modern Mirip WhatsApp</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <!-- <link href="https://fonts.googleapis.com/css?family=Segoe+UI:400,700&display=swap" rel="stylesheet">
     -->
    <style>
        /* Responsive navbar atas & bawah */
@media (max-width: 992px) {
    .chat-top-bar,
    .chat-input-area {
        padding: 8px 8px;
        font-size: 0.95em;
    }
    .chat-current-user .username {
        font-size: 1em;
    }
    .chat-actions .icon-button,
    .send-button {
        width: 38px;
        height: 38px;
        font-size: 1.1em;
    }
    .chat-current-user .avatar {
        width: 32px;
        height: 32px;
        margin-right: 8px;
    }
    .chat-input-area {
        gap: 6px;
    }
    .message-input {
        font-size: 0.95em;
        padding: 8px 12px;
    }
}
@media (max-width: 576px) {
    .chat-top-bar,
    .chat-input-area {
        padding: 5px 4px;
        font-size: 0.9em;
    }
    .chat-current-user .avatar {
        width: 28px;
        height: 28px;
    }
    .send-button,
    .chat-actions .icon-button {
        width: 32px;
        height: 32px;
        font-size: 1em;
    }
}

@media (max-width: 576px) {
    .chat-top-bar,
    .chat-input-area {
        padding: 4px 2vw;
        font-size: 0.92em;
        min-height: 48px;
        height: auto;
    }
    .chat-current-user .avatar {
        width: 26px;
        height: 26px;
        margin-right: 6px;
    }
    .chat-current-user .username {
        font-size: 0.97em;
        max-width: 30vw;
    }
    .chat-actions .icon-button,
    .send-button {
        width: 30px;
        height: 30px;
        font-size: 1em;
        min-width: 30px;
        min-height: 30px;
    }
    .mute-toggle-wrapper {
        margin-left: 8px;
        gap: 4px;
    }
    .switch {
        width: 28px;
        height: 16px;
    }
    .slider:before {
        height: 10px;
        width: 10px;
        left: 2px;
        bottom: 3px;
    }
    input:checked + .slider:before {
        transform: translateX(10px);
    }
    .chat-input-area {
        gap: 4px;
    }
    .message-input {
        font-size: 0.92em;
        padding: 7px 10px;
        min-height: 32px;
    }
}
        /* Global box-sizing for consistent layout */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        :root {
            /* Default colors (akan di-override oleh data-theme) */
            --sidebar-width: 350px;
            --primary-color: #008069; 
            --secondary-color: #005f54; 
            
            --body-bg: #0b141a; 
            --chat-bg-image: url('https://c4.wallpaperflare.com/wallpaper/639/699/747/emoticon-charms-bw-wallpaper-preview.jpg'); 
            --chat-bubble-received: #202c33;
            --chat-bubble-sent: #005c4b;
            --text-color-light: #e9edef; 
            --text-color-muted: #8696a0; 
            --border-color: rgba(255, 255, 255, 0.08); 

            --sidebar-bg: #111b21;
            --sidebar-header-bg: #202c33;
            --search-input-bg: #202c33;
            --search-input-text: var(--text-color-light);
            --search-input-placeholder: var(--text-color-muted);
            --chat-list-item-hover-bg: rgba(255, 255, 255, 0.05);

            --chat-top-bar-bg: #202c33;
            --chat-input-area-bg: #202c33;
            --message-input-bg: #2a3942;
            --message-input-text: var(--text-color-light);
            --message-input-placeholder: var(--text-color-muted);

            /* Toggle colors */
            --toggle-bg-off: #525f69;
            --toggle-bg-on: var(--primary-color);
            --toggle-circle: #e9edef;
            
            /* Notification */
            --notification-bg: #ff3b30;
            --notification-text: #ffffff;
        }

        /* --- Dark Mode (Default) --- */
        body[data-theme="dark"] {
            --body-bg: #0b141a;
            --chat-bg-image: url('https://c4.wallpaperflare.com/wallpaper/639/699/747/emoticon-charms-bw-wallpaper-preview.jpg');
            --chat-bubble-received: #202c33;
            --chat-bubble-sent: #005c4b;
            --text-color-light: #e9edef;
            --text-color-muted: #8696a0;
            --border-color: rgba(255, 255, 255, 0.08);

            --sidebar-bg: #111b21;
            --sidebar-header-bg: #202c33;
            --search-input-bg: #202c33;
            --search-input-text: var(--text-color-light);
            --search-input-placeholder: var(--text-color-muted);
            --chat-list-item-hover-bg: rgba(255, 255, 255, 0.05);

            --chat-top-bar-bg: #202c33;
            --chat-input-area-bg: #202c33;
            --message-input-bg: #2a3942;
            --message-input-text: var(--text-color-light);
            --message-input-placeholder: var(--text-color-muted);
        }

        /* --- White Mode --- */
        body[data-theme="white"] {
            --body-bg: #e0e0e0; 
            --chat-bg-image: url('https://media.istockphoto.com/id/1403848173/vector/vector-online-chatting-pattern-online-chatting-seamless-background.jpg?s=612x612&w=0&k=20&c=W3O15mtJiNlJuIgU6S9ZlnzM_yCE27eqwTCfXGYwCSo='); 
            --chat-bubble-received: #ffffff; 
            --chat-bubble-sent: #dcf8c6; 
            --text-color-light: #333333; 
            --text-color-muted: #666666; 
            --border-color: #e0e0e0; 

            --sidebar-bg: #f8f8f8;
            --sidebar-header-bg: #ededed;
            --search-input-bg: #ffffff;
            --search-input-text: #333333;
            --search-input-placeholder: #999999;
            --chat-list-item-hover-bg: #f0f0f0;

            --chat-top-bar-bg: #ededed;
            --chat-input-area-bg: #f0f0f0;
            --message-input-bg: #ffffff;
            --message-input-text: #333333;
            --message-input-placeholder: #999999;
        }

        /* PERBAIKAN UTAMA: Pastikan html dan body mengambil 100% tinggi viewport */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Mencegah scrollbody utama */
            font-family: 'Noto Sans', sans-serif;
        }

        body {
            background-color: var(--body-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .chat-app-wrapper {
            margin-left: 56px;
            width: calc(100vw - 56px);
            height: 100vh;
            display: flex;
            background-color: transparent;
            box-shadow: none;
            border-radius: 0;
            overflow: hidden;
            position: relative;
        }

        @media (max-width: 992px) {
            .chat-app-wrapper {
                margin-left: 0;
                width: 100vw;
            }
        }

        /* Sidebar (Chat List) */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            color: var(--text-color-light);
            display: flex;
            flex-direction: column;
            flex-shrink: 0; /* Penting agar sidebar tidak mengecil */
            position: relative;
            transition: transform 0.3s ease-in-out, background-color 0.3s ease, border-color 0.3s ease;
            z-index: 1000;
            height: 100%; /* Pastikan sidebar mengambil tinggi penuh */
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--sidebar-header-bg);
            flex-wrap: nowrap;
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .sidebar-header > * {
            min-width: 0; /* Supaya child bisa shrink */
        }

        #mainUserSelect {
            min-width: 0;
            max-width: 150px;
            width: 100%;
            flex-shrink: 1;
            flex-grow: 1;
            margin-left: 10px;
            margin-right: 0;
        }

        .sidebar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
            flex-shrink: 0;
        }

        .sidebar-header .user-profile-icon {
            flex-shrink: 0;
            margin-right: 0;
        }

        @media (max-width: 992px) {
            .close-sidebar-button {
                display: block !important;
                position: absolute;
                right: 15px;
                top: 15px;
                font-size: 2em;
                background: none;
                border: none;
                color: var(--text-color-light);
                z-index: 2100;
            }
            .sidebar-header {
                gap: 0;
            }
            #mainUserSelect {
                max-width: 100px;
            }
        }

        .sidebar-header h3 {
            color: var(--text-color-light);
            margin: 0;
            font-size: 1.2em;
            font-weight: 700;
        }
        
        .sidebar-header .user-profile-icon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
            cursor: pointer;
            flex-shrink: 0;
        }
        .sidebar-header .user-profile-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-header .sidebar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }

        .theme-toggle-switch {
            /* No special margins here, rely on gap from sidebar-actions */
        }

        .sidebar-header .icon-button {
            background: none;
            border: none;
            color: var(--text-color-muted);
            font-size: 1.2em;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .sidebar-header .icon-button:hover {
            color: var(--text-color-light);
        }

        .sidebar-header h3 .close-sidebar-button {
            display: none;
            background: none;
            border: none;
            color: var(--text-color-light);
            font-size: 1.8em;
            cursor: pointer;
        }

        .chat-search-bar {
            padding: 15px 20px;
            background-color: var(--sidebar-bg);
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s ease, border-color 0.3s ease;
            flex-shrink: 0;
        }

        .chat-search-bar .form-control {
            background-color: var(--search-input-bg);
            border: none;
            color: var(--search-input-text);
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 0.9em;
            height: auto;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        .chat-search-bar .form-control::placeholder {
            color: var(--search-input-placeholder);
        }

        .chat-list-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1; /* Penting agar list mengisi sisa ruang */
            overflow-y: auto; /* Mengaktifkan scroll pada list kontak */
            height: 0; /* Wajib untuk flex-grow bekerja dengan overflow */
            min-height: 0; /* Wajib untuk flex-grow bekerja dengan overflow */
        }
        /* Scrollbar Styling for Webkit browsers (Chrome, Safari) */
        .chat-list-menu::-webkit-scrollbar, .chat-messages-area::-webkit-scrollbar {
            width: 6px;
        }
        .chat-list-menu::-webkit-scrollbar-track {
            background: var(--sidebar-bg); /* Use sidebar background for sidebar scrollbar track */
        }
        .chat-messages-area::-webkit-scrollbar-track {
             background: transparent; /* Main chat scrollbar track is transparent */
        }

        .chat-list-menu::-webkit-scrollbar-thumb, .chat-messages-area::-webkit-scrollbar-thumb {
            background-color: #333d42;
            border-radius: 10px;
        }
        body[data-theme="white"] .chat-list-menu::-webkit-scrollbar-thumb,
        body[data-theme="white"] .chat-messages-area::-webkit-scrollbar-thumb {
            background-color: #aaa;
        }

        .chat-list-menu li {
            padding: 0;
        }

        .chat-list-item {
            color: var(--text-color-light);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .chat-list-item:last-child {
            border-bottom: none;
        }

        .chat-list-item:hover, .chat-list-item.active {
            background: var(--chat-list-item-hover-bg);
        }

        .chat-list-item .avatar {
            width: 49px;
            height: 49px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
            flex-shrink: 0;
        }
        .chat-list-item .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-list-item .avatar.online::after {
            content: '';
            position: absolute;
            bottom: 0px; 
            right: 0px; 
            width: 12px;
            height: 12px;
            background-color: #4CAF50; 
            border-radius: 50%;
            border: 2px solid var(--sidebar-bg);
        }

        .chat-list-item .chat-info {
            flex-grow: 1;
            overflow: hidden;
        }

        .chat-list-item .chat-name {
            font-weight: 400;
            font-size: 1.05em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-color-light);
        }

        .chat-list-item .last-message {
            font-size: 0.9em;
            color: var(--text-color-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-list-item .chat-time {
            font-size: 0.75em;
            color: var(--text-color-muted);
            margin-left: 10px;
            flex-shrink: 0;
            font-weight: 300;
        }
        
        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: 12px;
            right: 20px;
            background-color: var(--notification-bg);
            color: var(--notification-text);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.7em;
            font-weight: bold;
        }

        /* Main Chat Content (Area Obrolan Utama) */
        .main-chat-content {
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width)); /* Akan di override di media query */
            background-image: var(--chat-bg-image); 
            background-size: contain; 
            background-repeat: repeat; 
            background-position: center;
            background-attachment: fixed; 
            flex-grow: 1; /* Pastikan mengambil sisa ruang horizontal */
            height: 100%; /* Penting untuk Flexbox agar children-nya bisa mengisi tinggi */
            min-height: 0; 
            transition: background-image 0.3s ease; 
        }

        /* Top Bar (Header Obrolan Aktif) */
        .chat-top-bar {
            padding: 10px 20px; 
            background-color: var(--chat-top-bar-bg); 
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text-color-light);
            flex-shrink: 0; /* Penting agar tidak mengecil saat ada banyak pesan */
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        .toggle-sidebar-btn {
            background: none;
            border: none;
            color: var(--text-color-light);
            font-size: 1.8em;
            cursor: pointer;
            margin-right: 15px;
            display: none; /* Default hidden, visible in mobile */
        }

        .chat-current-user {
            display: flex;
            align-items: center;
            flex-grow: 1;
        }

        .chat-current-user .avatar {
            width: 40px; 
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .chat-current-user .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .chat-current-user .username {
            font-weight: 700;
            font-size: 1.1em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: var(--text-color-light);
        }

        .chat-actions {
            display: flex;
            align-items: center; 
            gap: 15px; 
        }

        .chat-actions .icon-button {
            background: none;
            border: none;
            color: var(--text-color-muted);
            font-size: 1.2em;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .chat-actions .icon-button:hover {
            color: var(--text-color-light);
        }

        /* Custom Toggle Switch (Mute/Unmute & Theme) */
        .mute-toggle-wrapper, .theme-toggle-switch {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        .mute-toggle-wrapper {
            margin-left: 20px; /* Specific margin for mute toggle */
        }

        .mute-toggle-wrapper span {
            font-size: 0.9em;
            color: var(--text-color-muted);
        }
        /* Slider styling applies to both mute and theme toggles */
        .switch {
            position: relative;
            display: inline-block;
            width: 40px; 
            height: 22px; 
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--toggle-bg-off); 
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 22px; 
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px; 
            width: 16px; 
            left: 3px;
            bottom: 3px;
            background-color: var(--toggle-circle); 
            -webkit-transition: .4s;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--toggle-bg-on); 
        }

        input:focus + .slider {
            box-shadow: 0 0 1px var(--toggle-bg-on);
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(18px); 
            -ms-transform: translateX(18px);
            transform: translateX(18px);
        }

        /* PERBAIKAN UTAMA: Chat Messages Area (Area Pesan) */
        .chat-messages-area {
            flex-grow: 1; /* Pastikan mengambil semua ruang vertikal yang tersedia */
            padding: 20px 80px; /* Padding ini menentukan 'jarak pinggir' dari pesan */
            overflow-y: auto; /* Kunci untuk scrolling vertikal */
            background-color: transparent; 
            display: flex;
            flex-direction: column;
            height: 1px; /* Penting untuk flex-grow agar overflow bekerja dengan benar */
            min-height: 0; /* Penting untuk kompatibilitas flexbox */
        }
        
        .messages-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            min-height: min-content;
            flex-grow: 1;
        }

        .message {
            display: flex;
            margin-bottom: 8px; 
            align-items: flex-end;
            max-width: 100%; 
        }

        .message.received {
            justify-content: flex-start;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message .avatar {
            display: none; 
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #5a5a7a;
            margin: 0 10px;
            flex-shrink: 0;
        }

        .message-content {
            max-width: 65%; /* Atur lebar maksimal bubble pesan */
            padding: 8px 12px; 
            border-radius: 8px; 
            line-height: 1.3;
            position: relative;
            word-wrap: break-word; /* Memastikan teks panjang pecah baris */
            font-size: 0.95em;
        }

        .message.received .message-content {
            background-color: var(--chat-bubble-received);
            color: var(--text-color-light);
            border-bottom-left-radius: 2px; 
            margin-left: 10px; /* Jarak dari kiri */
        }
        .message.received .message-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: -8px; 
            width: 0;
            height: 0;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            border-right: 8px solid var(--chat-bubble-received);
        }


        .message.sent .message-content {
            background-color: var(--chat-bubble-sent);
            color: #ffffff;
            border-bottom-right-radius: 2px; 
            margin-right: 10px; /* Jarak dari kanan */
        }
        .message.sent .message-content::before {
            content: '';
            position: absolute;
            top: 0;
            right: -8px; 
            width: 0;
            height: 0;
            border-top: 8px solid transparent;
            border-bottom: 8px solid transparent;
            border-left: 8px solid var(--chat-bubble-sent);
        }


        .message-content p {
            margin: 0;
            padding: 0;
        }

        .timestamp {
            font-size: 0.7em; 
            color: var(--text-color-muted); 
            margin-top: 4px; 
            display: block;
            text-align: right;
        }

        /* Chat Input Area (Area Input Pesan) */
        .chat-input-area {
            display: flex;
            align-items: center;
            padding: 10px 16px; 
            background-color: var(--chat-input-area-bg); 
            flex-shrink: 0; /* Penting agar tidak mengecil */
            transition: background-color 0.3s ease;
            gap: 10px; /* Tambahkan jarak antar ikon dan input */
        }

        .message-input {
            flex-grow: 1; /* Input mengambil sisa ruang */
            padding: 10px 18px; 
            border: none;
            border-radius: 25px; 
            background-color: var(--message-input-bg); 
            color: var(--message-input-text);
            font-size: 0.95em;
            margin: 0; /* Hapus margin horizontal di sini, gunakan gap dari parent */
            outline: none;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .message-input::placeholder {
            color: var(--message-input-placeholder);
        }

        .send-button, .chat-input-area .icon-button {
            background-color: var(--primary-color); /* Hanya send-button yang punya background */
            border: none;
            border-radius: 50%;
            width: 44px; 
            height: 44px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #ffffff;
            font-size: 1.4em; 
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            flex-shrink: 0;
        }
        /* Penyesuaian untuk icon-button di area input agar tidak punya background */
        .chat-input-area .icon-button {
            background: none; /* Hapus background */
            color: var(--text-color-muted); /* Warna ikon */
            font-size: 1.4em;
        }
        /* Hover untuk icon-button di area input */
        .chat-input-area .icon-button:hover {
            color: var(--text-color-light); /* Hanya ubah warna ikon */
            background-color: transparent; /* Pastikan tidak ada background saat hover */
        }


        .send-button:hover { /* Hanya send-button yang berubah background saat hover */
            background-color: var(--secondary-color);
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) { 
            .chat-app-wrapper {
                position: relative; /* Kembali ke relative untuk mempermudah positioning di mobile */
                height: 100vh;
            }
            /* PERBAIKAN UTAMA MOBILE: Sidebar menjadi fixed overlay penuh */
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%; /* Ambil tinggi penuh viewport */
                width: 100%; /* Ambil lebar penuh viewport */
                transform: translateX(-100%); /* Sembunyikan ke kiri */
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.5);
                border-right: none; 
                z-index: 1001; /* Pastikan di atas konten chat utama */
            }

            .sidebar.active {
                transform: translateX(0); /* Tampilkan sidebar */
            }

            .sidebar-header h3 .close-sidebar-button {
                display: block;
                order: 2; 
            }
            .sidebar-header h3 {
                justify-content: space-between; 
            }
            .sidebar-header .user-profile-icon {
                margin-right: 0; 
            }
            .sidebar-header .sidebar-actions {
                display: flex;
                gap: 10px;
            }
            .sidebar-header .theme-toggle-switch {
                margin-left: 10px; 
                order: 1; 
            }
            .sidebar-header .user-profile-icon {
                order: 0;
            }
            .sidebar-header .sidebar-actions {
                order: 2; 
                gap: 5px; 
            }

            .toggle-sidebar-btn {
                display: block; /* Tampilkan tombol toggle sidebar di mobile */
                order: -1; 
            }

            .main-chat-content {
                width: 100%; /* Konten chat utama mengambil lebar penuh di mobile */
                margin-left: 0;
                height: 100%; 
                min-height: 0; 
            }
            
            .chat-messages-area {
                padding: 15px 20px; /* Sesuaikan padding di mobile */
                flex-grow: 1; 
                overflow-y: auto; 
                height: 0; 
                min-height: 0; 
            }

            .message-content {
                max-width: 85%; /* Lebih lebar di mobile */
            }
            .message.received .message-content {
                margin-left: 0; 
            }
            .message.sent .message-content {
                margin-right: 0; 
            }
            .message.received .message-content::before {
                left: -8px;
            }
            .message.sent .message-content::before {
                right: -8px;
            }
        }

        @media (max-width: 768px) {
            .chat-top-bar, .chat-input-area {
                padding: 8px 15px;
            }
            .chat-current-user .username {
                font-size: 1em;
            }
            .chat-actions .icon-button {
                font-size: 1.1em;
                margin-left: 10px;
            }
            .chat-list-item {
                padding: 10px 15px;
            }
            .chat-list-item .avatar {
                width: 45px;
                height: 45px;
            }
            .chat-list-item .chat-name {
                font-size: 0.95em;
            }
            .chat-list-item .last-message {
                font-size: 0.8em;
            }
            .send-button, .chat-input-area .icon-button {
                width: 40px;
                height: 40px;
                font-size: 1.2em;
            }
            .mute-toggle-wrapper { 
                display: flex; 
                margin-left: 10px; 
                gap: 5px; 
            }
            .mute-toggle-wrapper span {
                font-size: 0.8em; 
            }
            .switch {
                width: 35px; 
                height: 20px;
            }
            .slider:before {
                height: 14px; 
                width: 14px;
                left: 3px;
                bottom: 3px;
            }
            input:checked + .slider:before {
                transform: translateX(15px); 
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100vw;
                max-width: 100vw;
            }
            .chat-list-menu {
                max-height: calc(100vh - 200px);
            }
        }

        /* Tambahan/Perbaikan Responsive Sidebar */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 85vw;
                max-width: 400px;
                height: 100vh;
                z-index: 2000;
                background: var(--sidebar-bg);
                transform: translateX(-100%);
                transition: transform 0.3s;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .close-sidebar-button {
                display: block !important;
                position: absolute;
                right: 15px;
                top: 15px;
                font-size: 2em;
                background: none;
                border: none;
                color: var(--text-color-light);
                z-index: 2100;
            }
            .toggle-sidebar-btn {
                display: block !important;
            }
            .main-chat-content {
                width: 100vw !important;
                margin-left: 0 !important;
            }
            .chat-list-menu {
                max-height: calc(100vh - 170px);
                overflow-y: auto;
            }
        }
        @media (max-width: 576px) {
            .sidebar {
                width: 100vw;
                max-width: 100vw;
            }
            .chat-list-menu {
                max-height: calc(100vh - 200px);
            }
        }
        
        /* Notification sound */
        #notification-sound {
            display: none;
        }
        body { font-family: 'Segoe UI', 'Noto Sans', sans-serif; }

        /* Sidebar menu vertikal kiri */
        .sidebar-menu-vertical {
            position: fixed;
            left: 0;
            top: 0;
            width: 56px;
            height: 100vh;
            background: var(--sidebar-bg, #222d34);
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2002;
            border-right: 1px solid #222d34;
            transition: width 0.3s;
        }
        .sidebar-menu-vertical ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .sidebar-menu-vertical li {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .sidebar-menu-vertical button {
            width: 44px;
            height: 44px;
            background: none;
            border: none;
            color: #e9edef;
            font-size: 1.4em;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border-radius: 50%;
            transition: background 0.2s;
            margin: 0 auto;
        }
        .sidebar-menu-vertical button.active,
        .sidebar-menu-vertical button:hover {
            background: #2a3942;
        }
        .sidebar-menu-vertical .badge-green {
            position: absolute;
            top: 7px;
            right: 7px;
            background: #25d366;
            color: #fff;
            border-radius: 50%;
            font-size: 0.7em;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            pointer-events: none;
        }
        /* Tombol close sidebar menu */
        .close-sidebar-menu-btn {
            display: none;
            position: absolute;
            top: 8px;
            right: 8px;
            background: none;
            border: none;
            color: #e9edef;
            font-size: 2em;
            z-index: 10;
            cursor: pointer;
        }
        @media (max-width: 992px) {
            .sidebar-menu-vertical {
                display: flex;
                width: 56px;
            }
            .close-sidebar-menu-btn {
                display: block;
            }
        }
        @media (max-width: 576px) {
            .sidebar-menu-vertical {
                width: 48px;
            }
        }
        /* Sembunyikan sidebar menu vertikal di mobile */
        @media (max-width: 992px) {
            .sidebar-menu-vertical {
                display: none !important;
            }
            .chat-app-wrapper {
                margin-left: 0 !important;
                width: 100vw !important;
            }
        }

        /* Perbaiki wrapper agar tidak tertutup sidebar menu vertikal */
        .chat-app-wrapper {
            margin-left: 56px;
            width: calc(100vw - 56px);
        }
        @media (max-width: 992px) {
            .chat-app-wrapper {
                margin-left: 0;
                width: 100vw;
            }
        }
    </style>
</head>
<body data-theme="dark">
    <input type="checkbox" id="themeToggleCheckbox" style="display:none;">
    <nav class="sidebar-menu-vertical" id="sidebarMenuVertical">
    <button class="close-sidebar-menu-btn" id="closeSidebarMenuBtn" title="Tutup Menu">&times;</button>
    <ul>
        <li>
            <button title="Chats" class="active">
                <i class="fas fa-comment-dots"></i>
                <span class="badge-green">5</span>
            </button>
        </li>
        <li><button title="Calls"><i class="fas fa-phone"></i></button></li>
        <li><button title="Status"><i class="fas fa-circle-notch"></i></button></li>
        <li><button title="Communities"><i class="fas fa-users"></i></button></li>
        <li><button title="Starred"><i class="fas fa-star"></i></button></li>
        <li><button title="Archive"><i class="fas fa-archive"></i></button></li>
        <li><button title="Settings"><i class="fas fa-cog"></i></button></li>
        <li><button title="Profile"><i class="fas fa-user-circle"></i></button></li>
    </ul>
</nav>
    <div class="chat-app-wrapper">
        <!-- Sidebar menu vertikal kiri ala WhatsApp Web -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h3 style="font-weight:700;font-size:1.3em;margin:0;">Chats</h3>
                <div class="sidebar-actions">
                    <button class="icon-button" title="Status"><i class="fas fa-circle-notch"></i></button>
                    <button class="icon-button" title="New Chat"><i class="fas fa-comment-dots"></i></button>
                    <button class="icon-button" title="Menu"><i class="fas fa-ellipsis-v"></i></button>
                </div>
            </div>

            <div class="chat-search-bar">
                <input type="text" class="form-control" placeholder="Search or start a new chat" id="contactSearch">
            </div>
            
            <ul class="chat-list-menu" id="chatListMenu">
                <li style="text-align: center; color: var(--text-color-muted); padding-top: 50px;">
                    <p>Memuat kontak...</p>
                </li>
            </ul>
        </nav>

        <div class="main-chat-content">
            <header class="chat-top-bar">
                <button class="toggle-sidebar-btn" id="sidebar-toggle" title="Buka Daftar Obrolan">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="chat-current-user">
                    <div class="avatar online"><img src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png" alt="Avatar"></div>
                    <span class="username">Pilih Obrolan</span>
                </div>
                <div class="chat-actions">
                    <div class="mute-toggle-wrapper">
                        <span>Mute</span>
                        <label class="switch">
                            <input type="checkbox" id="muteToggle">
                            <span class="slider"></span>
                        </label>
                    </div>
                    <button class="icon-button" title="Panggilan Video"><i class="fas fa-video"></i></button>
                    <button class="icon-button" title="Panggilan Suara"><i class="fas fa-phone"></i></button>
                    <button class="icon-button" title="Opsi Lain"><i class="fas fa-ellipsis-v"></i></button>
                </div>
            </header>

            <main class="chat-messages-area" id="chatMessagesArea">
                <div class="messages-container" id="messagesContainer">
                    <div style="text-align: center; color: var(--text-color-muted); padding-top: 50px;">
                        <p>Selamat datang! Pilih obrolan dari daftar di samping untuk melihat riwayat percakapan.</p>
                    </div>
                </div>
            </main>

            <footer class="chat-input-area">
                <button class="icon-button" title="Lampirkan File"><i class="fas fa-paperclip"></i></button>
                <input type="text" class="message-input" placeholder="Ketik pesan..." id="messageInput">
                <button class="icon-button" title="Emoji"><i class="far fa-smile"></i></button>
                <button class="send-button" title="Kirim Pesan" id="sendButton"><i class="fas fa-paper-plane"></i></button>
            </footer>
        </div>
    </div>
    
    <!-- Notification sound -->
    <audio id="notification-sound" preload="auto">
        <source src="https://assets.mixkit.co/active_storage/sfx/2870/2870-preview.mp3" type="audio/mpeg">
    </audio>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const API_BASE_URL = 'https://udara.unis.ac.id/fiver'; 
        const NOTIFICATION_SOUND = document.getElementById('notification-sound');
        
        // DOM elements
        const sidebarToggle = document.getElementById('sidebar-toggle');

        const sidebar = document.getElementById('sidebar');
        const muteToggle = document.getElementById('muteToggle');
        const body = document.body;
        const chatMessagesArea = document.getElementById('chatMessagesArea');
        const messagesContainer = document.getElementById('messagesContainer');
        const themeToggleCheckbox = document.getElementById('themeToggleCheckbox');
        const chatListMenu = document.getElementById('chatListMenu');
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const mainUserSelect = document.getElementById('mainUserSelect');
        let CURRENT_MAIN_USER = mainUserSelect ? mainUserSelect.value : 'ahmadfikri820';
        const contactSearch = document.getElementById('contactSearch');
        const sidebarMenuVertical = document.getElementById('sidebarMenuVertical');
        const closeSidebarMenuBtn = document.getElementById('closeSidebarMenuBtn');
        
        // Tutup sidebar menu vertikal (hanya jika tombol dan sidebar ada)
        if (closeSidebarMenuBtn && sidebarMenuVertical) {
    closeSidebarMenuBtn.addEventListener('click', function() {
        if (window.innerWidth <= 992) {
            sidebarMenuVertical.classList.add('closed');
        }
    });
}

// Tampilkan lagi sidebar menu jika resize ke desktop (hanya jika sidebar ada)
if (sidebarMenuVertical) {
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            sidebarMenuVertical.classList.remove('closed');
        }
    });
}
        
        // Tombol close hanya aktif di mobile
        closeSidebarMenuBtn.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                sidebarMenuVertical.classList.add('closed');
            }
        });

        // Tampilkan lagi sidebar menu jika resize ke desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                sidebarMenuVertical.classList.remove('closed');
            }
        });
        
        // Global variables
        let CURRENT_ACTIVE_RECIPIENT_USERNAME = null;
        let CURRENT_ACTIVE_CONVERSATION_ID = null;
        let CURRENT_CONVERSATION_SOURCE_USER = null;
        let LAST_MESSAGE_ID = 0;
        let MESSAGE_POLL_INTERVAL = null;
        let CONTACTS_POLL_INTERVAL = null;
        let IS_TAB_ACTIVE = true;
        let userMuteStatus = {}; // { username: true/false } -- default kosong (semua false)
        let notifiedUnreadUsers = new Set(); // Untuk tracking notifikasi suara per user
        let frontendReadStatus = {}; // { username: true/false } status read di frontend

        // Scroll to bottom of chat
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        function ensureChatScrolls() {
            setTimeout(scrollToBottom, 100);
        }
        
        // Format timestamp
        function formatTimestamp(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();

            const diffMinutes = Math.floor((now - date) / (1000 * 60));
            const diffHours = Math.floor(diffMinutes / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffMinutes < 1) return 'Baru saja';
            if (diffMinutes < 60) return `${diffMinutes} menit lalu`;
            if (diffHours < 24) return `${date.getHours()}:${String(date.getMinutes()).padStart(2, '0')}`;
            if (diffDays === 1) return 'Kemarin';
            if (diffDays < 7) return date.toLocaleDateString('id-ID', { weekday: 'long' });
            
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }
        
        // Play notification sound
        function playNotificationSound() {
            if (!muteToggle.checked) {
                NOTIFICATION_SOUND.currentTime = 0;
                NOTIFICATION_SOUND.play().catch(e => console.log('Audio play failed:', e));
            }
        }
        
        // Show notification badge
        function showNotificationBadge(username) {
            const chatItems = document.querySelectorAll('.chat-list-item');
            chatItems.forEach(item => {
                if (item.dataset.username === username) {
                    let badge = item.querySelector('.notification-badge');
                    if (!badge) {
                        badge = document.createElement('div');
                        badge.className = 'notification-badge';
                        badge.textContent = '1';
                        item.appendChild(badge);
                    } else {
                        badge.textContent = parseInt(badge.textContent) + 1;
                    }
                }
            });
            
            if (!IS_TAB_ACTIVE) {
                document.title = '(!) Pesan Baru - WhatsApp Clone';
            }
        }
        
        // Remove notification badge
        function removeNotificationBadge(username) {
            const chatItem = document.querySelector(`.chat-list-item[data-username="${username}"]`);
            if (chatItem) {
                const badge = chatItem.querySelector('.notification-badge');
                if (badge) {
                    badge.remove();
                }
            }
            document.title = 'WhatsApp Clone';
        }
        
        // Fetch inbox contacts
        async function fetchInboxContactsData(username) {
            try {
                const response = await fetch(`${API_BASE_URL}/get_inbox_contacts?username=${username}`);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error(`Error fetching inbox contacts for ${username}:`, error);
                return null;
            }
        }
        
        // Render contacts to sidebar
        function renderContactsToSidebar(contacts) {
            chatListMenu.innerHTML = '';
            if (contacts && contacts.length > 0) {
                contacts.forEach(contact => {
                    const li = document.createElement('li');
                    // Cek status read/unread dari frontendReadStatus
                    const isUnread = frontendReadStatus[contact.username] === false || (contact.unreadCount && contact.unreadCount > 0);
                    const unreadClass = isUnread ? 'unread' : '';
                    li.innerHTML = `
                        <a href="#" class="chat-list-item ${unreadClass}" 
                            data-username="${contact.username}" 
                            data-conversation-id="${contact.conversationId || ''}"
                            data-profile-image="${contact.profileImage || 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png'}"
                            data-source-user="${contact.sourceUser || ''}">
                            <div class="avatar ${contact.isOnline ? 'online' : ''}">
                                <img src="${contact.profileImage || 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png'}" alt="Avatar">
                            </div>
                            <div class="chat-info">
                                <span class="chat-name">${contact.displayName || contact.username}</span>
                                <span class="last-message">${contact.excerpt || 'No message'}</span>
                            </div>
                            <span class="chat-time">${formatTimestamp(contact.recentMessageDate)}</span>
                            ${
                                isUnread
                                ? `<span class="notification-badge">${contact.unreadCount || ''}</span>`
                                : `<span class="read-indicator" style="color:#2196f3;font-size:1.2em;margin-left:8px;"><i class="fas fa-check-double"></i></span>`
                            }
                        </a>
                    `;
                    chatListMenu.appendChild(li);
                });
                attachChatListItemListeners();
            } else {
                chatListMenu.innerHTML = `<li style="text-align: center; color: var(--text-color-muted); padding-top: 50px;"><p>Tidak ada kontak ditemukan.</p></li>`;
            }
        }
        
        // Load contacts for the selected user
        async function fetchAndRenderSingleUserInboxContacts(username) {
            const data = await fetchInboxContactsData(username);
            
            if (data && data.status === 'success' && data.data && data.data.length > 0) {
                data.data.forEach(contact => {
                    contact.sourceUser = username;
                });
                renderContactsToSidebar(data.data);
            } else {
                chatListMenu.innerHTML = `<li><p style="padding: 20px; color: var(--text-color-muted);">Tidak ada kontak ditemukan untuk ${username}.</p></li>`;
                document.querySelector('.chat-current-user .username').textContent = 'Pilih Obrolan';
                document.querySelector('.chat-current-user .avatar img').src = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
                messagesContainer.innerHTML = `<div style="text-align: center; color: var(--text-color-muted); padding-top: 50px;"><p>Selamat datang! Pilih obrolan dari daftar di samping untuk melihat riwayat percakapan.</p></div>`;
            }
        }
        
        // Load all contacts by default
        async function loadDefaultAllContacts() {
            const usernamesToFetch = ['ahmadfikri820', 'chyailya'];
            let allCombinedContacts = new Map();

            for (const username of usernamesToFetch) {
                const data = await fetchInboxContactsData(username);
                if (data && data.status === 'success' && data.data) {
                    data.data.forEach(contact => {
                        contact.sourceUser = username;
                        const uniqueKey = `${contact.username}__${username}`;
                        if (!allCombinedContacts.has(uniqueKey)) {
                            allCombinedContacts.set(uniqueKey, contact);
                        }
                    });
                }
            }

            const contactsArr = Array.from(allCombinedContacts.values());

            // Jika tidak ada kontak, tampilkan pesan
            if (contactsArr.length === 0) {
                chatListMenu.innerHTML = `<li style="text-align: center; color: var(--text-color-muted); padding-top: 50px;"><p>Tidak ada kontak ditemukan.</p></li>`;
                return;
            }

            // Urutkan: unread dulu, lalu berdasarkan waktu pesan terbaru (recentMessageDate)
            contactsArr.sort((a, b) => {
                // Unread di atas
                const aUnread = frontendReadStatus[a.username] === false || (a.unreadCount && a.unreadCount > 0);
                const bUnread = frontendReadStatus[b.username] === false || (b.unreadCount && b.unreadCount > 0);
                if (aUnread !== bUnread) return bUnread - aUnread;

                // Jika sama-sama unread atau sama-sama read, urutkan berdasarkan waktu pesan terbaru (desc)
                const aTime = new Date(a.recentMessageDate || 0).getTime();
                const bTime = new Date(b.recentMessageDate || 0).getTime();
                return bTime - aTime;
            });

            // Cek pesan baru untuk badge & suara
            contactsArr.forEach(contact => {
                const chatKey = `${contact.username}__${contact.sourceUser}`;
                const isUnread = frontendReadStatus[contact.username] === false || (contact.unreadCount && contact.unreadCount > 0);
                if (isUnread) {
                    showNotificationBadge(contact.username);
                    if (
                        contact.username !== CURRENT_ACTIVE_RECIPIENT_USERNAME ||
                        contact.sourceUser !== CURRENT_CONVERSATION_SOURCE_USER
                    ) {
                        if (!notifiedUnreadUsers.has(chatKey)) {
                            playNotificationSound();
                            notifiedUnreadUsers.add(chatKey);
                        }
                    }
                } else {
                    notifiedUnreadUsers.delete(chatKey);
                }
            });

            renderContactsToSidebar(contactsArr);

            // Jika chat yang sedang aktif ada di list, refresh detail chat
            if (CURRENT_ACTIVE_RECIPIENT_USERNAME && CURRENT_CONVERSATION_SOURCE_USER) {
                const found = contactsArr.find(
                    c => c.username === CURRENT_ACTIVE_RECIPIENT_USERNAME && c.sourceUser === CURRENT_CONVERSATION_SOURCE_USER
                );
                if (found) {
                    fetchAndRenderConversation(CURRENT_CONVERSATION_SOURCE_USER, CURRENT_ACTIVE_RECIPIENT_USERNAME);
                }
            }

            const defaultWelcomeMessage = messagesContainer.querySelector('div[style*="text-align: center"]');
            if (defaultWelcomeMessage) {
                defaultWelcomeMessage.style.display = 'none';
            }
        }
        
        // Fetch and render conversation
        async function fetchAndRenderConversation(mainUser, recipientUsername) {
            try {
                const response = await fetch(`${API_BASE_URL}/get_conversation_details?username=${mainUser}&recipient_username=${recipientUsername}`);
                const data = await response.json();
                
                messagesContainer.innerHTML = '';
                LAST_MESSAGE_ID = 0;
                
                if (data.status === 'success' && data.data && data.data.messages && data.data.messages.length > 0) {
                    data.data.messages.forEach(msg => {
                        addMessageToChat(msg.sender.toLowerCase() === mainUser.toLowerCase(), msg.body, msg.createdAt);
                        if (msg.id > LAST_MESSAGE_ID) {
                            LAST_MESSAGE_ID = msg.id;
                        }
                    });
                    ensureChatScrolls();
                } else {
                    messagesContainer.innerHTML = `<div style="text-align: center; color: var(--text-color-muted); padding-top: 50px;"><p>Belum ada percakapan dengan ${recipientUsername}.</p></div>`;
                }
                
                removeNotificationBadge(recipientUsername);
            } catch (error) {
                console.error('Error fetching conversation details:', error);
                messagesContainer.innerHTML = `<div style="text-align: center; color: red; padding-top: 50px;"><p>Gagal memuat riwayat obrolan. Cek koneksi API.</p></div>`;
            }
        }
        
        // Add message to chat UI
        function addMessageToChat(isSent, messageText, timestamp) {
            const messageDiv = document.createElement('div');
            messageDiv.classList.add('message');
            messageDiv.classList.add(isSent ? 'sent' : 'received');
            
            messageDiv.innerHTML = `
                <div class="message-content">
                    <p>${messageText}</p>
                    <span class="timestamp">${formatTimestamp(timestamp || Date.now())}</span>
                </div>
            `;
            messagesContainer.appendChild(messageDiv);
            ensureChatScrolls();
        }
        
        // Check for new messages
        async function checkForNewMessages() {
            if (!CURRENT_ACTIVE_RECIPIENT_USERNAME || !CURRENT_CONVERSATION_SOURCE_USER) return;
            
            try {
                const response = await fetch(`${API_BASE_URL}/get_inbox_contacts?username=${CURRENT_CONVERSATION_SOURCE_USER}&recipient_username=${CURRENT_ACTIVE_RECIPIENT_USERNAME}&last_message_id=${LAST_MESSAGE_ID}`);
                const data = await response.json();
                
                if (data.status === 'success' && data.data && data.data.messages && data.data.messages.length > 0) {
                    let hasNewMessage = false;
                    
                    data.data.messages.forEach(msg => {
                        if (msg.id > LAST_MESSAGE_ID) {
                            const isSent = msg.sender.toLowerCase() === CURRENT_CONVERSATION_SOURCE_USER.toLowerCase();
                            addMessageToChat(isSent, msg.body, msg.createdAt);
                            LAST_MESSAGE_ID = msg.id;
                            
                            // Play notification for received messages
                            if (!isSent) {
                                playNotificationSound();
                                hasNewMessage = true;
                            }
                        }
                    });
                    
                    if (hasNewMessage && !IS_TAB_ACTIVE) {
                        showNotificationBadge(CURRENT_ACTIVE_RECIPIENT_USERNAME);
                    }
                }
            } catch (error) {
                console.error('Error checking for new messages:', error);
            }
        }
        
        // Send message
        async function sendMessage() {
            const messageText = messageInput.value.trim();
            const senderUser = CURRENT_CONVERSATION_SOURCE_USER || CURRENT_MAIN_USER;
            const allowedSenders = ['ahmadfikri820', 'chyailya'];

            if (
                messageText === '' ||
                !senderUser ||
                !CURRENT_ACTIVE_RECIPIENT_USERNAME ||
                !CURRENT_ACTIVE_CONVERSATION_ID
            ) {
                alert('Pesan tidak boleh kosong atau obrolan belum dipilih.');
                return;
            }
            if (!allowedSenders.includes(senderUser)) {
                alert('Pengirim tidak valid!');
                return;
            }

            // Optimistic UI update (tampilkan pesan langsung)
            addMessageToChat(true, messageText);

            try {
                // Ambil status mute dari backend (bukan hanya localStorage)
                let isMuted = false;
                try {
                    const muteRes = await fetch(`${API_BASE_URL}/get_mute_status?username=${CURRENT_ACTIVE_RECIPIENT_USERNAME}`);
                    const muteData = await muteRes.json();
                    isMuted = muteData && muteData.button === 1;
                } catch (e) {
                    // fallback ke localStorage jika gagal
                    isMuted = !!userMuteStatus[CURRENT_ACTIVE_RECIPIENT_USERNAME];
                }

                const response = await fetch(`${API_BASE_URL}/send_message`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        username: senderUser,
                        recipient_username: CURRENT_ACTIVE_RECIPIENT_USERNAME,
                        message_text: messageText,
                        conversation_id: CURRENT_ACTIVE_CONVERSATION_ID,
                        mute: isMuted
                    })
                });
                const data = await response.json();

                if (data.status === 'success') {
                    messageInput.value = '';
                } else {
                    // Rollback optimistic update if failed
                    const messages = messagesContainer.querySelectorAll('.message');
                    const lastMessage = messages[messages.length - 1];
                    if (lastMessage && lastMessage.querySelector('.message-content p').textContent === messageText) {
                        lastMessage.remove();
                    }
                    alert('Gagal mengirim pesan: ' + (data.message || 'Error tidak diketahui'));
                }
            } catch (error) {
                // Rollback optimistic update if failed
                const messages = messagesContainer.querySelectorAll('.message');
                const lastMessage = messages[messages.length - 1];
                if (lastMessage && lastMessage.querySelector('.message-content p').textContent === messageText) {
                    lastMessage.remove();
                }
                alert('Terjadi kesalahan saat mengirim pesan.');
            }
        }
        
        // Attach chat item listeners
        function attachChatListItemListeners() {
            document.querySelectorAll('.chat-list-item').forEach(item => {
                item.removeEventListener('click', handleChatListItemClick);
                item.addEventListener('click', handleChatListItemClick);
            });
        }
        
        // Saat user memilih chat, update toggle sesuai status
        function handleChatListItemClick(event) {
            event.preventDefault();
            document.querySelectorAll('.chat-list-item').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            CURRENT_ACTIVE_RECIPIENT_USERNAME = this.dataset.username;
            CURRENT_ACTIVE_CONVERSATION_ID = this.dataset.conversationId;
            CURRENT_CONVERSATION_SOURCE_USER = this.dataset.sourceUser;

            const userName = this.querySelector('.chat-name').textContent;
const userAvatarSrc = this.querySelector('.avatar img').src;
const userSourceUser = this.dataset.sourceUser; // ambil username yang menerima

document.querySelector('.chat-current-user .username').textContent = `${userName} (${userSourceUser})`;
document.querySelector('.chat-current-user .avatar img').src = userAvatarSrc;

            // Default mute: OFF (false) jika belum pernah di-set
            muteToggle.checked = !!userMuteStatus[CURRENT_ACTIVE_RECIPIENT_USERNAME];

            fetchAndRenderConversation(CURRENT_CONVERSATION_SOURCE_USER, CURRENT_ACTIVE_RECIPIENT_USERNAME);

            // Tandai sebagai sudah dibaca di frontend
            frontendReadStatus[CURRENT_ACTIVE_RECIPIENT_USERNAME] = true;
            notifiedUnreadUsers.delete(CURRENT_ACTIVE_RECIPIENT_USERNAME);
            removeNotificationBadge(CURRENT_ACTIVE_RECIPIENT_USERNAME);

            if (window.innerWidth <= 992) {
                sidebar.classList.remove('active');
            }

            // Tambahkan baris ini agar sidebar langsung update centang read/unread
            loadDefaultAllContacts();
        }
        
        // Initialize app
        window.addEventListener('load', () => {
            loadMuteStatusFromStorage(); // Tambahkan ini
            ensureChatScrolls();
            if (body.dataset.theme === 'white') {
                themeToggleCheckbox.checked = true;
            }
            loadDefaultAllContacts();
            
            // Start polling for new messages (detail chat)
            MESSAGE_POLL_INTERVAL = setInterval(checkForNewMessages, 3000);

            // Start polling for contacts (sidebar)
            CONTACTS_POLL_INTERVAL = setInterval(loadDefaultAllContacts, 5000);
        });
        
        // Tab visibility change
        document.addEventListener('visibilitychange', () => {
            IS_TAB_ACTIVE = document.visibilityState === 'visible';
            if (IS_TAB_ACTIVE) {
                document.title = 'WhatsApp Clone';
            }
        });
        
        // Window resize
        window.addEventListener('resize', ensureChatScrolls);
        
        // Sidebar toggle
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.add('active');
        });
//   
        // Tutup sidebar jika klik di luar sidebar (khusus mobile)
        document.addEventListener('click', (event) => {
            if (window.innerWidth <= 992 && sidebar.classList.contains('active') &&
                !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
        
        // Mute toggle
        muteToggle.addEventListener('change', async function() {
            if (CURRENT_ACTIVE_RECIPIENT_USERNAME) {
                userMuteStatus[CURRENT_ACTIVE_RECIPIENT_USERNAME] = this.checked;
                saveMuteStatusToStorage();

                // Kirim ke backend (sinkron dengan backend Flask)
                try {
                    await fetch(`${API_BASE_URL}/set_mute_status`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            username: CURRENT_ACTIVE_RECIPIENT_USERNAME,
                            button: this.checked ? 1 : 0
                        })
                    });
                } catch (e) {
                    // Optional: tampilkan error jika gagal update mute ke backend
                    alert('Gagal update mute ke server.');
                }
            }
        });
        
        // Theme toggle
        if (themeToggleCheckbox) {
            themeToggleCheckbox.addEventListener('change', function() {
                body.dataset.theme = this.checked ? 'white' : 'dark';
                ensureChatScrolls();
            });
        }
        
        // Main user change
        if (mainUserSelect) {
            mainUserSelect.addEventListener('change', async function() {
                CURRENT_MAIN_USER = this.value;
                CURRENT_ACTIVE_RECIPIENT_USERNAME = null; 
                CURRENT_ACTIVE_CONVERSATION_ID = null;
                CURRENT_CONVERSATION_SOURCE_USER = CURRENT_MAIN_USER;
                
                await fetchAndRenderSingleUserInboxContacts(CURRENT_MAIN_USER);
                document.querySelector('.chat-current-user .username').textContent = 'Pilih Obrolan';
                document.querySelector('.chat-current-user .avatar img').src = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
                messagesContainer.innerHTML = `<div style="text-align: center; color: var(--text-color-muted); padding-top: 50px;"><p>Selamat datang! Pilih obrolan dari daftar di samping untuk melihat riwayat percakapan.</p></div>`;
                ensureChatScrolls(); 
            });
        }
        
        // Send message events
        sendButton.addEventListener('click', function(e) {
    e.preventDefault();
    sendMessage();
});
messageInput.addEventListener('keypress', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault(); // <-- penting!
        sendMessage();
    }
});

        // Contact search
        contactSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const chatItems = document.querySelectorAll('.chat-list-item');
            
            chatItems.forEach(item => {
                const chatName = item.querySelector('.chat-name').textContent.toLowerCase();
                const lastMessage = item.querySelector('.last-message').textContent.toLowerCase();
                
                if (chatName.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                    item.parentElement.style.display = 'block';
                } else {
                    item.parentElement.style.display = 'none';
                }
            });
        });

        function saveMuteStatusToStorage() {
    localStorage.setItem('userMuteStatus', JSON.stringify(userMuteStatus));
}

function loadMuteStatusFromStorage() {
    const data = localStorage.getItem('userMuteStatus');
    if (data) {
        userMuteStatus = JSON.parse(data);
    }
}
    </script>
</body>
</html>