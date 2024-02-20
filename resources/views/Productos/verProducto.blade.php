<form class="form" id="updateProducto" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <!---------------------->
    <div class="modal-header">
        <h4 class="modal-title col-12 text-center titulomodal">EDITAR PRODUCTO</h5>
    </div>
    {{--  --}}
    <div class="modal-body">
        <div class="bodymodal">
            <input type="hidden" class="form-control inputtext" id="idProductoEdit" name="idProductoEdit" value="{{$producto[0]->id}}">

            <label>NOMBRE:</label>
            <input type="text" class="form-control inputtext" id="nombreProductoEdit" name="nombreProductoEdit" placeholder="NOMBRE" maxlength="500" autocomplete="off" value="{{$producto[0]->nombre}}">
            <br>
            <label>GRUPO AL QUE PERTENECE:</label>
            <select class="form-control" id="gruposProductoEdit" name="gruposProductoEdit">
                <option value=""></option>
                @foreach ($grupos as $s)
                    <option value="{{$s->id}}" @if($producto[0]->idGrupo == $s->id) selected @endif>{{$s->nombre}} | {{$s->suma}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <!---------------------->
</form>
<div class="modal-footer">
    <button type="button" class="btn btn-success btn-sm" onclick="$('#updateProducto').submit();">GUARDAR</button>
    @if($producto[0]->estatus == 0)
        <button type="button" class="btn btn-danger btn-sm" onclick="operaciones('{{$producto[0]->id}}',1);">DESACTIVAR</button>
    @else
        <button type="button" class="btn btn-primary btn-sm" onclick="operaciones('{{$producto[0]->id}}',2);">ACTIVAR</button>
    @endif
    <button type="button" class="btn btn-secondary btn-sm" onclick="$('#modalverProducto').modal('hide');">CERRAR</button>
</div>

<script>
$('#gruposProductoEdit').select2();
$('#gruposProductoEdit').select2({
    dropdownParent: $('#modalverProducto'),
    placeholder: 'GRUPOS',
    language: {
        noResults: function(params) {
            return 'SIN RESULTADOS';
        }
    }
});

$("#updateProducto").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'updateProducto',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData:false,
        beforeSend: function(){
            swalLoading();
        },
        success: function(response){
            if(response.sta == 0){
                swalTimer('success','ACTUALIZANDO',1000);
                $('#modalverProducto').modal('hide'); 
                location.reload();
            }else{
                swalTimer('warning',response.msg,2000);
            }
        },
        error: function (error){
            swalTimer('error','HA OCURRIDO UN ERROR, INTENTALO NUEVAMENTE',2000);
        }
    });
});

function operaciones(id,t){
    var msg = null;
    if(t == 1){
        msg = "¿ESTAS SEGURO DE QUE QUIERES DESACTIVAR EL REGISTRO?";
    }else if(t == 2){
        msg = "¿ESTAS SEGURO DE QUE QUIERES REACTIVAR EL REGISTRO?";
    }

    swalConfirm(msg).then((value) => {
    if(value.value == true){
        $.ajax({
            type : "get",
            url : "operacionesProducto" + '?id=' + id + '&t=' + t,
            beforeSend : function () {
                swalLoading();
            },
            success:  function (response) {
                    swalTimer('success','ACTUALIZANDO','');
                    window.location.reload();
            },
            error: function (error){
                swalTimer('error','HA OCURRIDO UN ERROR, INTENTALO NUEVAMENTE',2000);
            }
        });
    }
    });
}
</script>