<x-layouts.autogestion>
    <x-slot:title>Inicio - Sistema Autogestión</x-slot:title>

    @push('styles')
    <style>
        main {
            padding: 2rem;
            overflow: hidden;
        }

        .content-wrapper {
            display: flex;
            gap: 2rem;
            margin-bottom: 1.5rem;
            margin-top: 2rem;
        }

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

        .buttons-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
        }

        .action-card {
            background: linear-gradient(135deg, #77BF43, #BED630);
            color: white;
            padding: 1.5rem 1rem;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }

        .action-card svg {
            width: 32px;
            height: 32px;
            stroke: white;
        }

        .action-card span {
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
        }
    </style>
    @endpush

    <div class="content-wrapper">
        <div class="employee-section">
            <div class="employee-photo">
                {{ strtoupper(substr(auth()->user()->name ?? 'JD', 0, 2)) }}
            </div>
            <div class="employee-info">
                <p><strong>Empleado:</strong> {{ auth()->user()->name ?? 'Juan Pérez' }}</p>
                <p><strong>DNI:</strong> {{ auth()->user()->dni ?? '12.345.678' }}</p>
                <p><strong>Legajo:</strong> {{ auth()->user()->legajo ?? '001234' }}</p>
                <p><strong>Perfil:</strong> {{ auth()->user()->perfil ?? 'Administrativo' }}</p>
                <p><strong>Categoría:</strong> {{ auth()->user()->categoria ?? 'A3' }}</p>
            </div>
        </div>

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

    <div class="buttons-grid">
        <a href="{{ route('recibos') }}" class="action-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <span>Recibos</span>
        </a>

        <a href="#" class="action-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
            <span>Asistencias</span>
        </a>

        <a href="#" class="action-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Compensatorios</span>
        </a>

        <a href="#" class="action-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="12" y1="18" x2="12" y2="12"/>
                <line x1="9" y1="15" x2="15" y2="15"/>
            </svg>
            <span>Solicitudes</span>
        </a>

        <a href="#" class="action-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <span>Preguntas Frecuentes</span>
        </a>
    </div>
</x-layouts.autogestion>