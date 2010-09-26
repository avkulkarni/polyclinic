var trClone; // first loaded tr is cloned
$(function() {
    var tableScope = $('#checkups_medicines');
    var tableScopeBody = $('tbody:first', tableScope);
    
    // add row
    $('#add_row').live("click", function(e) {
        var lastRow = $('tr.row_checkups_medicine:last', tableScope);
        
        // make sure exists one row on tableScope
        if ( lastRow.length ) {
            // preformating the clone
            newRow = preformatNewRow(lastRow.clone());
            
            // cache for later use
            if ( typeof(trClone) == 'undefined' ) {
                trClone = newRow;
            }
            
            // tableScopeBody.append(newRow);
            $('.row_checkups_medicine:last', tableScope).after(newRow);
        }
    });
    
    // delete row
    $('#del_row').click(function(e) {
        var totalCb = $('.cb_checkups_medicines').length;
        var totalChecked = $('.cb_checkups_medicines:checked').length;
        
        if ( totalChecked == 0 ) {
            alert('Centang obat yang ingin Anda hapus pada kotak\n'+
                  'kecil sebelah kiri pilihan nama obat'
            );
            return false;
        }
        
        if ( totalChecked < totalCb ) {
            $('.cb_checkups_medicines:checked').each(function(idx) {
                var rowId = $(this).parent().parent().attr('id');
                $('tr[id^=' + rowId + ']').remove();
            });
        } else {
            alert('Anda tidak dapat menghapus obat ini,\n' +
                  'karena harus ada 1 barang yang tersusa, jika ada\n' +
                  'lebih dari 1 barang, maka obat ini dapat dihapus'
            );
            return false;
        }
        
        e.preventDefault();
    });
    
    // force numeric on input.numeric
    $('.numeric').numeric();
    
    // bind change select
    $('#CheckupPatientId').change(function() {
        var id = this.value;
        if ( id ) {
            $.get(ajaxURL + id, function(data) {
                $('#patient_detail').html(data);
            });
        }
    });
});

/**
 * Function to format new tr after
 * #add_row is clicked
 * @param dom tr cloned row to be formatted before appended into tbody
 * @return dom tr after formatted
 */
function preformatNewRow(tr) {
    // get row-xth
    // and increment it
    var oldId = tr.attr('id').substr(1)*1;
    var newId = oldId+1;
    $(tr).attr('id', 'r' + newId);
    
    // rel is
    $(tr).attr('rel', 'r' + newId + '_d0');
    
    // change index of all input elements
    $('.cb_checkups_medicines:first', tr).attr('name', 'data[Checkup][CheckupsMedicine][' +
        newId + '][id]');
    $('.medicine_id:first', tr).attr('name', 'data[Checkup][CheckupsMedicine][' +
        newId + '][medicine_id]');
    $('.qty:first', tr).attr('name', 'data[Checkup][CheckupsMedicine][' +
        newId + '][qty]');

    return tr;
}
