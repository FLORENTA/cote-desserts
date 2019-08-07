$(document).ready(() => {
    let $imagesCollectionHolder = $('#appbundle_article_images');
    let $categoriesCollectionHolder = $('#appbundle_article_categories');
    let $imageIndex = $imagesCollectionHolder.find(':input[type="file"]').length;
    let $categoryIndex = $categoriesCollectionHolder.find(':input[type="text"]').length;
    let $addImageButton = $('#button-add-image');
    let $addCategoryButton = $('#button-add-category');

    $addImageButton.click(() => {
        addForm($imagesCollectionHolder, $imageIndex, Translator.trans('admin.article.form.image.label'));
        $imageIndex++;
    });

    $addCategoryButton.click(() => {
        addForm($categoriesCollectionHolder, $categoryIndex, Translator.trans('admin.article.form.category.label'));
        $categoryIndex++;
    });

    // Creation: if entered
    // Edition: else entered
    if ($imageIndex === 0) {
        $addImageButton.click();
    } else {
        $imagesCollectionHolder.children('div').each(function() {
            addDeleteLink($(this));
        });

        updateImageLabel();
    }

    // Creation: if entered
    // Edition: else entered
    if ($categoryIndex === 0) {
        $addCategoryButton.click();
    } else {
        $categoriesCollectionHolder.children('div').each(function() {
            addDeleteLink($(this));
        });

        updateCategoryLabel();
    }
});

function updateImageLabel() {
    let $index = 1;
    $('#appbundle_article_images').find('label').each(function() {
        $(this).text(Translator.trans('admin.article.form.image.label') + ' n° ' + $index);
        $index++;
    });
}

function updateCategoryLabel() {
    let $index = 1;
    $('#appbundle_article_categories').find('label').each(function() {
        $(this).text(Translator.trans('admin.article.form.category.label') + ' n° ' + $index);
        $index++;
    });
}

function addDeleteLink($prototype, type) {
    let $deleteLink = $('<div><button class="button-delete mt5"><i class="fa fa-trash"></i> ' + Translator.trans('admin.article.form.button.remove') + '</button></div>');
    $prototype.append($deleteLink);
    $deleteLink.click(() => {
        $prototype.remove();
        if (type === 'Image') {
            updateImageLabel();
        } else {
            updateCategoryLabel();
        }
    });
}

function addForm($collectionHolder, index, type) {
    let template = $collectionHolder.attr('data-prototype')
        .replace(/__name__label__/g, type + ' n°' + (index + 1))
        .replace(/__name__/g, index);

    let $prototype = $(template);
    addDeleteLink($prototype, type);
    $collectionHolder.append($prototype);
}