<!DOCTYPE html>
<html lang="es" class="h-screen overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Asistencias - Municipalidad de Mercedes</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'system-ui', 'sans-serif'],
                        mono: ['Space Mono', 'monospace'],
                    },
                    colors: {
                        primary: {
                            green: '#77BF43',
                            dark: '#5A9934',
                        },
                        secondary: {
                            green: '#BED630',
                            light: '#E8F5E0',
                        },
                        accent: {
                            pink: '#EE5B99',
                        },
                        municipal: {
                            dark: '#1a1a2e',
                            darker: '#16213e',
                        }
                    },
                    animation: {
                        'fade-in-down': 'fadeInDown 0.6s ease-out',
                        'slide-in-up': 'slideInUp 0.5s ease-out',
                        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeInDown: {
                            '0%': { opacity: '0', transform: 'translateY(-20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.7' },
                        },
                    },
                }
            }
        }
    </script>
    
    <style>
        /* Estilos mínimos necesarios para animaciones y efectos especiales */
        .gradient-text {
            background: linear-gradient(135deg, #77BF43, #5A9934);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .gradient-green {
            background: linear-gradient(135deg, #77BF43, #5A9934);
        }
        
        .gradient-dark {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
        }
        
        .gradient-badge-success {
            background: linear-gradient(135deg, #77BF43, #5A9934);
        }
        
        .gradient-badge-info {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        
        .gradient-badge-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        
        .gradient-badge-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
        }
        
        .gradient-canvas-bg {
            background: linear-gradient(135deg, #e8f5e0 0%, #ffffff 100%);
        }
        
        .news-ticker::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #77BF43, #BED630, #EE5B99);
        }
        
        #tarjeta::selection {
            background: #77BF43;
            color: #77BF43;
        }
    </style>
    
    @livewireStyles
</head>
<body class="h-screen overflow-hidden font-sans bg-gray-50">
    {{ $slot }}
    
    @livewireScripts
</body>
</html>