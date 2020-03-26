<body style="font-family: Helvetica; ">
<p style="line-height: 15px; font-size: 16px; font-style: italic; font-weight: bold">Familia: {{$familia}}</p>
<table>


</table>
@php
use Carbon\Carbon;
    $date = Carbon::now();
        $fechaHoy = $date->format('Y-m-d');
@endphp
<p>Inventario Impreso {{$fechaHoy}}</p>
<table style="border-width: 1px; width:100%; text-align:center">
    <tr style=" font-size: 12px">
      <th style="padding:4px">Imagen</th> 
      <th style="padding: 4px;">Servicio</th>
       <th style="padding: 4px;">Antes Bodega</th>
       <th style="padding: 4px;">Fisico Bodega</th>
       <th style="padding: 4px;">Diferencia</th>
       <th style="padding: 4px;">Antes Exhibición</th>
       <th style="padding: 4px;">Fisico Exhibición</th>
       <th style="padding: 4px;">Diferencia</th>
       <th style="padding: 4px;">Total Diferencia</th>
    </tr>
    @foreach ($Inventario as $item)
    @if(!$item->noAplica) 
    @php
        $registro = App\PhysicalInventory::where('idProducto', $item->id)->first();
    @endphp

    @if($faltantes=='si')
    @if(!is_null($registro))
    @php
        $faltante=($registro->fisicoBodega - $registro->antesBodega) + ($registro->fisicoExhibicion - $registro->antesExhibicion);

    @endphp
    @if($faltante>0)
<tr style="font-size:11px;">
<td style="padding: 4px; border-bottom:solid; border-width: 1px; "><img src="{{$item->imagen}}" width="35px"></td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; ">{{$item->servicio}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center">{{$registro->antesBodega}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center">{{$registro->fisicoBodega}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center; background:#FFFEDD; @if(($registro->fisicoBodega-$registro->antesBodega)<0) color:red @endif">{{$registro->fisicoBodega-$registro->antesBodega}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center">{{$registro->antesExhibicion}}</td>
<td style="padding: 4px; border-bottom:solid; text-align: center; border-width: 1px; ">{{$registro->fisicoExhibicion}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center; background:#FFFEDD; @if(($registro->fisicoExhibicion-$registro->antesExhibicion)<0) color:red @endif">{{$registro->fisicoExhibicion-$registro->antesExhibicion}}</td>
@if ($registro->diferencia)
  <td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center; background:#FFFEDD;">{{($registro->fisicoBodega - $registro->antesBodega) + ($registro->fisicoExhibicion - $registro->antesExhibicion)}}</td>
@endif
@endif

  @endif
 @else


 @if(!is_null($registro))
    @php
        $faltante=($registro->fisicoBodega - $registro->antesBodega) + ($registro->fisicoExhibicion - $registro->antesExhibicion);

    @endphp
<tr style="font-size:11px;">
<td style="padding: 4px; border-bottom:solid; border-width: 1px; "><img src="{{$item->imagen}}" width="35px"></td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; ">{{$item->servicio}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center">{{$registro->antesBodega}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center">{{$registro->fisicoBodega}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center; background:#FFFEDD; @if(($registro->fisicoBodega-$registro->antesBodega)<0) color:red @endif">{{$registro->fisicoBodega-$registro->antesBodega}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center">{{$registro->antesExhibicion}}</td>
<td style="padding: 4px; border-bottom:solid; text-align: center; border-width: 1px; ">{{$registro->fisicoExhibicion}}</td>
<td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center; background:#FFFEDD; @if(($registro->fisicoExhibicion-$registro->antesExhibicion)<0) color:red @endif">{{$registro->fisicoExhibicion-$registro->antesExhibicion}}</td>
@if ($registro->diferencia)
  <td style="padding: 4px; border-bottom:solid; border-width: 1px; text-align: center; background:#FFFEDD;">{{($registro->fisicoBodega - $registro->antesBodega) + ($registro->fisicoExhibicion - $registro->antesExhibicion)}}</td>
@endif


  @endif


</tr>
@endif
@endforeach
</table>

<table style="width: 100%; margin-top: 20px">
  <tr style="text-align: center">
    <td>_______________________<br>Firma Aaron Bodega</td>
    <td>_______________________<br>Ivonne C. Arroyos P.</td>
  </tr>
  <tr>
    <td colspan="2" style="font-style: italic">Acepto que estas son las cantidades que e contado fisicamente en persona y acepto mis faltantes</td>
  </tr>
 
  <tr>
    <td colspan="2"><br><span style="font-style: italic">Acepto que esta es la cantidad recibida al: ______________________</span></td>
  </tr>
  <tr>
    <td colspan="2" style="font-style: italic">Acepto que estas son las cantidades del inventario recibido</td>
  </tr>
  <tr style="text-align: center;">
    <td><br><br>_____________________________<br>Firma de inventario recibido por:<br>_______________________</td>
  </tr>
  <tr>
    <td colspan="2"><br><span style="font-style: italic">Acepto que esta es la cantidad recibida al: ______________________</span></td>
  </tr>
</table>

      

   
<script type="text/php">
if ( isset($pdf) ) {
    $font = "helvetica";
    $pdf->page_text(520, 817, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font , 6, array(0,0,0));
}
</script> 
   
</body>
