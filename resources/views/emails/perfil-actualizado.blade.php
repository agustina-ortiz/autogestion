<html>
<p>
    <strong>Nombre y Apellido</strong>: {{ $nombre }}
    <br>
    <strong>Legajo</strong>: {{ $legajo }}
    <br>
    <strong>Motivo</strong>: {{ $motivo }}
    <br>
    <strong>Mensaje</strong>: {{ $mensaje }}
    @if (!is_null($direccion) || !is_null($direccion1))
        <br>
        <strong>Direccion nueva</strong>: {{ $direccion }} <strong>Direccion anterior</strong>: {{ $direccion1 }}
    @endif
    @if (!is_null($telefono) || !is_null($telefono1))
        <br>
        <strong>Telefono nuevo</strong>: {{ $telefono }} <strong>Telefono anterior</strong>: {{ $telefono1 }}
    @endif
    @if (!is_null($email) || !is_null($email1))
        <br>
        <strong>Mail nuevo</strong>: {{ $email }} <strong>Mail anterior</strong>: {{ $email1 }}
    @endif
    @if (!is_null($imagen))
        <br>
        <img src='{{ $imagen }}'>
    @endif
</p>
</html>
