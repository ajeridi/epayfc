$(function () {
    search_box = $("#search"); // on prend la reference vers l'element qui va contenir la search bar
    createSearchBar(); // on cree la searchbar d'abord, puis apres on prend la ref vers le input
    search_input = $("#search_input");
    createCategoryFilter(); // creation du filtre de categorie
    category_list = $("#category_list");

    items_participated_in_container = $("#items_participated_in");
    other_available_items_container = $("#other_available_items");


    // si critere de recherche existant, on remplit le champs
    if (search_criteria != "") { // si pas de critere de recherche , search_criteria vaut "" si premiere fois sur browse ou -1, si navigation a eu lieu
        search_input.val(search_criteria);
    }

    if (selected_category != "") {
        category_list.val(selected_category);
    }

    // creation des carte des items
    displayItems(search_input.val(), category_list.val());


    // filtrage lors du input dans la barre de recherche
    search_input.on('input', async function () {
        displayItems(search_input.val(), category_list.val());
    })

    //filtrage lors de la selection d'une categorie
    category_list.on('change', function () {
        displayItems(search_input.val(), category_list.val());
    })
});

async function displayItems(search_criteria, selected_category) {
    items_participated_in_container.html("");
    other_available_items_container.html("");
    displayItemsParticipatedIn(search_criteria, selected_category);
    displayOtherAvailableItems(search_criteria, selected_category);
}

async function displayItemsParticipatedIn(search_criteria, selected_category) {
    // va display les carte des items
    for (let item of items_participated_in) { // for respecte mieux le await que le forEach
        if ((search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase()))
            && hasCategory(item, selected_category)) {
            let card = await createItemCard(item);
            items_participated_in_container.append(card);
        }
    }
    if (items_participated_in_container.html() === "") {
        items_participated_in_container.html('<p>No items found</p>');
    }
}

async function displayOtherAvailableItems(search_criteria, selected_category) {
    for (let item of other_available_items) {
        if ((search_criteria === "" || item.title.toLowerCase().includes(search_criteria.toLowerCase()) || item.description.toLowerCase().includes(search_criteria.toLowerCase()) || item.pseudo.toLowerCase().includes(search_criteria.toLowerCase()))
            && hasCategory(item, selected_category)) {
            let card = await createItemCard(item);
            other_available_items_container.append(card);
        }
    }
    if (other_available_items_container.html() === "") {
        other_available_items_container.html('<p>No items found</p>');
    }
}
// ca affichait promise parce que je ne respectais pas les await (remarque)