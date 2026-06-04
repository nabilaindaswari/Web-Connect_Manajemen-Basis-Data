<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Error</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #fbfbfd;
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: #333333;
        }

        .error-container {
            background-color: #ffffff;
            padding: 50px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }

        /* --- ANIMASI SVG --- */
        .svg-container {
            margin-bottom: 30px;
        }

        /* 1. Komputer hijau dengan efek glow (cahaya) yang bernapas */
        .anim-computer {
            fill: #8da750; /* Warna hijau solid */
            /* animation: glow-comp 2s infinite ease-in-out; */
        }

        @keyframes glow-comp {
            0%, 100% { filter: drop-shadow(0 0 3px rgba(141, 167, 80, 0.4)); }
            50% { filter: drop-shadow(0 0 12px rgba(141, 167, 80, 0.9)); }
        }

        /* 2. Titik-titik aktif bergerak ke kanan (ke arah X) */
        .anim-line-active {
            stroke-dasharray: 6;
            animation: dash-move 1s linear infinite;
        }

        @keyframes dash-move {
            to { stroke-dashoffset: -12; }
        }

        /* 3. X Merah diam di tempat tapi berkedip redup (Flash Dim) */
        .anim-x {
            stroke: #dc3545;
            animation: flash-dim 1.5s infinite ease-in-out;
        }

        @keyframes flash-dim {
            0%, 100% { opacity: 1; filter: drop-shadow(0 0 4px rgba(220, 53, 69, 0.6)); }
            50% { opacity: 0.2; filter: none; }
        }

        /* 4. Titik-titik setelah X (Redup dan Flashing Dimming) */
        .anim-line-dim {
            stroke-dasharray: 6;
            animation: dim-flash 1.5s infinite ease-in-out;
        }

        @keyframes dim-flash {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 0.1; }
        }

        /* --- TIPOGRAFI --- */
        h1 {
            font-family: 'Google Sans', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: #dc3545;
            margin: 0 0 10px 0;
            letter-spacing: 0.5px;
        }

        p {
            font-size: 15px;
            color: #666666;
            line-height: 1.5;
            margin: 0 0 35px 0;
        }

        /* --- TOMBOL --- */
        .btn-home {
            display: inline-block;
            background-color: #8da750;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: background-color 0.2s, transform 0.1s;
            box-shadow: 0 4px 10px rgba(141, 167, 80, 0.2);
        }

        .btn-home:hover {
            background-color: #718740;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="error-container">
        
        <div class="svg-container">
            <svg width="340" height="120" viewBox="0 0 340 120" xmlns="http://www.w3.org/2000/svg">
                
                <g class="anim-computer" transform="translate(10, 30)">
                    <rect x="0" y="0" width="50" height="35" rx="4" />
                    <rect x="20" y="35" width="10" height="10" />
                    <rect x="10" y="45" width="30" height="5" rx="2" />
                    <rect x="5" y="5" width="40" height="25" fill="#fff" />
                </g>

                <line x1="75" y1="55" x2="145" y2="55" stroke="#8da750" stroke-width="4" stroke-linecap="round" class="anim-line-active" />

                <g transform="translate(170, 55)" class="anim-x">
                    <line x1="-12" y1="-12" x2="12" y2="12" stroke-width="6" stroke-linecap="round" />
                    <line x1="12" y1="-12" x2="-12" y2="12" stroke-width="6" stroke-linecap="round" />
                </g>

                <line x1="195" y1="55" x2="265" y2="55" stroke="#cccccc" stroke-width="4" stroke-linecap="round" class="anim-line-dim" />

                <g transform="translate(280, 25)" fill="none" stroke="#888" stroke-width="4" opacity="0.4">
                    <path d="M0,45 v15 a25,10 0 0,0 50,0 v-15" fill="#eee" />
                    <path d="M0,25 v15 a25,10 0 0,0 50,0 v-15" fill="#eee" />
                    <path d="M0,5 v15 a25,10 0 0,0 50,0 v-15" fill="#eee" />
                    
                    <ellipse cx="25" cy="45" rx="25" ry="10" fill="#fdfdfd" />
                    <ellipse cx="25" cy="25" rx="25" ry="10" fill="#fdfdfd" />
                    <ellipse cx="25" cy="5" rx="25" ry="10" fill="#fdfdfd" />
                </g>
            </svg>
        </div>

        <h1>ERR_DATABASE_NOT_FOUND</h1>
        <p>Oops! Koneksi ke Database Gagal! Harap hubungi Admin.</p>

        <a href="home.php" class="btn-home">Kembali ke Home</a>
    </div>

</body>
</html>