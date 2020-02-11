@extends('layouts.backend')
@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
<style>
        table.dataTable td{
        box-sizing: inherit;
        }
        </style>
@endsection
@section('content')

@php
            $date = Carbon\Carbon::now();
            $usuario = Auth::user()->id; 
            $permisos = App\Permission::where('user_id', $usuario)->first();   
        @endphp
    <section class="container">
        <div class="row">
            <div id="divCalendario" style="display:none" class="col-md-12">
                
                    <div class="block">

                        <div class="block-content block-content-full text-right">
                                <button style="margin-bottom: 15px;" class="btn btn-primary" onclick="vista_lista()">
                                        <i class="fa fa-list"></i> <i>Vista Lista</i> 
                                    </button>
                    <div id='calendar'></div>
                        </div>
                    </div>
                </div>
        
       
    </div>
        <div class="content" id="PresupuestosActivos">
                <div class="block" id="divLista">
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                use App\Family;
                                $familias=Family::orderBy('nombre', 'ASC')->get();
                            @endphp
                            <form action="{{ route('inventario.filtro2') }}" method="POST">
                                @method('POST')
                                @csrf   
                                <div class="row" style="padding: 10px">
                                    @php
                                        $familia='-';
                                        $ban =0;
                                    @endphp
                                        @foreach ($Inventario as $inventario)
                                        @php
                                        if($ban>2){
                                            if($inventario->familia == $familia || $inventario->familia =='-'){
                                            $familia = $inventario->familia;
                                            }else{
                                                $ban++;
                                                $familia = "Todas Las Familias";
                                            }   
                                        }
                                        @endphp
                                        @endforeach
                                <p style="width: 100%; padding:15px; font-weight:bold; padding-bottom: 0">Familia Actual: {{$familia}}</p>
                             
                                <div class="col-md-3">
                                        <label for="">Familias:</label>
                                    <select name="familia" class="form-control" id="familia2" style="width: 100%" onchange="seleccionarFamilia()">
                                        <option value="">Todas las familias</option>
                                        @foreach($familias as $familia)    
                                            <option value="{{$familia->nombre}}">{{$familia->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" style="display: none">
                                    <div class="form-group">
                                        <label for="">Editado Desde:</label>
                                        <input type="date" name="fecha_1" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3" style="display: none">
                                    <div class="form-group">
                                            <label for="">Editado Hasta:</label>
                                        <input type="date" name="fecha_2" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3" style="padding-top:30px">
                                        <div class="form-group">
                                    <input type="submit" class="btn btn-sm btn-info" value="BUSCAR">
                                        </div>
                                </div>
                            </div>
                            </form>
                           
                        </div>
                    </div>
                    @php
                            $usuario = Auth::user()->id;    
                        @endphp 

                        
                    <div class="block-header block-header-default">
                        <div class="col-md-3">
                        <h3 class="block-title" style="color:green">Inventario</h3>
                        <form method="POST" action="{{route('imprimir.familia')}}" >
                                @method('POST')
                                @csrf 
                            <input type="hidden" name="familia" id="inputfamilia" value="">
                        <button class="btn btn-sm btn-info" type="submit">PDF inventario fisico</button>    
                        </form>    
                    </div>
                    <div class="col-md-9 text-right">
                         @if($permisos->inventarioAgregarFamilia==1)
                        <button onclick="finalizarInventarioFisico()" class="btn btn-danger">
                            Finalizar Inventario
                        </button>
                        @endif
                        
                       
                    </div>
                </div>
                    <div style="padding:15px; padding-top:30px;">
                    
                     <table style="font-size: 11px;" class="table table-bordered table-striped table-vcenter js-dataTable-full dataTable no-footer" id="TablaPresupuestos" role="grid" >
                            <thead>
                                <tr role="row">
                                    <th>Imagen</th>
                                    <th>Servicio</th>
                                    <th>Familia</th>
                                    <th>Actual en bodega</th>
                                    <th>Conteo Fisico Bodega</th>
                                    <th>Actual exhibición</th>
                                    <th>Conteo Fisico exhibición</th>
                                    <th>Opciones</th>
                                </tr>
                            </thead>
                            <tbody>                    
                                @if (!is_null($Inventario))
                                    @foreach ($Inventario as $inventario)    
                                    @php
                                        $servicio = App\PhysicalInventory::where('idProducto', $inventario->id)->get();
                                    @endphp
                                    @if(count($servicio)==0)
                            <tr role="row" class="odd">
                            <td class="text-center sorting_1"><img style="width: 80px" src="{{ $inventario->imagen}}"></td>
                                <td class="">{{ $inventario->servicio }}</td>
                                <td class="d-none d-sm-table-cell">{{ $inventario->familia }}</td>
                                <td>{{$inventario->cantidad}}
                                    <span id="aumentoBodega-{{ $inventario->id }}" style="color:green; display:none" class="fa fa-arrow-up"></span>
                                    <span id="disminucionBodega-{{ $inventario->id }}" style="color:red; display:none" class="fa fa-arrow-down"></span></td>
                                <td style="text-align:center; font-weight: bold" class="td-bodega" id="cantidad-{{ $inventario->id }}"  @if($usuario != 2) onclick="RegistrarActualizado({{ $inventario->id }}, {{ $inventario->cantidad }})" @endif>{{ $inventario->cantidad }}</td>
                                <td>{{$inventario->exhibicion}}
                                    <span id="aumentoExhibicion-{{ $inventario->id }}" style="color:green; display:none" class="fa fa-arrow-up"></span>
                                    <span id="disminucionExhibicion-{{ $inventario->id }}" style="color:red; display:none" class="fa fa-arrow-down"></span></td>
                                <td style="text-align:center; font-weight: bold" class="td-ex" id="exhibicion-{{ $inventario->id }}" onclick="RegistrarExhibicionActualizado({{ $inventario->id }}, {{ $inventario->cantidad }})"  @if($usuario != 2)  @endif>{{ $inventario->exhibicion }}</td>
                                @php
                                    $precioUnitario=number_format($inventario->precioUnitario,2);
                                @endphp
                                 @if($usuario != 2)
                                @endif
                                <td class="d-flex" style="box-sizing: content-box;">
                                    @if (Auth::user()->id == 17 )
                                    @else
                                        SIN PERMISOS
                                    @endif
                                    
                                </td>
                            </tr>
                            @else
                            @php
                                $servicioDatos = App\PhysicalInventory::where('idProducto', $inventario->id)->first();
                            @endphp
                            <tr role="row" class="odd">
                                <td class="text-center sorting_1"><img style="width: 80px" src="{{ $inventario->imagen}}"></td>
                                    <td class="">{{ $inventario->servicio }}</td>
                                    <td class="d-none d-sm-table-cell">{{ $inventario->familia }}</td>
                                    <td>{{$inventario->cantidad}}
                                        <span id="aumentoBodega-{{ $inventario->id }}" style="color:green; @if(($servicioDatos->fisicoBodega-$inventario->cantidad)>=0) display:inline @else display:none @endif" class="fa fa-arrow-up"></span>
                                        <span id="disminucionBodega-{{ $inventario->id }}" style="color:red; @if(($servicioDatos->fisicoBodega-$inventario->cantidad)<=0) display:inline @else display:none @endif" class="fa fa-arrow-down"></span></td>
                                    <td style="text-align:center; font-weight: bold" class="td-bodega" id="cantidad-{{ $inventario->id }}"  @if($usuario != 2) onclick="RegistrarActualizado({{ $inventario->id }}, {{ $inventario->cantidad }})" @endif>{{$servicioDatos->fisicoBodega}}</td>
                                    <td>{{$inventario->exhibicion}}
                                        <span id="aumentoExhibicion-{{ $inventario->id }}" style="color:green; @if(($servicioDatos->fisicoBodega-$inventario->exhibicion)>=0) display:inline @else display:none @endif" class="fa fa-arrow-up"></span>
                                        <span id="disminucionExhibicion-{{ $inventario->id }}" style="color:red; @if(($servicioDatos->fisicoBodega-$inventario->exhibicion)<=0) display:inline @else display:none @endif" class="fa fa-arrow-down"></span></td>
                                    <td style="text-align:center; font-weight: bold" class="td-ex" id="exhibicion-{{ $inventario->id }}" onclick="RegistrarExhibicionActualizado({{ $inventario->id }}, {{ $inventario->cantidad }})"  @if($usuario != 2)  @endif>{{$servicioDatos->fisicoExhibicion}}</td>
                                    @php
                                        $precioUnitario=number_format($inventario->precioUnitario,2);
                                    @endphp
                                     @if($usuario != 2)
                                    @endif
                                    <td class="d-flex" style="box-sizing: content-box;">
                                        @if (Auth::user()->id == 17 )
                                            <button style="display: none" type="button" style="margin-right:4px;" class="btn btn-sm btn-success archivar" data-toggle="tooltip" title="Confirmar Elemento" data-original-title="Confirmar Elemento">
                                                <i class="fa fa-check"></i> 
                                            </button>
                                        @else
                                            SIN PERMISOS
                                        @endif
                                        <i style="color:green; font-size:25px" class="fa fa-check"></i>
                                    </td>
                                </tr>
                            @endif
                            @endforeach
                            @endif
                       
                            
                            </tbody>
                     </table>
                            </div>
                        </div>
                </div>
                <!-- Vista presupuestos archivados -->
                
        <!-- modal paquete -->
        <div class="modal fade modalAgregarPaquete" id="agregarPaquete" tabindex="-1" role="dialog" aria-labelledby="agregarElemento" aria-hidden="true" style="overflow-y: scroll;">
            <div id="app" class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content" style="border: solid gray">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Crear nuevo paquete</h5>
                    <div  class="close" onClick="$('#agregarPaquete').modal('hide')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div  class="btn btn-secondary" onClick="$('#agregarPaquete').modal('hide')">Close</div>
                    <div  class="btn btn-primary" >Guardar paquete</div>
                </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="asignarAlta" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <altas-component></altas-component>
            </div>
            </div>
        </div>
                
               
    </section>
   
    @include('modals.agregarFamilia')
@endsection

@section("scripts")
    <script>
        $(function(){
            let archivar = document.getElementsByClassName('archivar');

            if(archivar.length != 0){
                for (var i = 0; i < archivar.length; i++) {
                    archivar[i].addEventListener('click', (e) => {
                        let id = e.target.dataset.archivar;
                    });
                }
            }
        })
        function archivarPresupuesto(){
    
            // Swal.fire({
            //     title: 'Are you sure?',
            //     text: "You won't be able to revert this!",
            //     type: 'warning',
            //     showCancelButton: true,
            //     confirmButtonColor: '#3085d6',
            //     cancelButtonColor: '#d33',
            //     confirmButtonText: 'Yes, delete it!'
            //     }).then((result) => {
            //     if (result.value) {
            //         let URL = 'inventario/delete/' id;
            //         Swal.fire(
            //         'Deleted!',
            //         'Your file has been deleted.',
            //         'success'
            //         )
            //     }
            // })
        }
        function seleccionarFamilia(){
            
           NombreFamilia = document.getElementById('familia2').value;
           
           //alert(NombreFamilia);
        document.getElementById('inputfamilia').value=NombreFamilia;
        }
        function editarCantidad(id){
            let nuevaCantidad = prompt('Ingresa la cantidad que quedara en bodega, si ingresas una cantidad menor a la actual, el sobrante pasara automaticamente a exhibicion, si ingresas una cantidad mayor, la diferencia se descontara a exhibición: ');
            let URL = 'editar-cantidad-inventario/' + id;

            let data = 'cantidad-' + id;
            let td = document.getElementById(data);

            parseInt(nuevaCantidad);

            if(isNaN(nuevaCantidad)){
                alert('Ingresa un valor valido');
            }else{
                console.log(td);

             axios.put(URL, {
                 'cantidad':  nuevaCantidad,
             }).then((response) => {
                td.innerHTML = nuevaCantidad;
                location.reload();
             }).catch((error) => {
                 console.log(error.data);
             })
            }

            
        }


        function RegistrarActualizado(id, cantidad){
            //alert(cantidad);
            let nuevaCantidad = prompt('Ingresa la cantidad obtenida en el inventario fisico');
            let URL = 'registrar-cantidad-actualizada/' + id;

            let data = 'cantidad-' + id;
            let aumento = 'aumentoBodega-' + id;
            let disminucion = 'disminucionBodega-' + id;
            let td = document.getElementById(data);
            let up = document.getElementById(aumento);
            let down = document.getElementById(disminucion);

           
            
            

            parseInt(nuevaCantidad);

            if(isNaN(nuevaCantidad)){
                alert('Ingresa un valor valido');
            }else{
             axios.put(URL, {
                 'cantidad':  nuevaCantidad,
             }).then((response) => {
                td.innerHTML = nuevaCantidad;
                
                if(nuevaCantidad>=cantidad){
                up.style.display="inline";
                down.style.display="none";
                }else{
                up.style.display="none";
                down.style.display="inline";  
                } 
               
                //location.reload();
             }).catch((error) => {
                 console.log(error.data);
             })
            }

            
        }

        function RegistrarExhibicionActualizado(id, cantidad){
            let nuevaCantidad = prompt('Ingresa la cantidad obtenida en el inventario fisico');



            let URL = 'registrar-cantidad-actualizada2/' + id;

            let data = 'exhibicion-' + id;
            let aumento = 'aumentoExhibicion-' + id;
            let disminucion = 'disminucionExhibicion-' + id;
            let td = document.getElementById(data);
            let up = document.getElementById(aumento);
            let down = document.getElementById(disminucion);
            

            parseInt(nuevaCantidad);

            if(isNaN(nuevaCantidad)){
                alert('Ingresa un valor valido');
            }else{
             axios.put(URL, {
                 'cantidad':  nuevaCantidad,
             }).then((response) => {
                td.innerHTML = nuevaCantidad;
                if(nuevaCantidad>=cantidad){
                up.style.display="inline";
                down.style.display="none";
                }else{
                up.style.display="none";
                down.style.display="inline";  
                } 
               
                //location.reload();
             }).catch((error) => {
                 console.log(error.data);
             })
            }   
        }

        function finalizarInventarioFisico(){
            var opcion = confirm("Al aceptar finalizar el inventario fisico se remplazaran las cantidades actuales en el inventario con las nuevas especificadas");
    if (opcion == true) {
        mensaje = "Inventario Fisico Actualizado";
	} else {
	    mensaje = "Inventario Fisico Cancelado";
	}
        }

        function editarExhibicion(id){
            let nuevaCantidad = prompt('Ingresa la cantidad: ');
            let URL = 'editar-exhibicion-inventario/' + id;

            let data = 'exhibicion-' + id;
            let td = document.getElementById(data);

            console.log(td);

             axios.put(URL, {
                 'exhibicion':  nuevaCantidad,
             }).then((response) => {
                console.log('Cantidad actualizada');
                td.innerHTML = nuevaCantidad;
             }).catch((error) => {
                 console.log(error.data);
             })
        }

        function vista_calendario(){
            document.getElementById('divCalendario').style.display="block";
            document.getElementById('divLista').style.display="none";
        }
    function vista_lista(){
        document.getElementById('divCalendario').style.display="none";
        document.getElementById('divLista').style.display="block";
    }
    function archivarCliente(){
        Swal.fire({
            title: '¿Estas seguro de archivar este presupuesto?',
            text: "Al archivar un presupuesto dejara de estar disponible en la tabla de presupuestos",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Confirmar'
            
        }).then((result) => {
        if (result.value) {
            var url= '/presupuestos/eliminar-presupuestos/'+task;
            axios.delete(url).then(response =>{
                this.obtenerTareas();
                }) 
            }
                            
        })
    }
   
   
    </script>

@endsection
