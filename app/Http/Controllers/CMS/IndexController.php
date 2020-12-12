<?php

namespace App\Http\Controllers\CMS;

use App\Task;
use App\User;
use stdClass;
use App\Budget;
use App\Supplier;
use App\Testimonials;
use App\SupplierTelephone;
use App\Client;
use App\Gallery;
use App\Photo;
use App\Missing;
use App\Inventory;
use App\Vehicle;
use App\Telephone;
use Carbon\Carbon;
use App\BudgetPack;
use App\Permission;
use App\Payment;
use App\OtherPayments;
use App\MoralPerson;
use App\CashRegister;
use App\AuthorizedPack;
use App\PhysicalPerson;
use App\BudgetInventory;
use App\BudgetPackInventory;
use App\PhysicalInventory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;



class IndexController extends Controller
{
    public function clientes(){

        //Obtenemos clientes
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion', 'moral_people.created_at')
        ->get();
        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.apellidoMaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion', 'physical_people.created_at')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        $CompleteClients=[];

        foreach($clientes as $cliente){
            
            $tamanoPresupuestos=0;
            $telefono = Telephone::orderBy('id', 'DESC')->where('client_id', $cliente->id)->first();

            //Obtenemos numero de presupuestos del cliente
            $Presupuestos = Budget::orderBy('id', 'DESC')->where('client_id', $cliente->id)->get()->toArray();

            $createdAt=date('d-m-Y',(strtotime($cliente->created_at)));
                        $CompleteClient = new stdClass();
                        $CompleteClient->id = $cliente->id;
                        
                        $tipoCliente = Client::where('id', $cliente->id)->first();
                        //dd($tipoCliente->tipoPersona);

                        if($tipoCliente->tipoPersona=='MORAL'){
                        $CompleteClient->nombre = $cliente->nombre;
                        }else{
                        $CompleteClient->nombre = $cliente->nombre.' '.$cliente->apellidoPaterno.' '.$cliente->apellidoMaterno;  
                        }
                    

                    
                        $CompleteClient->email = $cliente->email;
                        $CompleteClient->created_at = $createdAt;
                        $CompleteClient->presupuestos = $Presupuestos;
                        if(!is_null($telefono)){
                        $CompleteClient->telefono = $telefono->numero;}
                        else{
                            $CompleteClient->telefono = "--";  
                        }
                        array_push($CompleteClients,$CompleteClient);    
        }
    return view('clientes',compact('CompleteClients'));    
    }

    public function clientes2(){

        //Obtenemos clientes
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion', 'moral_people.created_at')
        ->get();
        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.apellidoMaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion', 'physical_people.created_at')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        $CompleteClients=[];

        foreach($clientes as $cliente){
            
            $tamanoPresupuestos=0;
            $telefono = Telephone::orderBy('id', 'DESC')->where('client_id', $cliente->id)->first();

            //Obtenemos numero de presupuestos del cliente
            $Presupuestos = Budget::orderBy('id', 'DESC')->where('client_id', $cliente->id)->get()->toArray();

            $createdAt=date('d-m-Y',(strtotime($cliente->created_at)));
                        $CompleteClient = new stdClass();
                        $CompleteClient->id = $cliente->id;
                        
                        $tipoCliente = Client::where('id', $cliente->id)->first();
                        //dd($tipoCliente->tipoPersona);

                        if($tipoCliente->tipoPersona=='MORAL'){
                        $CompleteClient->nombre = $cliente->nombre;
                        }else{
                        $CompleteClient->nombre = $cliente->nombre.' '.$cliente->apellidoPaterno.' '.$cliente->apellidoMaterno;  
                        }
                    

                    
                        $CompleteClient->email = $cliente->email;
                        $CompleteClient->created_at = $createdAt;
                        $CompleteClient->presupuestos = $Presupuestos;
                        if(!is_null($telefono)){
                        $CompleteClient->telefono = $telefono->numero;}
                        else{
                            $CompleteClient->telefono = "--";  
                        }
                        array_push($CompleteClients,$CompleteClient);    
        }
    return view('clientes2',compact('CompleteClients'));    
    }

   

   
    public function contratos(){
        $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->get();
        $Presupuestos=[];
      
        //Obtenemos clientes morales y fisicos
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
        ->get();

        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        foreach($budgets as $budget){
         $Presupuesto   = new stdClass();
         $Presupuesto->id = $budget->id;
         $Presupuesto->folio = $budget->folio;
         $Presupuesto->fechaEvento = $budget->fechaEvento;
         //$Presupuesto->vendedor = $budget->vendedor_id;
         $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
         $Presupuesto->vendedor = $DatosVendedor->name;
         $Presupuesto->version = $budget->version;
         $Presupuesto->pagado = $budget->pagado;
         $Presupuesto->updated_at = $budget->updated_at;
         

         foreach($clientes as $cliente){
             if($cliente->id===$budget->client_id){
                 
         $Presupuesto->cliente = $cliente->nombre;
                if($budget->lugarEvento = 'MISMA'){
                    $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $Presupuesto->lugarEvento = $budget->lugarEvento;
                }
                
        }else{$Presupuesto->cliente = "--";}
        }

         array_push($Presupuestos,$Presupuesto);
        }

        

        //dd($clientes);
        return view('contratos',compact('Presupuestos')); 
    }

    public function contratosTodos(){
        $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->where('archivado', '0')->get();
        $Presupuestos=[];
      
        //Obtenemos clientes morales y fisicos
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
        ->get();

        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        foreach($budgets as $budget){
         $Presupuesto   = new stdClass();
         $Presupuesto->id = $budget->id;
         $Presupuesto->folio = $budget->folio;
         $Presupuesto->fechaEvento = $budget->fechaEvento;
         //$Presupuesto->vendedor = $budget->vendedor_id;
         $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
         $Presupuesto->vendedor = $DatosVendedor->name;
         $Presupuesto->version = $budget->version;
         $Presupuesto->updated_at = $budget->updated_at;
         $Presupuesto->servicio = $budget->tipoEvento." ".$budget->tipoServicio;
         $Presupuesto->notasPresupuesto = $budget->notasPresupuesto;
         $Presupuesto->horaEventoInicio = $budget->horaEventoInicio;
         $Presupuesto->horaEventoFin = $budget->horaEventoFin;
         
            
            
         foreach($clientes as $cliente){
             if($cliente->id==$budget->client_id){
         $Presupuesto->cliente = $cliente->nombre;
                if($budget->lugarEvento = 'MISMA'){
                    $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $Presupuesto->lugarEvento = $budget->lugarEvento;
                }
                
        }else{$Presupuesto->cliente = "--";}
        }
        $arregloCliente = Client::orderBy('id', 'DESC')->where('id', $budget->client_id)->first();
        $Presupuesto->cliente = $arregloCliente->nombreCliente;

         array_push($Presupuestos,$Presupuesto);
        }

        //dd($clientes);
        return $Presupuestos; 
    }

    public function presupuestosTodos(){
    $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'PRESUPUESTO')->where('archivado', '0')->get();
    $Presupuestos=[];
  
    //Obtenemos clientes morales y fisicos
    $clientes_morales = DB::table('clients')
    ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
    ->select('clients.id', 'moral_people.nombre', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
    ->get();

    $clientes_fisicos = DB::table('clients')
    ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
    ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
    ->get();
    
    $clientes = $clientes_morales->merge($clientes_fisicos);

    foreach($budgets as $budget){
     $Presupuesto   = new stdClass();
     $Presupuesto->id = $budget->id;
     $Presupuesto->folio = $budget->folio;
     $Presupuesto->fechaEvento = $budget->fechaEvento;
     //$Presupuesto->vendedor = $budget->vendedor_id;
     $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
     $Presupuesto->vendedor = $DatosVendedor->name;
     $Presupuesto->version = $budget->version;
     $Presupuesto->updated_at = $budget->updated_at;
     $Presupuesto->servicio = $budget->tipoEvento." ".$budget->tipoServicio;
     $Presupuesto->notasPresupuesto = $budget->notasPresupuesto;
     $Presupuesto->horaEventoInicio = $budget->horaEventoInicio;
     $Presupuesto->horaEventoFin = $budget->horaEventoFin;
     
        
        
     foreach($clientes as $cliente){
         if($cliente->id==$budget->client_id){
     $Presupuesto->cliente = $cliente->nombre;
            if($budget->lugarEvento = 'MISMA'){
                $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                
            }else{
                $Presupuesto->lugarEvento = $budget->lugarEvento;
            }
            
    }else{$Presupuesto->cliente = "--";}
    }
    $arregloCliente = Client::orderBy('id', 'DESC')->where('id', $budget->client_id)->first();
    $Presupuesto->cliente = $arregloCliente->nombreCliente;

     array_push($Presupuestos,$Presupuesto);
    }

    //dd($clientes);
    return $Presupuestos; 
}
public function archivarUsuario($id){
    $budget=User::find($id);
    if($budget->archivado ==1){
    $budget->archivado='0';
    }else{
    $budget->archivado='1';
    }
    $budget->save();
    return back();
}
    //Pantalla usuarios
    public function pantallaUsuarios(){
        $Usuarios = User::orderBy('id', 'DESC')->get();
        return view('pantallaUsuarios', compact('Usuarios'));
    }

