// add_collection.js

(function($){

    var deleteItem = function (e) {
        e.preventDefault();
        var productToDelete = $(e.target).closest('.input-group');
        productToDelete.remove();
    };

    $(document).ready(function () {

        $('body').on('click', ".add-item", function (e) {
            e.preventDefault();
            var $collectionHolder = $(this).closest('.collection-wrapper');
            $collectionHolder.data('index', $collectionHolder.find('.input-group').length);
            // Get the data-prototype explained earlier
            var prototype = $collectionHolder.data('prototype');

            // get the new index
            var index = $collectionHolder.data('index');

            // Replace '__name__' in the prototype's HTML to
            // instead be a number based on how many items we have
            var newItem = prototype.replace(/__name__/g, index);

            // increase the index with one for the next item
            $collectionHolder.data('index', index + 1);

            newItem = $(newItem);
            newItem.find('.remove-item').on('click', deleteItem);
            $(this).parent().before(newItem);

            $('.datepicker').datepicker({
                dateFormat: 'dd-mm-yy',
                // altField: '#thealtdate',
                altFormat: 'yy-mm-dd'
            });
        });

        $('.remove-item').on('click', deleteItem);

    });
})(jQuery);