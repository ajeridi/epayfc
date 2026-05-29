let categoryList;
$(function () {
    categoryList = $("#jsCategoryList");

    $("#editZone").hide();

    $("#form hr").hide();

    $("#new_category").closest(".row").hide();

    renderCategories(categories);
    activateSortable();

    $(document).on("click", ".js-edit-btn", function () {
        const row = $(this).closest(".js-category-row");
        const nameSpan = row.find(".js-category-name");

        const currentName = nameSpan.data("name");

        nameSpan.html(`
            <input type="text" class="form-control form-control-sm js-edit-input" value="${escapeHtml(currentName)}">
        `);

        row.find(".js-edit-input").focus();
    });
});

function renderCategories(categories) {
    const list = $("#jsCategoryList");
    list.empty();

    categories.forEach(function (category) {
        list.append(createCategoryRow(category));
    });

    list.append(`
        <div class="text-end mt-3">
            <button id="jsAddCategoryBtn" type="button" class="btn btn-primary">
                <i class="bi bi-plus"></i>
            </button>
        </div>
    `);
}

function createCategoryRow(category) {
    const deleteDisabled = category.is_empty ? "" : "disabled";

    return `
        <div class="row align-items-center mt-2 mb-2 js-category-row" data-id="${category.id}">
            <div class="col-8 d-flex align-items-center gap-2">
                <span class="text-secondary drag-handle" style="cursor: grab;">⋮⋮</span>

                <span class="js-category-name text-white" data-name="${escapeHtml(category.name)}">
                    ${escapeHtml(category.name)} (${category.priority})
                </span>

                <span class="js-category-error text-danger ms-2"></span>
            </div>

            <div class="col-4 text-end">
                <button type="button" class="btn btn-outline-secondary btn-sm js-edit-btn">
                    Edit
                </button>

                <button type="button" class="btn btn-outline-danger btn-sm js-delete-btn" ${deleteDisabled}>
                    Delete
                </button>
            </div>
        </div>
    `;
}

function escapeHtml(text) {
    return $("<div>").text(text).html();
}

function activateSortable() {
    categoryList.sortable({
        items: ".js-category-row",
        handle: ".drag-handle",

        update: async function () {
            let newOrder = categoryList.sortable("toArray", {
                attribute: "data-id"
            });

            await updateCategoriesPriority(newOrder);
        }
    });
}

async function updateCategoriesPriority(newOrder) {
    await $.post("category/update_priorities_service",
        {
            order: newOrder
        },
        function (response) {
            console.log(response);
        },
        "json"
    );
}