    public function usuariosPermisos($id){

        $Usuario = User::findOrFail($id);
        $Permisos = Permission::where('user_id', $Usuario->id)->first();

        //dd($Permisos);

        return view('usuariosPermisos', compact('Usuario' , 'Permisos'));
    }
    //Permisos
    public function obtenerPermisos(){
        $usuario = Auth::user()->id; 
        $permisos = Permission::where('user_id', $usuario)->first();
        return $permisos;
    }

    public function editarPermisos(Request $request, $id){
        //dd($request);
       
        $Permisos = Permission::where('id', $id)->first();
        $Permisos->delete();
        $Permisos= Permission::create($request->all());
        
        $Permisos->fill($request->all())->save();

        return redirect()->route('usuario.permisos', $Permisos->user_id);
    }
    
     //Pantalla inventario
     public function inventario(){
        return view('inventario');
    }


    //Pantalla inventario pruebas
    public function inventariotest(){
        
        $Inventario = Inventory::orderBy('id', 'DESC')->get();
        $inventarioBudget = BudgetInventory::where('guardarInventario', true)->get()->toArray();
        $inventarioPack = BudgetPackInventory::where('guardarInventario', true)->get()->toArray();
        
        $inventarioAuth = array_merge($inventarioBudget, $inventarioPack);
        //dd($inventarioAuth);

        return view('inventariotest', compact('Inventario', 'inventarioAuth'));
    }

    public function inventario2(){

        $Inventario = Inventory::orderBy('id', 'DESC')->get();
        $inventarioBudget = BudgetInventory::where('guardarInventario', true)->get()->toArray();
        $inventarioPack = BudgetPackInventory::where('guardarInventario', true)->get()->toArray();
        
        $inventarioAuth = array_merge($inventarioBudget, $inventarioPack);
        //dd($inventarioAuth);

        return view('inventario2', compact('Inventario', 'inventarioAuth'));
    }

    public function inventario3($id){
        $familiaSelect = $id;

        $Inventario = Inventory::orderBy('id', 'DESC')->where('familia', $id)->get();
        $inventarioBudget = BudgetInventory::where('guardarInventario', true)->get()->toArray();
        $inventarioPack = BudgetPackInventory::where('guardarInventario', true)->get()->toArray();
        
        $inventarioAuth = array_merge($inventarioBudget, $inventarioPack);
        //dd($inventarioAuth);

        return view('inventario3', compact('Inventario', 'inventarioAuth', 'familiaSelect'));
    }


    //Funciones Calculadora Vehiculos
    public function obtenerVehiculos(){
        return Vehicle::orderBy('id', 'DESC')->where('tipo', 'Vehiculo')->get();
    }
    public function obtenerCasetas(){
        return Vehicle::orderBy('id', 'DESC')->where('tipo', 'Caseta')->get();
    }

    public function agregarVehiculo(Request $request){
        //dd($request);
        // Guardo un nueva categorÃ­a
        $tipo = new Vehicle(); 
        $tipo->nombre = $request->nombre;
        $tipo->tipo = $request->tipo;
        $tipo->consumo = $request->rendimiento;
        if($request->combustible == ''){
            $tipo->combustible = 'Gasolina';
        }else{
        $tipo->combustible = $request->combustible;}
        $tipo->save();
    }
    public function deleteVehiculo($id){
        $tipo = Vehicle::find($id);
        $tipo->delete();
    }


     //FIN Funciones Calculadora Vehiculos

    public function inventario4($id){
        
        $familiaSelect = $id;
        $Inventario = Inventory::orderBy('id', 'DESC')->where('familia', $id)->get();
        $inventarioBudget = BudgetInventory::where('guardarInventario', true)->get()->toArray();
        $inventarioPack = BudgetPackInventory::where('guardarInventario', true)->get()->toArray();
        
        $inventarioAuth = array_merge($inventarioBudget, $inventarioPack);
        //dd($inventarioAuth);

        return view('inventario4', compact('Inventario', 'inventarioAuth', 'familiaSelect'));
    }

    public function registrarDif(Request $request, $id){
        $producto = PhysicalInventory::where('idProducto', $id)->first();

        $producto->diferencia = !$producto->diferencia;
        $producto->save();
    }

    public function comisiones(){
        $fecha_actual= date('Y-m-d',time());
        //Empleado del mes
        $EmpleadoDelMes = DB::table('budgets')
        ->select(DB::raw('count(*) as ventas_count, vendedor_id'))
        ->where('tipo', '=', 'CONTRATO')
        ->groupBy('vendedor_id')
        ->get();
        
        $ventas=0;
        if(count($EmpleadoDelMes) != 0){
            foreach ($EmpleadoDelMes as $EmpleadoMes) {
                if(($EmpleadoMes->ventas_count) > $ventas){
                    $ventas = $EmpleadoMes->ventas_count;
                    $vendedorMes=$EmpleadoMes->vendedor_id;
                   
                }
            }
    
        $ArrayEmpleadoDelMes = User::orderBy('id', 'DESC')->where('id', $vendedorMes)->first();
    
        }else{
            $ArrayEmpleadoDelMes=null;
        }
        $Usuarios = User::orderBy('id', 'DESC')->get();

        //Construir arreglo que se enviara a la lista
        $CompleteUsers=[];

        foreach($Usuarios as $usuario){
            $num_ventas=0;
        foreach($EmpleadoDelMes as $Empleado){
            
            if($Empleado->vendedor_id == $usuario->id){
                $num_ventas = $Empleado->ventas_count;
            } 
        }
            $budgetsDeVendedor = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('vendedor_id', $usuario->id)->get();
            $totalVentas=0;
            foreach($budgetsDeVendedor as $budgetDeVendedor){
                $totalVentas=$totalVentas+$budgetDeVendedor->total;
            }
            
            $CompleteUser = new stdClass();
            $CompleteUser->totalventas = number_format($totalVentas);
            $CompleteUser->id = $usuario->id;
            $CompleteUser->name = $usuario->name;
            $CompleteUser->ventas = $num_ventas;
            
            array_push($CompleteUsers,$CompleteUser); 
    }


    // Nueva Version Comisiones---------------------------------------->
    $date = Carbon::now();
    $ContratosDelMes = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->whereMonth('fechaEvento', $date)->get();
    
    $Vendedores = User::orderBy('id', 'DESC')->where('tipo', '!=','BODEGA')->where('tipo', '!=','CONTABILIDAD')->get();
    
    foreach($Vendedores  as $vendedor){
        
    }


    //Obtenemos totales de venta de los vendedores
    

   
       
        return view('comisiones', compact( 'ArrayEmpleadoDelMes', 'CompleteUsers', 'Vendedores'));
   
    }





