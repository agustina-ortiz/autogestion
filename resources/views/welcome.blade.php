<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Autogestión - Mercedes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Header */
        header {
            background: linear-gradient(150deg, #77BF43 0%, #91D5E2 20%, #BED630 100%);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .logo {
            height: 50px;
            width: 80px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            margin-left: 80px;
            justify-content: center;
            font-weight: bold;
            color: #77BF43;
        }

        .header-right {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-btn {
            background-color: white;
            color: #77BF43;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .logout-btn {
            background-color: #BED630;
            color: #333;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* Main Content */
        main {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 2rem;
            overflow: hidden;
        }

        .content-wrapper {
            display: flex;
            gap: 2rem;
            margin-bottom: 1.5rem;
            flex: 0 0 auto;
        }

        /* Employee Section */
        .employee-section {
            flex: 0 0 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .employee-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #91D5E2, #77BF43);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            font-weight: bold;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .employee-info {
            width: 100%;
            text-align: left;
        }

        .employee-info p {
            margin: 0.5rem 0;
            font-size: 0.95rem;
            color: #333;
        }

        .employee-info strong {
            color: #77BF43;
            display: inline-block;
            width: 80px;
        }

        /* Welcome Section */
        .welcome-section {
            flex: 1;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .welcome-section h1 {
            color: #77BF43;
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .welcome-section p {
            color: #555;
            line-height: 1.6;
            font-size: 1rem;
        }

        .welcome-section strong {
            color: #91D5E2;
        }

        /* Buttons Grid */
        .buttons-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            margin-top: auto;
            flex: 0 0 auto;
        }

        .action-btn {
            background: linear-gradient(135deg, #77BF43, #BED630);
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .action-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }

        .action-btn svg {
            width: 32px;
            height: 32px;
        }

        /* Footer */
        footer {
            background: linear-gradient(90deg, #91D5E2 0%, #91D5E2 2%, #77BF43 25%, #77BF43 75%, #91D5E2 98%, #91D5E2 100%);
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .footer-logo {
            height: 30px;
            width: 60px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #77BF43;
            font-size: 0.9rem;
        }

        /* WhatsApp Button */
        .whatsapp-btn {
            position: fixed;
            bottom: 0.5rem;
            right: 1rem;
            width: 50px;
            height: 50px;
            background-color: #25D366;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: transform 0.3s;
            z-index: 1000;
        }

        .whatsapp-btn:hover {
            transform: scale(1.1);
        }

        .whatsapp-btn svg {
            width: 32px;
            height: 32px;
            fill: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <img src="{{ asset('images/logo-muni.svg') }}" alt="Logo Municipalidad">
        </div>
        <div class="header-right">
            <div class="dropdown">
                <button class="dropdown-btn">Autogestión ▼</button>
            </div>
            <button class="logout-btn" onclick="handleLogout()">
                Salir
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <div class="content-wrapper">
            <!-- Employee Section -->
            <div class="employee-section">
                <div class="employee-photo">JD</div>
                <div class="employee-info">
                    <p><strong>Empleado:</strong> Juan Pérez</p>
                    <p><strong>DNI:</strong> 12.345.678</p>
                    <p><strong>Legajo:</strong> 001234</p>
                    <p><strong>Perfil:</strong> Administrativo</p>
                    <p><strong>Categoría:</strong> A3</p>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Bienvenido al sistema AUTOGESTIÓN</h1>
                <p>
                    En esta página los empleados de la <strong>Municipalidad de Mercedes</strong>, podrán consultar 
                    sus <strong>recibos de sueldos</strong> como así también, otros temas de interés.
                </p>
                <p style="margin-top: 1rem;">
                    Si tiene dudas, puede consultar la sección de <strong>Preguntas frecuentes</strong>
                </p>
                <p style="margin-top: 0.5rem;">
                    Si necesita informar algo puede dirigirse a la sección <strong>Contacto</strong>
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="buttons-grid">
            <button class="action-btn" onclick="goToRecibos()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                Recibos
            </button>

            <button class="action-btn" onclick="goToAsistencias()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Asistencias
            </button>

            <button class="action-btn" onclick="goToCompensatorios()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                Compensatorios
            </button>

            <button class="action-btn" onclick="goToSolicitudes()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                Solicitudes
            </button>

            <button class="action-btn" onclick="goToPreguntas()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Preguntas Frecuentes
            </button>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-logo">
            <img src="{{ asset('images/logo-muni-M.svg') }}" alt="Logo Municipalidad">
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/5491234567890" target="_blank" class="whatsapp-btn">
        <svg viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
        </svg>
    </a>

    <script>
        function handleLogout() {
            // Aquí iría tu lógica de logout
            if(confirm('¿Está seguro que desea salir?')) {
                window.location.href = '/logout';
            }
        }

        function goToRecibos() {
            window.location.href = '/recibos';
        }

        function goToAsistencias() {
            window.location.href = '/asistencias';
        }

        function goToCompensatorios() {
            window.location.href = '/compensatorios';
        }

        function goToSolicitudes() {
            window.location.href = '/solicitudes';
        }

        function goToPreguntas() {
            window.location.href = '/preguntas-frecuentes';
        }
    </script>
</body>
</html>