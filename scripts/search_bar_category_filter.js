function createSearchBar() {
    let html = '<span class="input-group-text" id="searchHelp"><i class="bi bi-search"></i></span><input id="search_input" type="text" class="form-control d-block w-75 me-2" name="search" placeholder="Search items..." aria-label="Search" aria-describedby="searchHelp" value="">';
    search_box.html(html);
}

function createCategoryFilter() { //https://getbootstrap.com/docs/5.0/forms/select/
    let html = '<select id="category_list" class="form-select" aria-label="Categories filter">';
    html += '<option selected value="0">All</option>';
    for (let category of categories) {
        html += '<option value="' + category.id + '">' + category.name + '</option>';
    }
    html += '</select>';

    search_box.append(html);
}

// methode qui verifie si un item possede la categorie selectionnee
function hasCategory(item, selected_category) {
    if (selected_category == 0) return true; // si "all" est selectionne
    for (let category of item.categories) {
        if (category.id == selected_category) return true;
    }
    return false;
}