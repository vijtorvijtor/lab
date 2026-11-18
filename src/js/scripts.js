$(document).ready(function() {
    $('.select-filtrable').select2({
        placeholder: "Buscar...",
        allowClear: true,
        minimumResultsForSearch: 0,
        matcher: function(params, data) {
            if ($.trim(params.term) === '') return data;
            
            var pattern = params.term.toUpperCase().split(' ').map(function(term) {
                return term.replace(/[.*+?^${}()|[\\\]\\]/g, '\\$&'); 
            }).join('.*');
            
            var regex = new RegExp(pattern, 'i');
            var text = data.text.toUpperCase().replace(/\//g, ' ');
            
            return regex.test(text) ? data : null;
        }
    });
});