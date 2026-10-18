$(async function () {
    search_box = $("#search"); // on prend la reference vers l'element qui va contenir la search bar
    createSearchBar(); // on cree la searchbar d'abord, puis apres on prend la ref vers le input
    search_input = $("#search_input");
    createCategoryFilter(); // creation du filtre de categorie
    category_list = $("#category_list");

    active_items_container = $("#active_items");
    closed_unsold_items_container = $("#closed_unsold_items");
    sold_items_container = $("#sold_items");

    if (search_criteria != "") {
        search_input.val(search_criteria);
    }

    if (selected_category != "") {
        category_list.val(selected_category);
    }

    // creation des carte des items
    await displayAllItemCategories(search_input.val(), category_list.val());

    search_input.on('input', async function () {
        await displayAllItemCategories(search_input.val(), category_list.val());
    })

    //filtrage lors de la selection d'une categorie
    category_list.on('change', function () {
        displayAllItemCategories(search_input.val(), category_list.val());
    })
});

async function displayAllItemCategories(search_criteria, selected_category) {
    let encoded = "";
    if (search_criteria !== "" || selected_category !== "0") {
        let criteria = await $.getJSON("item/get_encoded_data_service/" + (search_criteria === "" ? "-1" : search_criteria) + "/" + selected_category);
        encoded = criteria.encoded_criteria;
    }

    displayItems(search_criteria, selected_category, active_items, active_items_container, encoded);
    displayItems(search_criteria, selected_category, closed_unsold_items, closed_unsold_items_container, encoded);
    displayItems(search_criteria, selected_category, sold_items, sold_items_container, encoded);
}

function displayItems(search_criteria, selected_category, items, container, encoded) {
    container.html("");
    for (let item of items) {
        if ((search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase())) &&
            hasCategory(item, selected_category)) {
            let card = createItemCard(item, encoded);
            container.append(card);
        }
    }
    if (container.html() === "") {
        container.html('<p>No items found</p>');
    }
}