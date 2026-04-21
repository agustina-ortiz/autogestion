<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DDJJ Asignaciones Familiares — Autogestión RR.HH.</title>
</head>
<body style="margin:0; padding:32px 16px; background-color:#f0f2f0; font-family:Arial,Helvetica,sans-serif;">
<div style="max-width:580px; margin:0 auto;">

  <!-- Header -->
  <div style="background-color:#81af00; padding:28px 36px; border-radius:10px 10px 0 0; display:flex; align-items:center; gap:16px;">
    <div>
      <p style="color:white; font-size:17px; font-weight:bold; margin:0 0 4px;">Municipalidad de Mercedes</p>
      <p style="color:rgba(255,255,255,0.82); font-size:13px; margin:0 0 10px;">Portal de Autogestión · Recursos Humanos</p>
      <span style="display:inline-block; background:rgba(255,255,255,0.22); color:white; font-size:11px; padding:4px 12px; border-radius:20px;">DDJJ Asignaciones Familiares</span>
    </div>
  </div>
  <div style="height:4px; background:linear-gradient(to right, #81af00, #c7d100, #a5d6e5);"></div>

  <!-- Body -->
  <div style="background:white; padding:36px 44px;">
    <p style="font-size:14px; color:#555; line-height:1.7; margin:0 0 32px;">
      Un empleado ha enviado o actualizado su Declaración Jurada para Asignaciones Familiares
      desde el portal de autogestión. A continuación se detalla la información registrada.
    </p>

    <!-- Datos del empleado -->
    <p style="font-size:11px; font-weight:bold; color:#999; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 14px;">Datos del empleado</p>
    <div style="background:#f7f8f2; border-radius:8px; padding:4px 20px; margin-bottom:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; border-bottom:1px solid #e8ecda; font-size:14px;">
        <span style="color:#777; white-space:nowrap;">Nombre y apellido: </span>
        <span style="color:#1a1a1a; font-weight:bold; text-align:right;">{{ $nombre }}</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; border-bottom:1px solid #e8ecda; font-size:14px;">
        <span style="color:#777; white-space:nowrap;">Legajo: </span>
        <span style="color:#1a1a1a; font-weight:bold; text-align:right;">{{ $legajo }}</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; font-size:14px;">
        <span style="color:#777; white-space:nowrap;">Período: </span>
        <span style="color:#1a1a1a; font-weight:bold; text-align:right;">{{ $periodo }}/{{ $anio }}</span>
      </div>
    </div>

    <!-- Un bloque por cada hijo -->
    @foreach($formularios as $index => $formulario)
      <p style="font-size:11px; font-weight:bold; color:#999; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 14px;">
        Hijo/a {{ $index + 1 }} — {{ $formulario['nombre'] }}
      </p>

      <div style="background:#f7f8f2; border-radius:8px; padding:4px 20px; margin-bottom:8px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; border-bottom:1px solid #e8ecda; font-size:14px;">
          <span style="color:#777; white-space:nowrap;">DNI hijo/a:</span>
          <span style="color:#1a1a1a; font-weight:bold; text-align:right;">{{ $formulario['dnihijo'] }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; border-bottom:1px solid #e8ecda; font-size:14px;">
          <span style="color:#777; white-space:nowrap;">Progenitor (padre): </span>
          <span style="color:#1a1a1a; font-weight:bold; text-align:right;">{{ $formulario['nombrepadre'] ?: '—' }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; border-bottom:1px solid #e8ecda; font-size:14px;">
          <span style="color:#777; white-space:nowrap;">DNI progenitor: </span>
          <span style="color:#1a1a1a; font-weight:bold; text-align:right;">{{ $formulario['dnipadre'] ?: '—' }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; border-bottom:1px solid #e8ecda; font-size:14px;">
          <span style="color:#777; white-space:nowrap;">CUIL progenitor: </span>
          <span style="color:#1a1a1a; font-weight:bold; text-align:right;">{{ $formulario['cuilpadre'] ?: '—' }}</span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; font-size:14px;">
          <span style="color:#777; white-space:nowrap;">Adjunto declarado: </span>
          <span style="color:#1a1a1a; font-weight:bold; text-align:right;">
            {{ $tiposAdjunto[$formulario['tipoadjunto']] ?? '—' }}
          </span>
        </div>
      </div>

      <!-- Badge archivo -->
      <div style="margin-bottom:28px; text-align:right;">
        @if($formulario['archivo_actual'] && $formulario['tipoadjunto'] != 4)
          <span style="display:inline-block; background:#eaf5fb; color:#1a6a80; border:1px solid #a5d6e5; font-size:12px; padding:4px 12px; border-radius:20px;">
            Archivo adjunto cargado
          </span>
        @elseif($formulario['tipoadjunto'] == 4)
          <span style="display:inline-block; background:#fef9e0; color:#7a6800; border:1px solid #c7d100; font-size:12px; padding:4px 12px; border-radius:20px;">
            Sin archivo — No tiene acceso a la información
          </span>
        @else
          <span style="display:inline-block; background:#fcebeb; color:#793535; border:1px solid #f09595; font-size:12px; padding:4px 12px; border-radius:20px;">
            Sin archivo cargado
          </span>
        @endif
      </div>
    @endforeach
  </div>

  <!-- Footer -->
  <div style="background:white; border-top:1px solid #e8ecda; padding:20px 44px; border-radius:0 0 10px 10px; display:flex; justify-content:space-between; align-items:center;">
    <span style="font-size:11px; color:#aaa;">Mensaje generado automáticamente · No responder</span>
  </div>

</div>
</body>
</html>