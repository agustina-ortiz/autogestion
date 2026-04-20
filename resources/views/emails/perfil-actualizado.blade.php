<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Actualización de datos — Autogestión RR.HH.</title>
</head>
<body style="margin:0; padding:32px 16px; background-color:#f0f2f0; font-family:Arial,Helvetica,sans-serif;">
<div style="max-width:580px; margin:0 auto;">

  <!-- Header -->
  <div style="background-color:#81af00; padding:28px 36px; border-radius:10px 10px 0 0; display:flex; align-items:center; gap:16px;">
    <div>
      <p style="color:white; font-size:17px; font-weight:bold; margin:0 0 4px;">Municipalidad de Mercedes</p>
      <p style="color:rgba(255,255,255,0.82); font-size:13px; margin:0 0 10px;">Portal de Autogestión · Recursos Humanos</p>
      <span style="display:inline-block; background:rgba(255,255,255,0.22); color:white; font-size:11px; padding:4px 12px; border-radius:20px;">Actualización de datos personales</span>
    </div>
  </div>

  <!-- Accent bar -->
  <div style="height:4px; background:linear-gradient(to right, #81af00, #c7d100, #a5d6e5);"></div>

  <!-- Body -->
  <div style="background:white; padding:36px 44px;">
    <p style="font-size:14px; color:#555; line-height:1.7; margin:0 0 32px;">
      Se registró una solicitud de actualización de datos desde el portal de autogestión.
      A continuación se detallan el empleado involucrado y los cambios solicitados.
    </p>

    <!-- Datos del empleado -->
    <p style="font-size:11px; font-weight:bold; color:#999; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 14px;">Datos del empleado</p>
    <div style="background:#f7f8f2; border-radius:8px; padding:4px 20px; margin-bottom:24px;">
      <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; border-bottom:1px solid #e8ecda; font-size:14px;">
        <span style="color:#777; white-space:nowrap;">Nombre y apellido: </span>
        <span style="color:#1a1a1a; font-weight:bold; text-align:right;"> {{ $nombre }}</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center; gap:32px; padding:14px 0; font-size:14px;">
        <span style="color:#777; white-space:nowrap;">Legajo: </span>
        <span style="color:#1a1a1a; font-weight:bold; text-align:right;"> {{ $legajo }}</span>
      </div>
    </div>

    <!-- Motivo -->
    <div style="background:#f3f9e8; border-left:3px solid #81af00; border-radius:0 8px 8px 0; padding:16px 20px; margin-bottom:32px;">
      <p style="color:#81af00; font-weight:bold; font-size:13px; margin:0 0 6px;">{{ $motivo }}</p>
      <p style="color:#4a5e10; font-size:13px; line-height:1.6; margin:0;">{{ $mensaje }}</p>
    </div>

    @if (!is_null($direccion) || !is_null($direccion1) || !is_null($telefono) || !is_null($telefono1) || !is_null($email) || !is_null($email1))
      <p style="font-size:11px; font-weight:bold; color:#999; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 14px;">Cambios solicitados</p>
    @endif

    @if (!is_null($direccion) || !is_null($direccion1))
      <div style="margin-bottom:24px;">
        <p style="font-size:12px; color:#777; font-weight:bold; margin:0 0 10px;">Dirección</p>
        <div style="display:flex; gap:14px;">
          <div style="flex:1; padding:12px 16px; margin-right:12px; border-radius:6px; font-size:13px; line-height:1.5; background:#fef9e0; color:#7a6800; border:1px solid #c7d100;">
            <span style="font-size:10px; font-weight:bold; display:block; margin-bottom:5px; opacity:0.7;">Anterior</span>
            {{ $direccion1 ?? '—' }}
          </div>
          <div style="flex:1; padding:12px 16px; border-radius:6px; font-size:13px; line-height:1.5; background:#eaf5fb; color:#1a6a80; border:1px solid #a5d6e5;">
            <span style="font-size:10px; font-weight:bold; display:block; margin-bottom:5px; opacity:0.7;">Nueva</span>
            {{ $direccion ?? '—' }}
          </div>
        </div>
      </div>
    @endif

    @if (!is_null($telefono) || !is_null($telefono1))
      <div style="margin-bottom:24px;">
        <p style="font-size:12px; color:#777; font-weight:bold; margin:0 0 10px;">Teléfono</p>
        <div style="display:flex; gap:14px;">
          <div style="flex:1; padding:12px 16px; margin-right:12px; border-radius:6px; font-size:13px; line-height:1.5; background:#fef9e0; color:#7a6800; border:1px solid #c7d100;">
            <span style="font-size:10px; font-weight:bold; display:block; margin-bottom:5px; opacity:0.7;">Anterior</span>
            {{ $telefono1 ?? '—' }}
          </div>
          <div style="flex:1; padding:12px 16px; border-radius:6px; font-size:13px; line-height:1.5; background:#eaf5fb; color:#1a6a80; border:1px solid #a5d6e5;">
            <span style="font-size:10px; font-weight:bold; display:block; margin-bottom:5px; opacity:0.7;">Nuevo</span>
            {{ $telefono ?? '—' }}
          </div>
        </div>
      </div>
    @endif

    @if (!is_null($email) || !is_null($email1))
      <div style="margin-bottom:24px;">
        <p style="font-size:12px; color:#777; font-weight:bold; margin:0 0 10px;">Correo electrónico</p>
        <div style="display:flex; gap:14px;">
          <div style="flex:1; padding:12px 16px; border-radius:6px; font-size:13px; line-height:1.5; background:#fef9e0; color:#7a6800; border:1px solid #c7d100;">
            <span style="font-size:10px; font-weight:bold; display:block; margin-bottom:5px; opacity:0.7;">Anterior</span>
            {{ $email1 ?? '—' }}
          </div>
          <div style="flex:1; padding:12px 16px; margin-right:12px; border-radius:6px; font-size:13px; line-height:1.5; background:#eaf5fb; color:#1a6a80; border:1px solid #a5d6e5;">
            <span style="font-size:10px; font-weight:bold; display:block; margin-bottom:5px; opacity:0.7;">Nuevo</span>
            {{ $email ?? '—' }}
          </div>
        </div>
      </div>
    @endif

    @if (!is_null($imagen))
      <div style="margin-top:8px;">
        <p style="font-size:12px; color:#777; font-weight:bold; margin:0 0 10px;">Imagen adjunta</p>
        <div style="text-align:center; padding:12px 0;">
          <img src="{{ $imagen }}" alt="Imagen adjunta por el empleado" style="max-width:100%; border-radius:6px; border:1px solid #ddd;">
        </div>
      </div>
    @endif
  </div>

  <!-- Footer -->
  <div style="background:white; border-top:1px solid #e8ecda; padding:20px 44px; border-radius:0 0 10px 10px; display:flex; justify-content:space-between; align-items:center;">
    <span style="font-size:11px; color:#aaa;">Mensaje generado automáticamente · No responder</span>
  </div>

</div>
</body>
</html>