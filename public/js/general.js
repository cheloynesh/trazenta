function actualizarSelect(result, select)
{
    var assignPlan = $(select);

    $(select).empty();
    if(result == null || result.length == 0) {
        assignPlan.append('<option selected  value="">Seleccione una opción</option>');
    } else {
        assignPlan.append('<option selected hidden value="">Seleccione una opción</option>');
        result.forEach( function(valor, indice, array) {
            assignPlan.append("<option value='" + valor.id + "'>" + valor.name + "</option>");
        });
    }
}

function ocultar(modalId){
    $(modalId).modal("hide");
}

Date.isLeapYear = function (year) {
    return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
};

Date.getDaysInMonth = function (year, month) {
    return [31, (Date.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
};

Date.prototype.isLeapYear = function () {
    return Date.isLeapYear(this.getFullYear());
};

Date.prototype.getDaysInMonth = function () {
    return Date.getDaysInMonth(this.getFullYear(), this.getMonth());
};

Date.prototype.addMonths = function (value,day) {
    this.setDate(1);
    this.setMonth(this.getMonth() + value);
    this.setDate(Math.min(day, this.getDaysInMonth()));
    return this;
};

function padLeadingZeros(num, size){
    var s = num + "";
    while (s.length < size) s = "0" + s;
    return s;
}

var formatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  });

// Función utilitaria para retornar iconos basados en extensión
function obtenerIconoDocumento(filePath) {
    let ext = filePath.split('.').pop().toLowerCase();
    console.log("Extensión del archivo:", ext); // Depuración
    
    switch (ext) {
        case 'pdf':
            return 'fa-file-pdf text-danger'; // Rojo para PDF
            
        case 'xls':
        case 'xlsx':
        case 'csv':
            return 'fa-file-excel text-success'; // Verde para Excel
            
        case 'doc':
        case 'docx':
            return 'fa-file-word text-primary'; // Azul oscuro para Word
            
        case 'zip':
        case 'rar':
            return 'fa-file-archive text-warning'; // Amarillo para comprimidos
        
        case 'xml':
            return 'fa-file-code text-secondary'; // Gris para XML
            
        default:
            return 'fa-file-image text-info'; // Azul claro para imágenes (jpg, png, etc.)
    }
}

const contenedoresArchivosPreview = {};
const urlsArchivosPreview = {};

function limpiarUrlsPreview(key) {
    if (urlsArchivosPreview[key]) {
        urlsArchivosPreview[key].forEach(url => URL.revokeObjectURL(url));
    }
    urlsArchivosPreview[key] = [];
}

function inicializarPreviewArchivos(formSelector) {
    $(formSelector).find('input[type="file"]').each(function (idx) {
        var input = this;
        var key = (formSelector + '_' + (input.id || input.name || idx)).replace(/[^a-zA-Z0-9_]/g, '_');
        contenedoresArchivosPreview[key] = new DataTransfer();

        if ($(input).next('.file-preview-list').length === 0) {
            $(input).after('<div class="mt-2 d-flex flex-wrap file-preview-list" data-file-key="' + key + '"></div>');
        }
        var $previewBox = $(input).next('.file-preview-list');

        $(input).off('change.preview').on('change.preview', function () {
            if (!input.multiple) {
                contenedoresArchivosPreview[key] = new DataTransfer();
            }
            var dt = contenedoresArchivosPreview[key];

            Array.from(input.files).forEach(function (file) {
                dt.items.add(file);
            });

            input.files = dt.files;
            renderizarBadgesPreview(key, input, $previewBox);
        });
    });
}

function renderizarBadgesPreview(key, input, $container) {
    $container.empty();
    limpiarUrlsPreview(key);
    var dt = contenedoresArchivosPreview[key];

    Array.from(dt.files).forEach(function (file, index) {
        var icono = obtenerIconoDocumento(file.name);
        var fileUrl = URL.createObjectURL(file);
        urlsArchivosPreview[key].push(fileUrl);

        var $badge = $(`
            <div class="d-inline-flex align-items-center bg-white border pl-3 pr-2 py-2 mr-2 mb-2 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                <a href="${fileUrl}" target="_blank" rel="noopener noreferrer" class="d-inline-flex align-items-center text-decoration-none mr-3" title="Abrir ${file.name}">
                    <i class="fas ${icono} mr-2"></i>
                    <span class="font-weight-bold text-muted small" style="max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${file.name}</span>
                </a>
                <button type="button" class="btn-remove-file" title="Quitar archivo" style="background: transparent; border: none; padding: 0; color: #94a3b8; cursor: pointer;">
                    <i class="fas fa-times" style="font-size: 11px;"></i>
                </button>
            </div>
        `);

        $badge.find('.btn-remove-file').on('click', function () {
            quitarArchivoPreview(key, index, input, $container);
        });

        $container.append($badge);
    });
}

function quitarArchivoPreview(key, indexEliminar, input, $container) {
    var dtActual = contenedoresArchivosPreview[key];
    var nuevoDt = new DataTransfer();

    Array.from(dtActual.files).forEach(function (file, idx) {
        if (idx !== indexEliminar) {
            nuevoDt.items.add(file);
        }
    });

    contenedoresArchivosPreview[key] = nuevoDt;
    input.files = nuevoDt.files;
    if (nuevoDt.files.length === 0) {
        $(input).val('');
    }
    renderizarBadgesPreview(key, input, $container);
}

function limpiarPreviewInput(inputSelector) {
    var $input = $(inputSelector);
    if (!$input.length) return;
    var $box = $input.next('.file-preview-list');
    var key = $box.attr('data-file-key');
    if (key) {
        limpiarUrlsPreview(key);
        contenedoresArchivosPreview[key] = new DataTransfer();
        $input[0].files = contenedoresArchivosPreview[key].files;
    }
    $input.val('');
    $box.empty();
}