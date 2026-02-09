document.addEventListener('DOMContentLoaded', function() {
    // Enable Bootstrap tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Date pickers for reports
    $('.date-picker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
    
    // Quick select all present in attendance taking
    $('#select-all-present').click(function(e) {
        e.preventDefault();
        $('select[name^="attendance["]').val('Present');
    });
    
    // Quick select all absent in attendance taking
    $('#select-all-absent').click(function(e) {
        e.preventDefault();
        $('select[name^="attendance["]').val('Absent');
    });
    
    // Auto-focus notes field when absent is selected
    $(document).on('change', 'select[name^="attendance["]', function() {
        if ($(this).val() === 'Absent') {
            const studentId = $(this).attr('name').match(/\[(\d+)\]/)[1];
            $(`input[name="notes[${studentId}]"]`).focus();
        }
    });
    
    // Confirm before deleting records
    $('.delete-btn').click(function() {
        return confirm('Are you sure you want to delete this record?');
    });
    
    // Real-time search for student lists
    $('#student-search').keyup(function() {
        const searchText = $(this).val().toLowerCase();
        $('.student-row').each(function() {
            const studentText = $(this).text().toLowerCase();
            $(this).toggle(studentText.includes(searchText));
        });
    });
});