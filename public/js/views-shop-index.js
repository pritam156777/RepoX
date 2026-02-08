
    $(document).ready(function() {
    const cards = $('.views-shop-index-card');
    const btnDown = $('#views-shop-index-slideDown');
    const btnUp = $('#views-shop-index-slideUp');
    const btnAll = $('#views-shop-index-showAll');

    const step = 4; // Number of cards visible initially

    // Hide all except first 4
    cards.each(function(i){
    if(i >= step) $(this).hide();
});

    // Slide Down
    btnDown.on('click', function() {
    const hiddenCards = cards.filter(':hidden');
    if(hiddenCards.length === 0) return;
    hiddenCards.slice(0, step).slideDown(400);
});

    // Slide Up
    btnUp.on('click', function() {
    const visibleCards = cards.filter(':visible');
    if(visibleCards.length <= step) return;
    visibleCards.slice(-step).slideUp(400);
});

    // Show All / Collapse
    btnAll.on('click', function() {
    if(cards.filter(':hidden').length > 0) {
    cards.slideDown(400);
} else {
    cards.each(function(i){
    if(i >= step) $(this).slideUp(400);
});
}
});

    // Auto toggle button text
    setInterval(function() {
    if(cards.filter(':hidden').length === 0){
    btnAll.find('span').text('Collapse to 4');
} else {
    btnAll.find('span').text('Show All');
}
}, 200);
});



    /* ============================
       SUPER ADMIN JS MAGIC
    ============================ */


        $('#viewsShopPhotoInput').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        // Preview image
        const reader = new FileReader();
        reader.onload = e => {
        $('#viewsShopCategoryPreview')
        .attr('src', e.target.result)
        .addClass('viewsShopImageGlow');
    };
        reader.readAsDataURL(file);

        // Activate update button
        $('#viewsShopUpdateBtn')
        .prop('disabled', false)
        .removeClass('viewsShopBtnDisabled')
        .addClass('viewsShopBtnActivePulse')
        .text('✨ Ready to Update');
    });

        // Loading state
        $('#viewsShopUpdateForm').on('submit', function () {
        $('#viewsShopUpdateBtn')
            .text('⏳ Updating...')
            .addClass('viewsShopBtnLoading')
            .prop('disabled', true);
    });

    $('#deleteConfirmModal').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const categoryName = button.data('category');
        $('#deleteCategoryName').text(categoryName);
    });

