@extends('main')

@section('contenido')
<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
<h5 class="card-title">
    <button type="button" class="btn btn-success" onclick="agregarProducto();">Agregar</button>
    <span class="colvisBut"></span>
</h5>
<p></p>
<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
<section class="section profile">
<div class="row">
    <table id="Dtable" class="styled-table" style="width:100%">
        <thead>
            <tr>
                <th class="colcont" id="c0"></th>
                <th class="colcont" id="c1"></th>
                <th class="colcont" id="c2"></th>
                <th class="colcont" id="c3"></th>
            </tr>
            <tr>
                <th class="col" style="width: 5% !important;">ID</th>
                <th class="col" style="width: 45% !important;">NOMBRE</th>
                <th class="col" style="width: 40% !important;">GRUPO</th>
                <th class="col" style="width: 10% !important;">ESTATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $t)
                <tr style="cursor: pointer" onclick="verProducto('{{$t->id}}')">
                    <td>{{$t->id}}</td>
                    <td class="lefti">{{$t->nombre}}</td>
                    <td class="lefti">{{$t->nombreGrupo}}</td>
                    <td @if($t->estatus == 0) value="ACTIVO" @else value="DESHABILITADO" @endif>
                        @if($t->estatus == 0)
                            <span class="badge rounded-pill bg-primary">ACTIVO</span>
                        @else
                            <span class="badge rounded-pill bg-secondary">DESHABILITADO</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td class="filtercol"><input type="text" class="thfilter" idc="0" id="i0"></td>
                <td class="filtercol"><input type="text" class="thfilter" idc="1" id="i1"></td>
                <td class="filtercol"><input type="text" class="thfilter" idc="2" id="i2"></td>
                <td class="filtercol"><input type="text" class="thfilter" idc="3" id="i3"></td>
            </tr> 
        </tfoot>
    </table>
</div>
</section>
<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
{{--  --}}
<div class="modal fade" id="modalverProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            {{--  --}}
            <div id="modalverProductoBody">

            </div>
            {{--  --}}
        </div>
    </div>
</div>
{{--  --}}
{{--  --}}
<div class="modal fade" id="modalagregarProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            {{--  --}}
            <div id="modalagregarProductoBody">

            </div>
            {{--  --}}
        </div>
    </div>
</div>
{{--  --}}
<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
<script type="text/javascript">
// ///////////////////////////////////////////////////////////////////////
var c_CID = 0;
var c_NOM = 1;
var c_GRU = 2;
var c_STA = 3;
// ///////////////////////////////////////////////////////////////////////
Dtable();
function Dtable(){
var Dtable = $('#Dtable').DataTable({
    "sDom": "tp",
    scrollY: "500px",
    scrollX: true,
    paging: false,
    "language": {
        "sProcessing": "Procesando...",
        "sLengthMenu": "# REG _MENU_ ",
        "sZeroRecords": "No se encontraron resultados",
        "sEmptyTable": "Ningún dato disponible en esta tabla",
        "sInfo": "_START_ - _END_",
        "sInfoEmpty": "",
        "sInfoFiltered": "",
        "sInfoPostFix": "",
        "sSearch": "<i class='fa fa-search'></i>",
        "sUrl": "",
        "sInfoThousands": ","
    },
    columnDefs: [
        { "targets": c_CID }
    ],
    buttons: [{
        text: 'COLUMNAS',
        extend: 'colvis',
    }],
})

Dtable.buttons().container().appendTo($('.colvisBut'));

contador(Dtable);

$('.thfilter').on('keyup change blur',function () {let idc = this.getAttribute("idc");Dtable.columns(idc).search( this.value ).draw();contador(Dtable);});

}

function contador(Dtable) {
    $('#c'+c_CID).html(number_format(Dtable.column(c_CID,{filter: 'applied'}).data().filter(function(value, index){return value != "" ? true : false;}).count()));
    $('#c'+c_NOM).html(number_format(Dtable.column(c_NOM,{filter: 'applied'}).data().filter(function(value, index){return value != "" ? true : false;}).count()));
    $('#c'+c_GRU).html(number_format(Dtable.column(c_GRU,{filter: 'applied'}).data().filter(function(value, index){return value != "" ? true : false;}).count()));
    $('#c'+c_STA).html(contarEstatus(c_STA));
}

function contarEstatus(columna) {
    let contadorA = 0;
    let contadorI = 0;
    $("#Dtable tr").find('td:eq(' + columna + ') span').each(function() {
        let contenido = $(this).html();
        if (contenido == "ACTIVO") {
            contadorA++;
        }else{
            contadorI++;
        }
    });

    let cadena = 'ACT: ' + contadorA + '&nbsp;&nbsp; DES: ' + contadorI;
    $("#c"+columna).html(cadena);
}
// ///////////////////////////////////////////////////////////////////////
function agregarProducto(){
    $.ajax({
        data: { _token: "{{ csrf_token() }}" },
        type : "GET",
        url : "{{route('agregarProducto')}}",
        beforeSend : function () {
            $("#modalagregarProductoBody").html('{{Html::image('img/loading.gif', 'CARGANDO ESPERE', ['class' => 'center-block'])}}');
        },
        success:  function (response) {
            $('#modalagregarProducto').modal({backdrop: 'static',keyboard: false});
            $('#modalagregarProducto').modal('show');
            $("#modalagregarProductoBody").html(response);
        },
        error: function(error) {
            swalTimer('error','HA OCURRIDO UN ERROR, INTENTALO NUEVAMENTE',2000);
        }
    });
}
// ///////////////////////////////////////////////////////////////////////
function verProducto(id){
    $.ajax({
        data: { 'id':id, _token: "{{ csrf_token() }}" },
        type : "GET",
        url : "{{route('verProducto')}}",
        beforeSend : function () {
            $("#modalverProductoBody").html('{{Html::image('img/loading.gif', 'CARGANDO ESPERE', ['class' => 'center-block'])}}');
        },
        success:  function (response) {
            $('#modalverProducto').modal({backdrop: 'static',keyboard: false});
            $('#modalverProducto').modal('show');
            $("#modalverProductoBody").html(response);
        },
        error: function(error) {
            swalTimer('error','HA OCURRIDO UN ERROR, INTENTALO NUEVAMENTE',2000);
        }
    });
}
// ///////////////////////////////////////////////////////////////////////
</script>
@endsection