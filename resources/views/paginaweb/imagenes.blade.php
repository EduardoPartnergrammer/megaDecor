@extends('layouts.backend')
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
@endsection
@section('content')

    <section class="container">
                
            <div class="content" id="PresupuestosActivos">
            <div class="block" id="divLista">
                <div class="block-header block-header-default">
                    <div class="col-md-3">
                    <h3 class="block-title" style="color:green">Galería {{$galeria->name}}</h3>
                    </div>
                    <div class="col-md-9 text-right">
                        
                    </div>
                </div>

                <div class="">
                    <form action="{{ asset('/proyecto/'.$galeria->id.'/imagenes') }}"
                    class="dropzone"
                    id="my-awesome-dropzone">
                    {{ csrf_field() }}
              </form>
                </div>
                    <div style="padding:15px; padding-top:30px;">
                        <table  style="font-size: 11px" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" id="TablaPresupuestos" role="grid" >
                            <thead>
                                <tr role="row">
                                    <th>Foto Principal</th>
                                    <th class="d-none d-sm-table-cell">Categoría</th>
                                    <th>Opciones</th>
                                </tr>
                                @foreach ($imagenes as $imagen)
                                <tr>
                                <td><img style="width: 80px" src="{{ $imagen->imagen}}"></td>
                                <td>{{$imagen->created_at}}</td>
                                <td><form id="delete-photo-{{ $imagen->id }}" action="{{ route('photo.delete', $imagen->id) }}" method="POST" class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                                </tr>
                                @endforeach
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
         
               
    </section>
   
    
@endsection
@section("scripts")
<script>

Dropzone.options.myAwesomeDropzone = {
    paramName: "file", // Las imágenes se van a usar bajo este nombre de parámetro
    maxFilesize: 50 // Tamaño máximo en MB
};
</script>

@endsection