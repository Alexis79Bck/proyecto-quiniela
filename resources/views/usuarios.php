@extends('layout.app')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Listado de Usuarios</h4>
                <p class="card-description">
                    Administración de integrantes de la quiniela
                </p>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Usuario</th>
                                <th>Correo</th>
                                <th>Estado</th>
                                <th>Creado en</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $usuario)
                            <tr>
                                <td>{{ $usuario->nombre_completo }}</td>
                                <td>{{ $usuario->nombre_usuario }}</td>
                                <td>{{ $usuario->correo_electronico }}</td>
                                <td>
                                    @if($usuario->correo_verificado)
                                        <label class="badge badge-success">Verificado</label>
                                    @else
                                        <label class="badge badge-warning">Pendiente</label>
                                    @endif
                                </td>
                                <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection