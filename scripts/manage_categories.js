let categoryList;
$(function () {
    categoryList = $("#jsCategoryList");

    $("#editZone").hide();

    $("#form hr").hide();

    $("#new_category").closest(".row").hide();

    renderCategories(categories);
    activateSortable();

});

$(document).on("click", ".js-edit-btn", function () {
    const row = $(this).closest(".js-category-row");
    const nameSpan = row.find(".js-category-name");

    const currentName = nameSpan.data("name");

    nameSpan.html(`
            <input type="text" class="form-control form-control-sm js-edit-input" value="${escapeHtml(currentName)}">
        `);

    row.find(".js-edit-input").focus();
});

$(document).on("blur", ".js-edit-input", async function () {
    const input = $(this);
    const row = input.closest(".js-category-row");
    const errorZone = row.find(".js-category-error");

    const id = row.data("id");
    const newName = input.val().trim();

    errorZone.text("");
    input.removeClass("is-invalid");

    // validation synchrone
    if (newName.length < 3 || newName.length > 25) {
        input.addClass("is-invalid");
        errorZone.text("Category name length must be between 3 and 25 characters.");
        return;
    }

    // validation asynchrone
    await $.post(
        "category/validate_name_service",
        {
            id: id,
            name: newName
        },
        function (response) {
            if (!response.valid) {
                input.addClass("is-invalid");
                errorZone.text(response.error);
            } else {
                console.log("valid");
            }
        },
        "json"
    ).fail(function (xhr) {
        console.log(xhr.responseText);
        input.addClass("is-invalid");
        errorZone.text("Validation error.");
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
                        ${escapeHtml(category.name)}
                </span>

                <span class="js-category-priority text-white">
                    (${category.priority})
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