    //Vista dasboard
    public function dashboard(){
         $fecha_actual= date('Y-m-d',time());
        //Presupuestos activos
        
        //calculo adeudo total
        $adeudoTotal = 0;
        $contratosAdeudo = Budget::orderBy('id', 'DESC')->where('pagado', '!=', true)->where('archivado', 'FALSE')->where('tipo', 'CONTRATO')->where('fechaEvento', '!=', null)->get();
        foreach($contratosAdeudo as $contratoAdeudo){
            $totalAbono=0;
            $pagosContrato = Payment::where('budget_id', $contratoAdeudo->id)->get();

            foreach($pagosContrato as $currentPayment){
                if($currentPayment->method=='DOLAR'){
                $totalAbono=$totalAbono+($currentPayment->amount*$currentPayment->reference);}
                else{
                    $totalAbono=$totalAbono+$currentPayment->amount;
                }
            }

            $banIva =1;
                                if($contratoAdeudo->opcionIVA){
                                    $banIva =1.16;
                                }else{
                                    $banIva =1;
                                }
            if((($contratoAdeudo->total*$banIva) - $totalAbono) > 0 ){
                $adeudoTotal=$adeudoTotal+(($contratoAdeudo->total*$banIva) - $totalAbono);

            }
        }
       
         //Fincalculo adeudo total

        $fechaHoy = Carbon::yesterday();
        
        $numeroPresupuestos = Budget::orderBy('id', 'DESC')->where('tipo', 'PRESUPUESTO')->where('archivado', FALSE)->where('pendienteFecha', '=', TRUE)->get();
        $numeroPresupuestosF = Budget::orderBy('id', 'DESC')->where('tipo', 'PRESUPUESTO')->where('archivado', FALSE)->whereDate('fechaEvento', '>', $fechaHoy)->get();
        //Presupuestos del dia actual
        $numeroPresupuestosDiaActual = Budget::orderBy('id', 'DESC')->where('fechaEvento', $fecha_actual)->where('tipo', 'CONTRATO')->get();
        //Empleado del mes
        $EmpleadoDelMes = DB::table('budgets')
        ->select(DB::raw('count(*) as ventas_count, vendedor_id'))
        ->where('tipo', '=', 'CONTRATO')
        ->groupBy('vendedor_id')
        ->get();
        
        $ventas=0;
        if(count($EmpleadoDelMes) != 0){
            foreach ($EmpleadoDelMes as $EmpleadoMes) {
                if(($EmpleadoMes->ventas_count) > $ventas){
                    $ventas = $EmpleadoMes->ventas_count;
                    $vendedorMes=$EmpleadoMes->vendedor_id;
                }
            }
    
        $ArrayEmpleadoDelMes = User::orderBy('id', 'DESC')->where('id', $vendedorMes)->first();
    
        }else{
            $ArrayEmpleadoDelMes=null;
        }


        //Comparacion ventas actuales con años pasados
            //Obtenemos contratos del año pasado pero del mes acutal
            $fecha_ano_pasado= date('Y-m',strtotime($fecha_actual."- 365 days"));
            $presupuestosAnoPasado = Budget::orderBy('id', 'DESC')->where('fechaEvento', 'like' , $fecha_ano_pasado.'%')->where('tipo', 'CONTRATO')->get();
            //Obtenemos los contratos de el año y mes actual
            $fecha_mes_actual= date('Y-m',strtotime($fecha_actual."- 0 days"));
            $presupuestosAnoActual = Budget::orderBy('id', 'DESC')->where('fechaEvento', 'like' , $fecha_mes_actual.'%')->where('tipo', 'CONTRATO')->get();

            if(count($presupuestosAnoActual) !== 0 && count($presupuestosAnoPasado) !== 0){
                $porcentajeActual= (100/71 /*count($presupuestosAnoPasado)*/) * count($presupuestosAnoActual);
            }else{
                $porcentajeActual = 0;
            }
            
            
            //calculamos total ventas del año pasado
            $ventasAnoPasado=0;
            $ventasAnoActual=0;
            //obtenemos ventas año pasado
            foreach($presupuestosAnoPasado as $anoPasado){
                $ventasAnoPasado= $ventasAnoPasado+$anoPasado->total;
            }
            //obtenemos ventas año actual
            foreach($presupuestosAnoActual as $anoActual){
                $ventasAnoActual=$ventasAnoActual+$anoActual->total;
            }
               
            if($ventasAnoPasado != 0){
            
                $porcentajeActualDinero = (100/$ventasAnoPasado) * $ventasAnoActual;
                $diferenciaDinero = $ventasAnoActual-$ventasAnoPasado;
             
            }else{
                $porcentajeActualDinero = 0;
                $diferenciaDinero = 0;
            }
          
            

            //Obtenemos datos para tabla comparativa de comisiones
            $Vendedores = User::orderBy('id', 'DESC')->get();

            $ElementosVendedores=[];
            foreach($Vendedores as $Vendedor){
                $idVendedor=$Vendedor->id;
                $ElementoVendedor = new stdClass();
                $ElementoVendedor->name = $Vendedor->name;

                $fecha_mes_actual= date('Y-m',strtotime($fecha_actual."- 0 days"));
                $PresupuestosVendedor = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('vendedor_id', $idVendedor)->where('fechaEvento', 'like' , $fecha_mes_actual.'%')->get();
                $ElementoVendedor->ventas = count($PresupuestosVendedor);
                $ElementoVendedor->cantidadVenta=0;
                foreach($PresupuestosVendedor as $PresupuestoVendedor){
                        $ElementoVendedor->cantidadVenta = $ElementoVendedor->cantidadVenta+$PresupuestoVendedor->total;
                    }

                if($ElementoVendedor->ventas>0){
                array_push($ElementosVendedores,$ElementoVendedor); }
            }
            arsort($ElementosVendedores);
    
           
        $tasks = Task::orderBy('id', 'DESC')->get();
        return view('dashboard', compact('tasks', 'numeroPresupuestos','numeroPresupuestosF', 'numeroPresupuestosDiaActual', 'ArrayEmpleadoDelMes', 'presupuestosAnoPasado', 'presupuestosAnoActual', 'porcentajeActual', 'ventasAnoActual', 'ventasAnoPasado', 'porcentajeActualDinero', 'ElementosVendedores', 'diferenciaDinero', 'adeudoTotal'));
        
        $ventas=0;
        if(count($EmpleadoDelMes) != 0){
            foreach ($EmpleadoDelMes as $EmpleadoMes) {
                if(($EmpleadoMes->ventas_count) > $ventas){
                    $ventas = $EmpleadoMes->ventas_count;
                    $vendedorMes=$EmpleadoMes->vendedor_id;
                }
            }
    
        $ArrayEmpleadoDelMes = User::orderBy('id', 'DESC')->where('id', $vendedorMes)->first();
    
        }else{
            $ArrayEmpleadoDelMes=null;
        }


        //Comparacion ventas actuales con años pasados
            //Obtenemos contratos del año pasado pero del mes acutal
            $fecha_ano_pasado= date('Y-m',strtotime($fecha_actual."- 365 days"));
            $presupuestosAnoPasado = Budget::orderBy('id', 'DESC')->where('fechaEvento', 'like' , $fecha_ano_pasado.'%')->where('tipo', 'CONTRATO')->get();
            //Obtenemos los contratos de el año y mes actual
            $fecha_mes_actual= date('Y-m',strtotime($fecha_actual."- 0 days"));
            $presupuestosAnoActual = Budget::orderBy('id', 'DESC')->where('fechaEvento', 'like' , $fecha_mes_actual.'%')->where('tipo', 'CONTRATO')->get();

            if(count($presupuestosAnoActual) !== 0 && count($presupuestosAnoPasado) !== 0){
                $porcentajeActual= (100/count($presupuestosAnoPasado)) * count($presupuestosAnoActual);
            }else{
                $porcentajeActual = 0;
            }
            
            
            //calculamos total ventas del año pasado
            $ventasAnoPasado=0;
            $ventasAnoActual=0;
            //obtenemos ventas año pasado
            foreach($presupuestosAnoPasado as $anoPasado){
                $ventasAnoPasado= $ventasAnoPasado+$anoPasado->total;
            }
            //obtenemos ventas año actual
            foreach($presupuestosAnoActual as $anoActual){
                $ventasAnoActual=$ventasAnoActual+$anoActual->total;
            }
               
            if($ventasAnoPasado != 0){
            
                $porcentajeActualDinero = (100/$ventasAnoPasado) * $ventasAnoActual;
                $diferenciaDinero = $ventasAnoActual-$ventasAnoPasado;
             
            }else{
                $porcentajeActualDinero = 0;
                $diferenciaDinero = 0;
            }
          
            

            //Obtenemos datos para tabla comparativa de comisiones
            $Vendedores = User::orderBy('id', 'DESC')->get();

            $ElementosVendedores=[];
            foreach($Vendedores as $Vendedor){
                $idVendedor=$Vendedor->id;
                $ElementoVendedor = new stdClass();
                $ElementoVendedor->name = $Vendedor->name;

                $fecha_mes_actual= date('Y-m',strtotime($fecha_actual."- 0 days"));
                $PresupuestosVendedor = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('vendedor_id', $idVendedor)->where('fechaEvento', 'like' , $fecha_mes_actual.'%')->get();
                $ElementoVendedor->ventas = count($PresupuestosVendedor);
                $ElementoVendedor->cantidadVenta=0;
                foreach($PresupuestosVendedor as $PresupuestoVendedor){
                        $ElementoVendedor->cantidadVenta = $ElementoVendedor->cantidadVenta+$PresupuestoVendedor->total;
                    }

                if($ElementoVendedor->ventas>0){
                array_push($ElementosVendedores,$ElementoVendedor); }
            }
            arsort($ElementosVendedores);


        $tasks = Task::orderBy('id', 'DESC')->get();
        return view('dashboard', compact('tasks', 'numeroPresupuestos', 'numeroPresupuestosDiaActual', 'ArrayEmpleadoDelMes', 'presupuestosAnoPasado', 'presupuestosAnoActual', 'porcentajeActual', 'ventasAnoActual', 'ventasAnoPasado', 'porcentajeActualDinero', 'ElementosVendedores', 'diferenciaDinero'));
    }

    public function presupuestos(){
        $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'PRESUPUESTO')->where('archivado', '0')->get();

        $fechaHoy = Carbon::yesterday();
        $presupuestosHistorial = Budget::orderBy('id', 'DESC')->where('tipo', 'PRESUPUESTO')->where('archivado', 0)->whereDate('fechaEvento', '<=', $fechaHoy)->get();
        $Presupuestos=[];
      
