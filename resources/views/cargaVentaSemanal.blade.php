<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Molino</title>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
</head>
<body>
    <header>
        <h1>Carga Excel Venta Semanal</h1>
    </header>

    <main>
        <section>
            <form class="form" id="cargaLayoutVentaSemanal" method="post" enctype="multipart/form-data">
                {{csrf_field()}}
                <input type="file" name="layout" id="layout" accept=".xlsx,.xlsm">
                <button id="submit">CARGAR</button>
            </form> 
        </section>
    </main>

    <footer>
    </footer>
</body>

<script type="text/javascript">
    $("#cargaLayoutVentaSemanal").on('submit', function(e){
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'cargaLayoutVentaSemanal',
            data: new FormData(this),
            dataType: 'json',
            contentType: false,
            cache: false,
            processData:false,
            beforeSend: function(){
                // showSwalLoading();
            },
            success: function(response){
                if(response.status !== 'undefined'){
                    alert(response.msg);
                }else{
                    window.location.href = ('{{ route('descagaLayoutSemanal') }}' + '?tabla=' + encodeURIComponent(JSON.stringify(response)));
                }
            },
            error: function (error){
                // swalTimmerWarning('HA OCURRIDO UN ERROR');
            }
        });
    });
</script>
</html>
