$(document).ready(function() {
        $('.select-filtrable').select2({
            width: '100%',
            minimumResultsForSearch: 0,
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                var pattern = params.term.toUpperCase().split(' ').map(term => term.replace(/[.*+?^${}()|[\\\]\\]/g, '\\$&')).join('.*');
                var regex = new RegExp(pattern, 'i');
                return regex.test(data.text.toUpperCase().replace(/\//g, ' ')) ? data : null;
            }
        });
    $(document).on('select2:open', function() {
        document.querySelector('.select2-container--open .select2-search__field').focus();
    });

    $(document).on('keypress', '.cantidad-input', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            var $this = $(this);
            var id_grad = $this.data('id');
            var cantidad = $this.val();
            if (cantidad > 0) {
                var formData = $('#formInventario').serializeArray();
                formData.push({name: 'id_grad', value: id_grad});
                formData.push({name: 'cantidad', value: cantidad});
                formData.push({name: 'ajax', value: 1});
                $.post('alta_material.php', formData, function(res) {
                    $this.addClass('border-success');
                    $this.closest('tr').next().find('.cantidad-input').focus();
                });
            }
        }
    });
});