        //Obtenemos clientes morales y fisicos
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.nombre as apellidoPaterno', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
        ->get();

        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        foreach($budgets as $budget){
            if($budget->fechaEvento >= $fechaHoy || $budget->fechaEvento == null){
                $Presupuesto   = new stdClass();
                $Presupuesto->id = $budget->id;
                $Presupuesto->folio = $budget->folio;
                $Presupuesto->fechaEvento = $budget->fechaEvento;
                //$Presupuesto->vendedor = $budget->vendedor_id;
                $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
                $Presupuesto->vendedor = $DatosVendedor->name;
                $Presupuesto->version = $budget->version;
                $Presupuesto->impresion = $budget->impresion;
                $Presupuesto->enviado = $budget->enviado;
                $Presupuesto->pendienteFecha = $budget->pendienteFecha;
                if($budget->opcionIVA==1){
                    $Presupuesto->total = ($budget->total)+($budget->total*.16);
                    $Presupuesto->IVA = true;
                }else{
                    $Presupuesto->total = $budget->total;
                    $Presupuesto->IVA = false;
                }
                $Presupuesto->impresionBodega = $budget->impresionBodega;
                $Presupuesto->updated_at = $budget->updated_at;
            
         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budget->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$Presupuesto->cliente = $cliente->nombre;}else{
                     $Presupuesto->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $Presupuesto->lugarEvento = $budget->lugarEvento;
                }
                
        }
        }

         array_push($Presupuestos,$Presupuesto);
        }
        }


        //Obtenemos los archivados
        $budgetsArchivados = Budget::orderBy('id', 'ASC')->where('tipo', 'PRESUPUESTO')->where('archivado', '1')->get();
        $PresupuestosArchivados=[];
      
        //No obtenemos clientes por que ya los tenemos arriba
        foreach($budgetsArchivados as $budgetArchivados){
         $PresupuestoArchivados   = new stdClass();
         $PresupuestoArchivados->id = $budgetArchivados->id;
         $PresupuestoArchivados->folio = $budgetArchivados->folio;
         $PresupuestoArchivados->fechaEvento = $budgetArchivados->fechaEvento;
         //$Presupuesto->vendedor = $budget->vendedor_id;
         $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budgetArchivados->vendedor_id)->first();
         $PresupuestoArchivados->vendedor = $DatosVendedor->name;
         $PresupuestoArchivados->version = $budgetArchivados->version;
         $PresupuestoArchivados->impresion = $budgetArchivados->impresion;
         $PresupuestoArchivados->enviado = $budgetArchivados->enviado;
         $PresupuestoArchivados->total = $budgetArchivados->total;
         $PresupuestoArchivados->pendienteFecha = $budgetArchivados->pendienteFecha;
         if($budgetArchivados->opcionIVA==1){
            $PresupuestoArchivados->total = ($budgetArchivados->total)+($budgetArchivados->total*.16);
        }else{
            $PresupuestoArchivados->total = $budgetArchivados->total;
        }
         $PresupuestoArchivados->impresionBodega = $budgetArchivados->impresionBodega;
         $PresupuestoArchivados->updated_at = $budgetArchivados->updated_at;

         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budgetArchivados->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$PresupuestoArchivados->cliente = $cliente->nombre;}else{
                     $PresupuestoArchivados->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budgetArchivados->lugarEvento = 'MISMA'){
                    $PresupuestoArchivados->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $PresupuestoArchivados->lugarEvento = $budgetArchivados->lugarEvento;
                }
                
        }
        }

         array_push($PresupuestosArchivados,$PresupuestoArchivados);
        }

        //dd(count($Presupuestos));
        return view('presupuestos',compact('Presupuestos', 'PresupuestosArchivados', 'presupuestosHistorial'));   
    }

    public function presupuestos2(){
        $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->where('archivado', '0')->where('categoriaEvento', '!=', 'nube')->get();

        $fechaHoy = Carbon::yesterday();
        $presupuestosHistorial = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('archivado', 0)->whereDate('fechaEvento', '<=', $fechaHoy)->get();
        $Presupuestos=[];
      
        //Obtenemos clientes morales y fisicos
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.nombre as apellidoPaterno', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
        ->get();

        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        foreach($budgets as $budget){
            if($budget->fechaEvento >= $fechaHoy || $budget->fechaEvento == null){
                $Presupuesto   = new stdClass();
                $Presupuesto->id = $budget->id;
                $Presupuesto->folio = $budget->folio;
                $Presupuesto->fechaEvento = $budget->fechaEvento;
                //$Presupuesto->vendedor = $budget->vendedor_id;
                $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
                $Presupuesto->vendedor = $DatosVendedor->name;
                $Presupuesto->version = $budget->version;
                $Presupuesto->impresion = $budget->impresion;
                $Presupuesto->enviado = $budget->enviado;
                $Presupuesto->facturaSolicitada = $budget->facturaSolicitada;
                $Presupuesto->pagado = $budget->pagado;
                $Presupuesto->pendienteFecha = $budget->pendienteFecha;
                if($budget->opcionIVA==1){
                    $Presupuesto->total = ($budget->total)+($budget->total*.16);
                    $Presupuesto->IVA = true;
                }else{
                    $Presupuesto->total = $budget->total;
                    $Presupuesto->IVA = false;
                }
                $Presupuesto->impresionBodega = $budget->impresionBodega;
                $Presupuesto->updated_at = $budget->updated_at;
            
         
         

         foreach($clientes as $cliente){

             
       
             if($cliente->id==$budget->client_id){
                $Presupuesto->email = $cliente->email;

                    if($cliente->apellidoPaterno==$cliente->nombre){$Presupuesto->cliente = $cliente->nombre;}else{
                     $Presupuesto->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $Presupuesto->lugarEvento = $budget->lugarEvento;
                }
                
        }
        }

         array_push($Presupuestos,$Presupuesto);
        }
        }


        //Obtenemos los archivados
        $budgetsArchivados = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->where('archivado', '1')->get();
        $PresupuestosArchivados=[];
      
        //No obtenemos clientes por que ya los tenemos arriba
        foreach($budgetsArchivados as $budgetArchivados){
         $PresupuestoArchivados   = new stdClass();
         $PresupuestoArchivados->id = $budgetArchivados->id;
         $PresupuestoArchivados->folio = $budgetArchivados->folio;
         $PresupuestoArchivados->fechaEvento = $budgetArchivados->fechaEvento;
         //$Presupuesto->vendedor = $budget->vendedor_id;
         $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
         $PresupuestoArchivados->vendedor = $DatosVendedor->name;
         $PresupuestoArchivados->version = $budgetArchivados->version;
         $PresupuestoArchivados->impresion = $budgetArchivados->impresion;
         $PresupuestoArchivados->enviado = $budgetArchivados->enviado;
         $PresupuestoArchivados->total = $budgetArchivados->total;
         $PresupuestoArchivados->pendienteFecha = $budgetArchivados->pendienteFecha;
         if($budgetArchivados->opcionIVA==1){
            $PresupuestoArchivados->total = ($budgetArchivados->total)+($budgetArchivados->total*.16);
            $PresupuestoArchivados->IVA = true;
        }else{
            $PresupuestoArchivados->total = $budget->total;
            $PresupuestoArchivados->IVA = false;
        }
         $PresupuestoArchivados->impresionBodega = $budgetArchivados->impresionBodega;
         $PresupuestoArchivados->updated_at = $budgetArchivados->updated_at;

         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budgetArchivados->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$PresupuestoArchivados->cliente = $cliente->nombre;}else{
                     $PresupuestoArchivados->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $PresupuestoArchivados->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $PresupuestoArchivados->lugarEvento = $budgetArchivados->lugarEvento;
                }
                
        }
        }

         array_push($PresupuestosArchivados,$PresupuestoArchivados);
        }

        //dd($clientes);
        return view('presupuestos2',compact('Presupuestos', 'PresupuestosArchivados', 'presupuestosHistorial'));
    }

    public function presupuestosNube(){
        $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->where('archivado', '0')->where('categoriaEvento', 'nube')->get();

        $fechaHoy = Carbon::yesterday();
        $presupuestosHistorial = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('archivado', 0)->whereDate('fechaEvento', '<=', $fechaHoy)->get();
        $Presupuestos=[];
      
        //Obtenemos clientes morales y fisicos
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.nombre as apellidoPaterno', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
        ->get();

        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        foreach($budgets as $budget){
            if($budget->fechaEvento >= $fechaHoy || $budget->fechaEvento == null){
                $Presupuesto   = new stdClass();
                $Presupuesto->id = $budget->id;
                $Presupuesto->folio = $budget->folio;
                $Presupuesto->fechaEvento = $budget->fechaEvento;
                //$Presupuesto->vendedor = $budget->vendedor_id;
                $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
                $Presupuesto->vendedor = $DatosVendedor->name;
                $Presupuesto->version = $budget->version;
                $Presupuesto->impresion = $budget->impresion;
                $Presupuesto->enviado = $budget->enviado;
                $Presupuesto->facturaSolicitada = $budget->facturaSolicitada;
                $Presupuesto->pagado = $budget->pagado;
                $Presupuesto->pendienteFecha = $budget->pendienteFecha;
                if($budget->opcionIVA==1){
                    $Presupuesto->total = ($budget->total)+($budget->total*.16);
                    $Presupuesto->IVA = true;
                }else{
                    $Presupuesto->total = $budget->total;
                    $Presupuesto->IVA = false;
                }
                $Presupuesto->impresionBodega = $budget->impresionBodega;
                $Presupuesto->updated_at = $budget->updated_at;
            
         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budget->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$Presupuesto->cliente = $cliente->nombre;}else{
                     $Presupuesto->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $Presupuesto->lugarEvento = $budget->lugarEvento;
                }
                
        }
        }

         array_push($Presupuestos,$Presupuesto);
        }
        }


       

        //dd($clientes);
        return view('presupuestosNube',compact('Presupuestos'));
    }


    

    public function facturas(){
        $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->where('facturaSolicitada', '>','0')->where('archivado', '0')->get();

        $fechaHoy = Carbon::yesterday();
        $presupuestosHistorial = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('archivado', 0)->whereDate('fechaEvento', '<', $fechaHoy)->get();
        $Presupuestos=[];
      
        //Obtenemos clientes morales y fisicos
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.nombre as apellidoPaterno', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
        ->get();

        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        foreach($budgets as $budget){
            
                $Presupuesto   = new stdClass();
                $Presupuesto->id = $budget->id;
                $Presupuesto->folio = $budget->folio;
                $Presupuesto->fechaEvento = $budget->fechaEvento;
                //$Presupuesto->vendedor = $budget->vendedor_id;
                $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
                $Presupuesto->vendedor = $DatosVendedor->name;
                $Presupuesto->version = $budget->version;
                $Presupuesto->impresion = $budget->impresion;
                $Presupuesto->enviado = $budget->enviado;
                $Presupuesto->fechaEnvioFactura = $budget->fechaEnvioFactura;
                $Presupuesto->facturaSolicitada = $budget->facturaSolicitada;
                $Presupuesto->pagado = $budget->pagado;
                if($budget->opcionIVA==1){
                    $Presupuesto->total = ($budget->total)+($budget->total*.16);
                    $Presupuesto->IVA = true;
                }else{
                    $Presupuesto->total = $budget->total;
                    $Presupuesto->IVA = false;
                }
                $Presupuesto->impresionBodega = $budget->impresionBodega;
                $Presupuesto->updated_at = $budget->updated_at;
       
         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budget->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$Presupuesto->cliente = $cliente->nombre;}else{
                     $Presupuesto->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $Presupuesto->lugarEvento = $budget->lugarEvento;
                }
                
        }
        }

         array_push($Presupuestos,$Presupuesto);
        
        }


        //Obtenemos los archivados
        $budgetsArchivados = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->where('archivado', '1')->get();
        $PresupuestosArchivados=[];
      
        //No obtenemos clientes por que ya los tenemos arriba
        foreach($budgetsArchivados as $budgetArchivados){
         $PresupuestoArchivados   = new stdClass();
         $PresupuestoArchivados->id = $budgetArchivados->id;
         $PresupuestoArchivados->folio = $budgetArchivados->folio;
         $PresupuestoArchivados->fechaEvento = $budgetArchivados->fechaEvento;
         //$Presupuesto->vendedor = $budget->vendedor_id;
         $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
         $PresupuestoArchivados->vendedor = $DatosVendedor->name;
         $PresupuestoArchivados->version = $budgetArchivados->version;
         $PresupuestoArchivados->impresion = $budgetArchivados->impresion;
         $PresupuestoArchivados->enviado = $budgetArchivados->enviado;
         $PresupuestoArchivados->total = $budgetArchivados->total;
         $PresupuestoArchivados->pagado = $budgetArchivados->pagado;
         $PresupuestoArchivados->facturaSolicitada = $budgetArchivados->facturaSolicitada;
         $PresupuestoArchivados->pendienteFecha = $budgetArchivados->pendienteFecha;
         if($budgetArchivados->opcionIVA==1){
            $PresupuestoArchivados->total = ($budgetArchivados->total)+($budgetArchivados->total*.16);
            $PresupuestoArchivados->IVA = true;
        }else{
            $PresupuestoArchivados->total = $budget->total;
            $PresupuestoArchivados->IVA = false;
        }
         $PresupuestoArchivados->impresionBodega = $budgetArchivados->impresionBodega;
         $PresupuestoArchivados->updated_at = $budgetArchivados->updated_at;

         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budgetArchivados->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$PresupuestoArchivados->cliente = $cliente->nombre;}else{
                     $PresupuestoArchivados->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $PresupuestoArchivados->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $PresupuestoArchivados->lugarEvento = $budgetArchivados->lugarEvento;
                }
                
        }
        }

         array_push($PresupuestosArchivados,$PresupuestoArchivados);
        }

        //dd($clientes);
        return view('facturas',compact('Presupuestos', 'PresupuestosArchivados', 'presupuestosHistorial'));
    }
    

    public function presupuestosHoy(){
        $budgets = Budget::orderBy('id', 'ASC')->where('tipo', 'CONTRATO')->where('archivado', '0')->get();

        $fechaHoy = Carbon::yesterday();
        $presupuestosHistorial = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('archivado', 0)->whereDate('fechaEvento', '=', $fechaHoy)->get();
        $Presupuestos=[];
      
        //Obtenemos clientes morales y fisicos
        $clientes_morales = DB::table('clients')
        ->join('moral_people', 'moral_people.client_id', '=', 'clients.id')
        ->select('clients.id', 'moral_people.nombre', 'moral_people.nombre as apellidoPaterno', 'moral_people.emailFacturacion as email', 'moral_people.nombreFacturacion','moral_people.direccionFacturacion', 'moral_people.coloniaFacturacion', 'moral_people.numeroFacturacion')
        ->get();

        $clientes_fisicos = DB::table('clients')
        ->join('physical_people', 'physical_people.client_id', '=', 'clients.id')
        ->select( 'clients.id', 'physical_people.nombre', 'physical_people.apellidoPaterno', 'physical_people.email', 'physical_people.nombreFacturacion', 'physical_people.direccionFacturacion', 'physical_people.coloniaFacturacion', 'physical_people.numeroFacturacion')
        ->get();
        
        $clientes = $clientes_morales->merge($clientes_fisicos);

        foreach($budgets as $budget){
            if($budget->fechaEvento >= $fechaHoy || $budget->fechaEvento == null){
                $Presupuesto   = new stdClass();
                $Presupuesto->id = $budget->id;
                $Presupuesto->folio = $budget->folio;
                $Presupuesto->fechaEvento = $budget->fechaEvento;
                //$Presupuesto->vendedor = $budget->vendedor_id;
                $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
                $Presupuesto->vendedor = $DatosVendedor->name;
                $Presupuesto->version = $budget->version;
                $Presupuesto->impresion = $budget->impresion;
                $Presupuesto->enviado = $budget->enviado;
                if($budget->opcionIVA==1){
                    $Presupuesto->total = ($budget->total)+($budget->total*.16);
                }else{
                    $Presupuesto->total = $budget->total;
                }
                $Presupuesto->impresionBodega = $budget->impresionBodega;
                $Presupuesto->updated_at = $budget->updated_at;
            
         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budget->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$Presupuesto->cliente = $cliente->nombre;}else{
                     $Presupuesto->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $Presupuesto->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $Presupuesto->lugarEvento = $budget->lugarEvento;
                }
                
        }
        }

         array_push($Presupuestos,$Presupuesto);
        }
        }


        //Obtenemos los archivados
        $budgetsArchivados = Budget::orderBy('id', 'ASC')->where('tipo', 'PRESUPUESTO')->where('archivado', '1')->get();
        $PresupuestosArchivados=[];
      
        //No obtenemos clientes por que ya los tenemos arriba
        foreach($budgetsArchivados as $budgetArchivados){
         $PresupuestoArchivados   = new stdClass();
         $PresupuestoArchivados->id = $budgetArchivados->id;
         $PresupuestoArchivados->folio = $budgetArchivados->folio;
         $PresupuestoArchivados->fechaEvento = $budgetArchivados->fechaEvento;
         //$Presupuesto->vendedor = $budget->vendedor_id;
         $DatosVendedor = User::orderBy('id', 'DESC')->where('id', $budget->vendedor_id)->first();
         $PresupuestoArchivados->vendedor = $DatosVendedor->name;
         $PresupuestoArchivados->version = $budgetArchivados->version;
         $PresupuestoArchivados->impresion = $budgetArchivados->impresion;
         $PresupuestoArchivados->enviado = $budgetArchivados->enviado;
         $PresupuestoArchivados->total = $budgetArchivados->total;
         $PresupuestoArchivados->pendienteFecha = $budgetArchivados->pendienteFecha;
         $PresupuestoArchivados->impresionBodega = $budgetArchivados->impresionBodega;
         $PresupuestoArchivados->updated_at = $budgetArchivados->updated_at;

         
         

         foreach($clientes as $cliente){
       
             if($cliente->id==$budgetArchivados->client_id){
                    if($cliente->apellidoPaterno==$cliente->nombre){$PresupuestoArchivados->cliente = $cliente->nombre;}else{
                     $PresupuestoArchivados->cliente = $cliente->nombre.' '.$cliente->apellidoPaterno;}

                if($budget->lugarEvento = 'MISMA'){
                    $PresupuestoArchivados->lugarEvento = $cliente->direccionFacturacion; 
                    
                }else{
                    $PresupuestoArchivados->lugarEvento = $budgetArchivados->lugarEvento;
                }
                
        }
        }

         array_push($PresupuestosArchivados,$PresupuestoArchivados);
        }

        //dd($clientes);
        return view('presupuestos-hoy',compact('Presupuestos', 'PresupuestosArchivados', 'presupuestosHistorial')); 
    }

   

    public function editarPresupuesto($id){
        $presupuesto = Budget::orderBy('id', 'DESC')->where('id', $id)->first();

        return view('presupuesto', compact('presupuesto'));
    }

    public function ticketSalida($id){
        $salida = OtherPayments::orderBy('id', 'DESC')->where('id', $id)->first();

        return view('ticketSalida', compact('salida'));
    }

    //Ventas

    public function ventas(){
        $date = Carbon::now();
        $contratos = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->where('archivado', '!=', 'TRUE')->whereYear('fechaEvento', $date)->whereMonth('fechaEvento', $date)->get();
        $vendedores = User::orderBy('id', 'DESC')->where('archivado', '0')->where('tipo', 'ADMINISTRADOR')->orWhere('tipo', 'VENTAS')->get();
       
        return view('ventas', compact('contratos', 'vendedores'));
    }

    public function ventasFiltro(Request $request){
        $vendedores = User::orderBy('id', 'DESC')->where('archivado', '0')->where('tipo', 'ADMINISTRADOR')->orWhere('tipo', 'VENTAS')->get();
        $fecha = strtotime($request->fecha);
        $vendedor = $request->vendedor;
        $mes = date("n", $fecha);
        $ano = date("Y", $fecha);

        if($request->vendedor==0){
        $contratos = Budget::orderBy('id', 'DESC')->where('archivado', '!=', 'TRUE')->whereYear('fechaEvento', $ano)->whereMonth('fechaEvento', $mes)->where('tipo', 'CONTRATO')->get();
        }else{
        $contratos = Budget::orderBy('id', 'DESC')->where('archivado', '!=', 'TRUE')->whereYear('fechaEvento', $ano)->whereMonth('fechaEvento', $mes)->where('tipo', 'CONTRATO')->where('vendedor_id', $vendedor)->get();  
        }
        return view('ventas', compact('contratos', 'vendedores'));
    }

    public function ventasPDF(){
        $contratos = Budget::orderBy('id', 'DESC')->where('tipo', 'CONTRATO')->get();
        
        $pdf = App::make('dompdf');

        $pdf = PDF::loadView('pdf.reporteVentas', compact('contratos'));

        return $pdf->stream();
    }

    public function pdfCreditosAtrasados(){
        $date = Carbon::now();
        $fechaActual = $date->format('Y-m-d');
        $contratos = [];

         //calculo adeudo total
         $adeudoTotal = 0;
         $contratosAdeudo = Budget::orderBy('id', 'DESC')->where('pagado', '!=', true)->where('archivado', 'FALSE')->where('tipo', 'CONTRATO')->where('fechaEvento', '!=', null)->get();
         foreach($contratosAdeudo as $contratoAdeudo){
             
             $PagosContratoAdeudo = Payment::orderBy('id', 'DESC')->where('budget_id', $contratoAdeudo->id)->get();
             if(count($PagosContratoAdeudo)>0){
                 $sumaPagos = 0;
                 foreach($PagosContratoAdeudo as $PagoContratoAdeudo){
                     if($PagoContratoAdeudo->method=='DOLAR'){
                     $sumaPagos=$sumaPagos+($PagoContratoAdeudo->amount*$PagoContratoAdeudo->reference);}
                     else{
                        $sumaPagos=$sumaPagos+$PagoContratoAdeudo->amount;
                     }
                 }
                 if($contratoAdeudo->opcionIVA){
                 $adeudoTotal=$adeudoTotal+(($contratoAdeudo->total*1.16)-$sumaPagos);
                 }else{
                 $adeudoTotal=$adeudoTotal+($contratoAdeudo->total-$sumaPagos);}
             }else{
                 $adeudoTotal=$adeudoTotal+$contratoAdeudo->total;
             }
         }
          //Fincalculo adeudo total

        $creditos = Budget::orderBy('id', 'DESC')->where('pagado', null)->where('tipo', 'CONTRATO')->where('archivado', 'FALSE')->where('fechaEvento', '!=', null)->get();
        foreach ($creditos as $credito) {
            if(!is_null($credito->fechaEvento)){

            $cliente = Client::findOrFail($credito->client_id);
            if($cliente->tipoPersona == 'FISICA'){
                $persona = PhysicalPerson::where('client_id', $cliente->id)->first();
                $vendedor = User::where('id', $credito->vendedor_id)->first();
                $fechaEvento = strtotime($credito->fechaEvento . '+' . $persona->diasCredito . '  days');
                $fechaFormato = date('Y-m-d',$fechaEvento);
                $pagos = Payment::where('budget_id', $credito->id)->get();
                
                $saldoPendiente = 0;
                foreach($pagos as $pago){
                    if($pago->method=='DOLAR'){
                        $saldoPendiente = $saldoPendiente + ($pago->amount*$pago->reference);}
                        else{
                            $saldoPendiente = $saldoPendiente + $pago->amount;
                        }
                }

                if($fechaFormato < $fechaActual){

                    $contrato = new stdClass();
                    $contrato->id = $credito->id;
                    $contrato->fechaLimite = $fechaFormato;
                    $contrato->diasCredito = $persona->diasCredito;
                    $contrato->fechaEvento = $credito->fechaEvento;
                    $contrato->folio = $credito->folio;
                    $contrato->cliente = $persona->nombre.' '.$persona->apellidoPaterno.' '.$persona->apellidoMaterno;
                    $contrato->vendedor = $vendedor->name;
                    $contrato->total = $credito->total;
                    $contrato->pagado = $credito->pagado;
                    $contrato->pendienteFecha = $credito->pendienteFecha;
                    $contrato->client_id = $credito->client_id;
                    $contrato->user = $credito->user;
                    $contrato->version = $credito->version;
                    $contrato->impresion = $credito->impresion;
                    $contrato->impresionBodega = $credito->impresionBodega;
                    $contrato->enviado = $credito->enviado;
                    $contrato->updated_at = $credito->updated_at;
                    $contrato->opcionIVA = $credito->opcionIVA;
                    $contrato->saldoPendiente = $saldoPendiente;
           
                    array_push($contratos, $contrato);
                }
                
            }else{
                $persona = MoralPerson::where('client_id', $cliente->id)->first();
                $fechaEvento = strtotime($credito->fechaEvento . '+' . $persona->diasCredito . '  days');
                $fechaFormato = date('Y-m-d',$fechaEvento);
                $vendedor = User::where('id', $credito->vendedor_id)->first();
                $pagos = Payment::where('budget_id', $credito->id)->get();

                $saldoPendiente = 0;
              
                foreach($pagos as $pago){
                    if($pago->method=='DOLAR'){
                        $saldoPendiente = $saldoPendiente + ($pago->amount*$pago->reference);}
                        else{
                            $saldoPendiente = $saldoPendiente + $pago->amount;
                        }
                }
                
                if($fechaFormato < $fechaActual){
                    $contrato = new stdClass();
                    $contrato->id = $credito->id;
                    $contrato->fechaLimite = $fechaFormato;
                    $contrato->diasCredito = $persona->diasCredito;
                    $contrato->fechaEvento = $credito->fechaEvento;
                    $contrato->folio = $credito->folio;
                    $contrato->cliente = $persona->nombre.' '.$persona->apellidoPaterno.' '.$persona->apellidoMaterno;
                    $contrato->vendedor = $vendedor->name;
                    $contrato->total = $credito->total;
                    $contrato->pagado = $credito->pagado;
                    $contrato->pendienteFecha = $credito->pendienteFecha;
                    $contrato->client_id = $credito->client_id;
                    $contrato->user = $credito->user;
                    $contrato->version = $credito->version;
                    $contrato->impresion = $credito->impresion;
                    $contrato->impresionBodega = $credito->impresionBodega;
                    $contrato->updated_at = $credito->updated_at;
                    $contrato->enviado = $credito->enviado;
                    $contrato->opcionIVA = $credito->opcionIVA;
                    $contrato->saldoPendiente = $saldoPendiente;
                  
                    array_push($contratos, $contrato);
                }
                
            }
        }
    }
        
        $pdf = App::make('dompdf');

        $pdf = PDF::loadView('pdf.creditosPendientes', compact('contratos', 'adeudoTotal'))->setPaper('a4', 'landscape');

        return $pdf->stream();
    }

    public function ventasShow($id){
        $cliente = Client::where('id', $id)->first();

        if($cliente->tipoPersona == 'FISICA'){
            $persona = PhysicalPerson::where('client_id', $cliente->id)->first();
        }else{
            $persona = MoralPerson::where('client_id', $cliente->id)->first();
        }

        $presupuestos = Budget::orderBy('id', 'DESC')->where('client_id', $cliente->id)->get();

        return view('ventasShow', compact('presupuestos', 'persona'));
    }

    public function historialCortes(){
        $cortes = CashRegister::orderBy('id', 'DESC')->where('estatus', false)->get();
        return view('cortesCaja', compact('cortes'));
    }

    public function danados(){
        $productos = Missing::orderBy('id', 'DESC')->where('danados', '>', 0)->orwhere('faltante', '>', 0)->get();
        
        return view('danados', compact('productos'));
    }

    public function hacerInventario(){
        
        $Inventario = Inventory::orderBy('id', 'DESC')->where('archivar', false)->orWhere('archivar', null)->get();
        return view('hacerInventario', compact('Inventario'));
    }

    public function aprobarDanados(){
        $productos = Missing::orderBy('id', 'DESC')->where('reportado', true)->where('aprobado', false)->get();
        return view('aprobarDanados', compact('productos'));
    }

    public function paquetes(){
        $paquetes = BudgetPack::orderBy('id', 'DESC')->where('guardarPaquete', true)->get();
        $paquetesAuth = AuthorizedPack::orderBy('id', 'DESC')->get();
        return view('paquetes', compact('paquetes', 'paquetesAuth'));
    }

    public function creditosAtrasados2(){
        $date = Carbon::now();
        $fechaActual = $date->format('Y-m-d');
        $contratos = [];

         //calculo adeudo total
         $adeudoTotal = 0;
         $contratosAdeudo = Budget::orderBy('id', 'DESC')->where('pagado', '!=', true)->where('archivado', 'TRUE')->where('tipo', 'CONTRATO')->where('fechaEvento', '!=', null)->get();
         
          //Fincalculo adeudo total

        $creditos = Budget::orderBy('id', 'DESC')->where('pagado', null)->where('tipo', 'CONTRATO')->where('archivado', 'FALSE')->where('fechaEvento', '!=', null)->get();
        foreach ($creditos as $credito) {
            if(!is_null($credito->fechaEvento)){

            $cliente = Client::findOrFail($credito->client_id);
            if($cliente->tipoPersona == 'FISICA'){
                $persona = PhysicalPerson::where('client_id', $cliente->id)->first();
                $vendedor = User::where('id', $credito->vendedor_id)->first();
                $fechaEvento = strtotime($credito->fechaEvento . '+' . $persona->diasCredito . '  days');
                $fechaFormato = date('Y-m-d',$fechaEvento);
                $pagos = Payment::where('budget_id', $credito->id)->get();
                
                $saldoPendiente = 0;
                foreach($pagos as $pago){
                    
                    if($pago->method=='DOLAR'){
                        $saldoPendiente = $saldoPendiente + ($pago->amount*$pago->reference);}
                        else{
                            $saldoPendiente = $saldoPendiente + $pago->amount;
                        }
                    
                }
                
                if($credito->opcionIVA){
                    if((($credito->total*1.16)-$saldoPendiente)>0){
                $adeudoTotal=$adeudoTotal+(($credito->total*1.16)-$saldoPendiente);}
            }else{
                if(($credito->total-$saldoPendiente)>0){
                $adeudoTotal=$adeudoTotal+($credito->total-$saldoPendiente);}
                }

                if($fechaFormato < $fechaActual){

                    $contrato = new stdClass();
                    $contrato->id = $credito->id;
                    $contrato->fechaLimite = $fechaFormato;
                    $contrato->diasCredito = $persona->diasCredito;
                    $contrato->fechaEvento = $credito->fechaEvento;
                    $contrato->folio = $credito->folio;
                    $contrato->cliente = $persona->nombre.' '.$persona->apellidoPaterno.' '.$persona->apellidoMaterno;
                    $contrato->vendedor = $vendedor->name;
                    $contrato->total = $credito->total;
                    $contrato->pagado = $credito->pagado;
                    $contrato->pendienteFecha = $credito->pendienteFecha;
                    $contrato->client_id = $credito->client_id;
                    $contrato->user = $credito->user;
                    $contrato->version = $credito->version;
                    $contrato->impresion = $credito->impresion;
                    $contrato->impresionBodega = $credito->impresionBodega;
                    $contrato->enviado = $credito->enviado;
                    $contrato->updated_at = $credito->updated_at;
                    $contrato->opcionIVA = $credito->opcionIVA;
                    $contrato->saldoPendiente = $saldoPendiente;
           
                    array_push($contratos, $contrato);
                }
                
            }else{
                $persona = MoralPerson::where('client_id', $cliente->id)->first();
                $fechaEvento = strtotime($credito->fechaEvento . '+' . $persona->diasCredito . '  days');
                $fechaFormato = date('Y-m-d',$fechaEvento);
                $vendedor = User::where('id', $credito->vendedor_id)->first();
                $pagos = Payment::where('budget_id', $credito->id)->get();

                $saldoPendiente = 0;
                foreach($pagos as $pago){
                    if($pago->method=='DOLAR'){
                        $saldoPendiente = $saldoPendiente + ($pago->amount*$pago->reference);}
                        else{
                            $saldoPendiente = $saldoPendiente + $pago->amount;
                        }
                }
                if($fechaFormato < $fechaActual){
                    $contrato = new stdClass();
                    $contrato->id = $credito->id;
                    $contrato->fechaLimite = $fechaFormato;
                    $contrato->diasCredito = $persona->diasCredito;
                    $contrato->fechaEvento = $credito->fechaEvento;
                    $contrato->folio = $credito->folio;
                    $contrato->cliente = $persona->nombre.' '.$persona->apellidoPaterno.' '.$persona->apellidoMaterno;
                    $contrato->vendedor = $vendedor->name;
                    $contrato->total = $credito->total;
                    $contrato->pagado = $credito->pagado;
                    $contrato->pendienteFecha = $credito->pendienteFecha;
                    $contrato->client_id = $credito->client_id;
                    $contrato->user = $credito->user;
                    $contrato->version = $credito->version;
                    $contrato->impresion = $credito->impresion;
                    $contrato->impresionBodega = $credito->impresionBodega;
                    $contrato->updated_at = $credito->updated_at;
                    $contrato->enviado = $credito->enviado;
                    $contrato->opcionIVA = $credito->opcionIVA;
                    $contrato->saldoPendiente = $saldoPendiente;
                  
                    array_push($contratos, $contrato);
                }
                
            }
        }
    }

    $adeudoTotal=$adeudoTotal-25855;

        //dd($contratos);
        return view('creditosAtrasadoscancel', compact('contratos', 'adeudoTotal'));
}
public function creditosAtrasados(){
    $date = Carbon::now();
    $fechaActual = $date->format('Y-m-d');
    $contratos = [];

     //calculo adeudo total
     $adeudoTotal = 0;
     $contratosAdeudo = Budget::orderBy('id', 'DESC')->where('pagado', '!=', true)->where('archivado', 'FALSE')->where('tipo', 'CONTRATO')->where('fechaEvento', '!=', null)->get();
     
      //Fincalculo adeudo total

    $creditos = Budget::orderBy('id', 'DESC')->where('pagado', null)->where('tipo', 'CONTRATO')->where('archivado', 'FALSE')->where('fechaEvento', '!=', null)->get();
    foreach ($creditos as $credito) {
        if(!is_null($credito->fechaEvento)){

        $cliente = Client::findOrFail($credito->client_id);
        if($cliente->tipoPersona == 'FISICA'){
            $persona = PhysicalPerson::where('client_id', $cliente->id)->first();
            $vendedor = User::where('id', $credito->vendedor_id)->first();
            $fechaEvento = strtotime($credito->fechaEvento . '+' . $persona->diasCredito . '  days');
            $fechaFormato = date('Y-m-d',$fechaEvento);
            $pagos = Payment::where('budget_id', $credito->id)->get();
            
            $saldoPendiente = 0;
            foreach($pagos as $pago){
                
                if($pago->method=='DOLAR'){
                    $saldoPendiente = $saldoPendiente + ($pago->amount*$pago->reference);}
                    else{
                        $saldoPendiente = $saldoPendiente + $pago->amount;
                    }
                
            }
            
            if($credito->opcionIVA){
                if((($credito->total*1.16)-$saldoPendiente)>0){
            $adeudoTotal=$adeudoTotal+(($credito->total*1.16)-$saldoPendiente);}
        }else{
            if(($credito->total-$saldoPendiente)>0){
            $adeudoTotal=$adeudoTotal+($credito->total-$saldoPendiente);}
            }

            if($fechaFormato < $fechaActual){

                $contrato = new stdClass();
                $contrato->id = $credito->id;
                $contrato->fechaLimite = $fechaFormato;
                $contrato->diasCredito = $persona->diasCredito;
                $contrato->fechaEvento = $credito->fechaEvento;
                $contrato->folio = $credito->folio;
                $contrato->cliente = $persona->nombre.' '.$persona->apellidoPaterno.' '.$persona->apellidoMaterno;
                $contrato->vendedor = $vendedor->name;
                $contrato->total = $credito->total;
                $contrato->pagado = $credito->pagado;
                $contrato->pendienteFecha = $credito->pendienteFecha;
                $contrato->client_id = $credito->client_id;
                $contrato->user = $credito->user;
                $contrato->version = $credito->version;
                $contrato->impresion = $credito->impresion;
                $contrato->impresionBodega = $credito->impresionBodega;
                $contrato->enviado = $credito->enviado;
                $contrato->updated_at = $credito->updated_at;
                $contrato->opcionIVA = $credito->opcionIVA;
                $contrato->saldoPendiente = $saldoPendiente;
       
                array_push($contratos, $contrato);
            }
            
        }else{
            $persona = MoralPerson::where('client_id', $cliente->id)->first();
            $fechaEvento = strtotime($credito->fechaEvento . '+' . $persona->diasCredito . '  days');
            $fechaFormato = date('Y-m-d',$fechaEvento);
            $vendedor = User::where('id', $credito->vendedor_id)->first();
            $pagos = Payment::where('budget_id', $credito->id)->get();

            $saldoPendiente = 0;
            foreach($pagos as $pago){
                if($pago->method=='DOLAR'){
                    $saldoPendiente = $saldoPendiente + ($pago->amount*$pago->reference);}
                    else{
                        $saldoPendiente = $saldoPendiente + $pago->amount;
                    }
            }
            if($fechaFormato < $fechaActual){
                $contrato = new stdClass();
                $contrato->id = $credito->id;
                $contrato->fechaLimite = $fechaFormato;
                $contrato->diasCredito = $persona->diasCredito;
                $contrato->fechaEvento = $credito->fechaEvento;
                $contrato->folio = $credito->folio;
                $contrato->cliente = $persona->nombre.' '.$persona->apellidoPaterno.' '.$persona->apellidoMaterno;
                $contrato->vendedor = $vendedor->name;
                $contrato->total = $credito->total;
                $contrato->pagado = $credito->pagado;
                $contrato->pendienteFecha = $credito->pendienteFecha;
                $contrato->client_id = $credito->client_id;
                $contrato->user = $credito->user;
                $contrato->version = $credito->version;
                $contrato->impresion = $credito->impresion;
                $contrato->impresionBodega = $credito->impresionBodega;
                $contrato->updated_at = $credito->updated_at;
                $contrato->enviado = $credito->enviado;
                $contrato->opcionIVA = $credito->opcionIVA;
                $contrato->saldoPendiente = $saldoPendiente;
              
                array_push($contratos, $contrato);
            }
            
        }
    }
}

$adeudoTotal=$adeudoTotal-25855;

    //dd($contratos);
    return view('creditosAtrasados', compact('contratos', 'adeudoTotal'));
}

    public function proveedores(){
        $proveedores = Supplier::orderBy('id', 'DESC')->where('tipo', 'NORMAL')->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function agregarProveedor(Request $request){
        $proveedor =  new Supplier();
        $proveedor->nombre = $request->proveedor['nombre'];
        $proveedor->direccion = $request->proveedor['direccion'];
        $proveedor->descripcion = $request->proveedor['descripcion'];
        $proveedor->telefonoGeneral = $request->proveedor['telefonoGeneral'];
        $proveedor->tipo = $request->proveedor['tipo'];
        $proveedor->save();

        $proveedor = Supplier::orderBy('id', 'DESC')->first();

        foreach($request->telefonos as $numero){

            $telefono = new SupplierTelephone();
            $telefono->proveedor_id = $proveedor->id;
            $telefono->nombre = $numero['nombre'];
            $telefono->numero = $numero['telefono'];
            $telefono->correo = $numero['correo'];
            $telefono->tipo = $numero['tipo'];
            $telefono->ext = $numero['ext'];
            $telefono->save();
        }
        return;
    }

    public function editarProveedor($id){
        $proveedor = Supplier::with('telefonos')->findOrFail($id);
        
        return view('proveedores.edit', compact('proveedor'));
    }

    public function actualizarProveedor(Request $request, $id){
        //dd($request->proveedor['telefonos']);
        $proveedor = Supplier::findOrFail($id);
        $proveedor->nombre = $request->proveedor['nombre'];
        $proveedor->direccion = $request->proveedor['direccion'];
        $proveedor->descripcion = $request->proveedor['descripcion'];
        $proveedor->tipo = $request->proveedor['tipo'];
        $proveedor->publico = $request->proveedor['publico'];
        $proveedor->save();
        
        DB::table('supplier_telephones')->where('proveedor_id', $id)->delete();

        foreach($request->proveedor['telefonos'] as $numero){

            $telefono = new SupplierTelephone();
            $telefono->proveedor_id = $proveedor->id;
            $telefono->nombre = $numero['nombre'];
            $telefono->numero = $numero['numero'];
            $telefono->correo = $numero['correo'];
            $telefono->tipo = $numero['tipo'];
            $telefono->ext = $numero['ext'];
            $telefono->save();
        }

        return;
    }

    public function borrarProveedor($id){
        $proveedor = Supplier::findOrFail($id);
        $proveedor->delete();

        return back()->with('info', 'Proveedor eliminado con exito');
    }


    public function paginaweb(){

        $galerias=Gallery::all();
        return view('paginaweb.admin', compact('galerias'));

    }

    public function createGallery()
    {
                
        return view('paginaweb.create');
    }

    public function editarGaleria($id)
    {
        $galeria = Gallery::findOrFail($id);

        return view('paginaweb.edit', compact('galeria'));
    }

    public function imagesGaleria($id)
    {
        $galeria = Gallery::findOrFail($id);
        $imagenes = Photo::where('gallery_id', $id)->get();

        return view('paginaweb.imagenes', compact('imagenes', 'galeria'));
    }

    public function uploadPhotos($id, Request $request)
    {
        
        
        $archivo = $request->file('file');
            $md5Name = md5_file($archivo->getRealPath());
            $guessExtension = $archivo->guessExtension();
            $path = $archivo->storeAs('mmDecor', $md5Name.'.'.$guessExtension  ,'s3');

            $url = 'https://mm-decor.s3.us-east-2.amazonaws.com/';

            //$photo->fill(['imagen' => asset($url.$path)])->save();
      
        
        $Photo = new Photo();
        $Photo->gallery_id = $id;
        $Photo->imagen = $url.$path;
        $Photo->save();
    }


    public function storeGallery(Request $request)
    {
        //dd($request->all());
         //Comprobamos que el slug no se repita pero ignoramos el slug propio
         $v = \Validator::make($request->all(), [
            'name' => 'required',
            'imagen' => 'required',
        ]);
            
        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }
        
        $gallery = Gallery::create($request->all());

        // Store in AWS S3
        if($archivo = $request->file('imagen')){

            $md5Name = md5_file($archivo->getRealPath());
            $guessExtension = $archivo->guessExtension();
            $path = $archivo->storeAs('mmDecor', $md5Name.'.'.$guessExtension  ,'s3');

            $url = 'https://mm-decor.s3.us-east-2.amazonaws.com/';

            $gallery->fill(['imagen' => asset($url.$path)])->save();
        }

        $gallery = Gallery::orderBy('id', 'DESC')->first();

        return redirect()->route('gallery.create')
            ->with('info', 'Galeria creada con exito');

    }

    public function updateGaleria(Request $request, $id)
    {
        //Comprobamos que el slug no se repita pero ignoramos el slug propio
        $v = \Validator::make($request->all(), [
            'name' => 'required',
            'imagen' => 'required',
        ]);
 
        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        $gallery = Gallery::find($id);
        $gallery->fill($request->all())->save();


        // Store in AWS S3
        if($archivo = $request->file('imagen')){

            $md5Name = md5_file($archivo->getRealPath());
            $guessExtension = $archivo->guessExtension();
            $path = $archivo->storeAs('mmDecor', $md5Name.'.'.$guessExtension  ,'s3');

            $url = 'https://mm-decor.s3.us-east-2.amazonaws.com/';

            $gallery->fill(['imagen' => asset($url.$path)])->save();
        }

        return redirect()->route('gallery.edit', $gallery->id)
            ->with('info', 'Galeria actualizada con exito');

    }
    public function borrarPhoto($id){
        $photo = Photo::findOrFail($id);
        $photo->delete();

        return back()->with('info', 'Foto eliminado con exito');
    }

    public function landing(){
        $testimonios = Testimonials::all();
        $galerias = Gallery::all();
        return view('landing', compact('galerias', 'testimonios'));
    }
    public function gallery($id){
        $gallery = Gallery::where('id',$id)->first();
        $imagenes = Photo::where('gallery_id', $id)->get();
        return view('gallery', compact('imagenes', 'gallery'));
    }

    //Testimonios
    public function testimonios(){

        $testimonios=Testimonials::all();
        return view('paginaweb.admintestimonios', compact('testimonios'));
    }
    public function createTestimonial()
    {      
        return view('paginaweb.createtestimonials');
    }

    public function editarTestimonio($id)
    {
        $testimonios = Testimonials::findOrFail($id);
        return view('paginaweb.edittestimonials', compact('testimonios'));
    }
    public function storeTestimonial(Request $request)
    {
        //dd($request->all());
         //Comprobamos que el slug no se repita pero ignoramos el slug propio
         $v = \Validator::make($request->all(), [
            'name' => 'required',
            'imagen' => 'required',
        ]);
            
        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }
        
        $testimonial = Testimonials::create($request->all());

        // Store in AWS S3
        if($archivo = $request->file('imagen')){

            $md5Name = md5_file($archivo->getRealPath());
            $guessExtension = $archivo->guessExtension();
            $path = $archivo->storeAs('mmDecor', $md5Name.'.'.$guessExtension  ,'s3');

            $url = 'https://mm-decor.s3.us-east-2.amazonaws.com/';

            $testimonial->fill(['imagen' => asset($url.$path)])->save();
        }

        $testimonial = Testimonials::orderBy('id', 'DESC')->first();

        return redirect()->route('testimonial.create')
            ->with('info', 'Testimonio creado con exito');

    }

    public function updateTestimonio(Request $request, $id)
    {
        //Comprobamos que el slug no se repita pero ignoramos el slug propio
        $v = \Validator::make($request->all(), [
            'name' => 'required',
            'imagen' => 'required',
        ]);
 
        if ($v->fails())
        {
            return redirect()->back()->withInput()->withErrors($v->errors());
        }

        $testimonial = Testimonials::find($id);
        $testimonial->fill($request->all())->save();


        // Store in AWS S3
        if($archivo = $request->file('imagen')){

            $md5Name = md5_file($archivo->getRealPath());
            $guessExtension = $archivo->guessExtension();
            $path = $archivo->storeAs('mmDecor', $md5Name.'.'.$guessExtension  ,'s3');

            $url = 'https://mm-decor.s3.us-east-2.amazonaws.com/';

            $testimonial->fill(['imagen' => asset($url.$path)])->save();
        }

        return redirect()->route('testimonial.edit', $testimonial->id)
            ->with('info', 'Testimonio actualizado con exito');

    }
}
