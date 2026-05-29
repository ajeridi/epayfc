let categoryList;
let deleteModal;
let categoryToDeleteId = null;
let categoryToDeleteRow = null;

$(function () {
    categoryList = $("#jsCategoryList");

    $("#editZone").hide();

    $("#form hr").hide();

    $("#new_category").closest(".row").hide();

    deleteModal = new bootstrap.Modal(document.getElementById("deleteCategoryModal"));

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
    const nameSpan = row.find(".js-category-name");
    const errorZone = row.find(".js-category-error");

    const id = row.data("id");
    const newName = input.val().trim();

    input.removeClass("is-invalid");
    errorZone.text("");

    if (newName.length < 3 || newName.length > 25) {
        input.addClass("is-invalid");
        errorZone.text("Category name length must be between 3 and 25 characters.");
        return;
    }

    await $.post(
        "category/save_name_service",
        {
            id: id,
            name: newName
        },
        function (response) {
            if (response.success) {
                nameSpan.data("name", response.name);
                nameSpan.html(escapeHtml(response.name));
            } else {
                input.addClass("is-invalid");
                errorZone.text(response.error);
            }
        },
        "json"
    ).fail(function (xhr) {
        console.log(xhr.responseText);
        input.addClass("is-invalid");
        errorZone.text("Save error.");
    });
});

$(document).on("click", "#jsAddCategoryBtn", function () {
    if ($("#jsNewCategoryRow").length > 0) {
        return;
    }

    $("#jsAddCategoryBtn").closest("div").before(`
        <div id="jsNewCategoryRow" class="row align-items-center mt-2 mb-2">
            <div class="col-8 d-flex align-items-center gap-2">
                <input type="text"
                       id="jsNewCategoryInput"
                       class="form-control form-control-sm d-inline-block"
                       style="width: 220px;"
                       placeholder="Category name">

                <span id="jsNewCategoryError" class="text-danger ms-2"></span>
            </div>

            <div class="col-4 text-end">
                <button type="button" id="jsCancelAddCategoryBtn" class="btn btn-secondary btn-sm">
                    Cancel
                </button>
            </div>
        </div>
    `);

    $("#jsNewCategoryInput").focus();
});

$(document).on("click", "#jsCancelAddCategoryBtn", function () {
    $("#jsNewCategoryRow").remove();
});

$(document).on("blur", "#jsNewCategoryInput", async function () {
    const input = $(this);
    const newName = input.val().trim();
    const errorZone = $("#jsNewCategoryError");

    input.removeClass("is-invalid");
    errorZone.text("");

    if (newName === "") {
        return;
    }

    if (newName.length < 3 || newName.length > 25) {
        input.addClass("is-invalid");
        errorZone.text("Category name length must be between 3 and 25 characters.");
        return;
    }

    await $.post(
        "category/save_name_service",
        {
            id: 0,
            name: newName
        },
        function (response) {
            if (response.success) {
                location.reload();
            } else {
                input.addClass("is-invalid");
                errorZone.text(response.error);
            }
        },
        "json"
    ).fail(function (xhr) {
        console.log(xhr.responseText);
        input.addClass("is-invalid");
        errorZone.text("Save error.");
    });
});

$(document).on("click", ".js-delete-btn", function () {
    const row = $(this).closest(".js-category-row");
    const name = row.find(".js-category-name").data("name");

    categoryToDeleteId = row.data("id");
    categoryToDeleteRow = row;

    $("#deleteCategoryName").text(name);

    deleteModal.show();
});

$(document).on("click", "#confirmDeleteCategoryBtn", async function () {
    if (categoryToDeleteId === null) {
        return;
    }

    await $.post(
        "category/delete_service",
        {
            id: categoryToDeleteId
        },
        function (response) {
            if (response.success) {
                categoryToDeleteRow.remove();

                categoryToDeleteId = null;
                categoryToDeleteRow = null;

                deleteModal.hide();
            } else {
                alert(response.error);
            }
        },
        "json"
    ).fail(function (xhr) {
        console.log(xhr.responseText);
        alert("Delete error.");
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