<form class="form" id="guardarProducto" method="post" enctype="multipart/form-data">
    {{csrf_field()}}
    <!---------------------->
    <div class="modal-header">
        <h4 class="modal-title col-12 text-center titulomodal">AGREGAR NUEVO PRODUCTO</h5>
    </div>
    {{--  --}}
    <div class="modal-body">
        <div class="bodymodal">
            <label>NOMBRE:</label>
            <input type="text" class="form-control inputtext" id="nombreProducto" name="nombreProducto" placeholder="NOMBRE" maxlength="500" autocomplete="off">
            <br>
            <label>GRUPO AL QUE PERTENECE:</label>
            <select class="form-control" id="gruposProducto" name="gruposProducto">
                <option value=""></option>
                @foreach ($grupos as $s)
                    <option value="{{$s->id}}">{{$s->nombre}} | {{$s->suma}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <!---------------------->
</form>
<div class="modal-footer">
    <button type="button" class="btn btn-success btn-sm" onclick="$('#guardarProducto').submit();">GUARDAR</button>
    <button type="button" class="btn btn-secondary btn-sm" onclick="$('#modalagregarProducto').modal('hide');">CERRAR</button>
</div>

<script>
$('#gruposProducto').select2();
$('#gruposProducto').select2({
    dropdownParent: $('#modalagregarProducto'),
    placeholder: 'GRUPOS',
    language: {
        noResults: function(params) {
            return 'SIN RESULTADOS';
        }
    }
});

$("#guardarProducto").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'guardarProducto',
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
                $('#modalagregarProducto').modal('hide'); 
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
</script>