$(document).ready(function () {
    // Initialize DataTable
    var table = $('#activity_logs_table').DataTable({
        "pageLength": 25,
        "order": [
            [0, 'desc']
        ],
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    // Action Filter (Column index 2)
    $('#actionFilter').on('change', function () {
        var value = $(this).val();
        table.column(2).search(value).draw();
    });

    // User Filter (Column index 1 - searching in username)
    $('#userFilter').on('change', function () {
        var value = $(this).val();
        table.column(1).search(value).draw();
    });

    // Table Name Filter (Column index 3)
    $('#tableFilter').on('keyup', function () {
        var value = $(this).val();
        table.column(3).search(value).draw();
    });

    // Clear All Filters
    $('#clearFilters').on('click', function () {
        $('#actionFilter').val('');
        $('#userFilter').val('');ƒ
        $('#tableFilter').val('');

        table.columns().search('').draw();
    });
});