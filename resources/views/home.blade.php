@extends('main')

@section('contenido')
<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
<h2>CARGA SEMANAL</h2>
<form class="form" id="cargaLayoutVentaSemanal" method="post" enctype="multipart/form-data">
{{csrf_field()}}
    <label for="layout" class="drop-container" id="dropcontainer">
        <span class="drop-title">Arrastra el archivo Excel ORIGINAL</span>
        o
        <input type="file" name="layout" id="layout" accept=".xlsx,.xlsm">
    </label>
    <br>
    <button id="submit" class="btn btn-success centered-button">Cargar</button>
</form>
<!------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------>
<script type="text/javascript">
// ///////////////////////////////////////////////////////////////////////
$("#cargaLayoutVentaSemanal").on('submit', function(e){
    e.preventDefault();
    $.ajax({
        type: 'POST',
        url: 'cargaLayoutVentaSemanal',
        data: new FormData(this),
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        beforeSend: function(){
            swalLoading();
        },
        success: function(response){
            if(response.status !== undefined){
                swalTimer('warning', response.msg, 2500);
            } else {
                swalTimer('success', 'GENERANDO ARCHIVO', 1000);
                window.location.href = ('{{ route('descagaLayoutSemanal') }}' + '?tabla=' + encodeURIComponent(JSON.stringify(response)));
            }
        },
        error: function (error) {
            swalTimer('error', error, 2500);
        }
    });
});

const dropContainer = document.getElementById("dropcontainer")
const fileInput = document.getElementById("layout")

dropContainer.addEventListener("dragover", (e) => {
// prevent default to allow drop
e.preventDefault()
}, false)

dropContainer.addEventListener("dragenter", () => {
dropContainer.classList.add("drag-active")
})

dropContainer.addEventListener("dragleave", () => {
dropContainer.classList.remove("drag-active")
})

dropContainer.addEventListener("drop", (e) => {
e.preventDefault()
dropContainer.classList.remove("drag-active")
fileInput.files = e.dataTransfer.files
})

dropContainer.addEventListener("drop", (e) => {
    e.preventDefault();
    dropContainer.classList.remove("drag-active");
    
    const files = e.dataTransfer.files;
    const validExtensions = [".xlsx", ".xlsm"];

    // Verificar si se ha seleccionado algún archivo
    if (files.length > 0) {
        const file = files[0];
        const fileNameParts = file.name.split(".");
        const fileExtension = `.${fileNameParts[fileNameParts.length - 1].toLowerCase()}`;

        // Verificar si la extensión del archivo es válida
        if (validExtensions.includes(fileExtension)) {
            fileInput.files = files;
        } else {
            swalTimer('warning','Solo se aceptan archivos tipo Excel del Original Sicar',2000);
        }
    }
});
// ///////////////////////////////////////////////////////////////////////
</script>
@endsection
