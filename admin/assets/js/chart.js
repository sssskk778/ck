// Инициализация DataTables для всех таблиц с классом .datatable
$(document).ready(function() {
    $('.datatable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/ru.json"
        },
        "responsive": true,
        "autoWidth": false
    });
});

// Подтверждение для опасных действий (удаление и т.д.)
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.confirm-action');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите выполнить это действие?')) {
                e.preventDefault();
            }
        });
    });
});