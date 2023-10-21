$(document).ready(function () {
    // in case creation of category/expense fails, modal should stay open and display an error message
    if (shouldOpenModal == "add" || shouldOpenModal == "edit") {
        $('#categoryModal').modal('show');
        console.log("expense modal show");
    } else if (shouldOpenModal == "confirmDelete") {
        $('#confirmDeleteModal').modal('show');
    }

    //#region prevent multiple form submitions
    let formSubmitted = false;
    console.log("before: ", formSubmitted);
    document.getElementById("formAddEditCategory").addEventListener("submit", function (event) {
        console.log("add: ", formSubmitted);
        if (formSubmitted) {
            event.preventDefault();
        } else {
            formSubmitted = true;
        }
    });

    document.getElementById("formDeleteCategory").addEventListener("submit", function (event) {
        console.log("delete: ", formSubmitted);
        if (formSubmitted) {
            event.preventDefault();
        } else {
            formSubmitted = true;
        }
    });
    //#endregion

    //#region set autofocus for modal input fields
    const categoryModal = document.getElementById('categoryModal');
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    const inpTitle = document.getElementById('inpTitle');
    const inpTitleDelete = document.getElementById('inpTitleDelete');

    categoryModal.addEventListener('shown.bs.modal', () => {
        inpTitle.focus()
    });

    confirmDeleteModal.addEventListener('shown.bs.modal', () => {
        inpTitleDelete.focus()
    });

    //#endregion

    //#region check confirm delete input value
    inpTitleDelete.addEventListener("input", function () {

        const btnConfirmDelete = document.getElementById("btnConfirmDelete");
        if (inpTitleDelete.value == confirmDeleteTitle) {
            btnConfirmDelete.classList.remove("disabled");
        }
        else {
            btnConfirmDelete.classList.add("disabled");
        }
    });
    //#endregion

    //#region show alert box
    const alertDiv = document.getElementById("alertDiv");

    successAlert == "true" ? alertDiv.classList.add("alert-success") : alertDiv.classList.add("alert-danger");

    if (showAlert) {
        alertDiv.classList.remove("d-none");
    }
    else {
        alertDiv.classList.add("d-none");
    }
    //#endregion

    //#region reload page when click on cancel while adding/editing a category

    document.getElementById("btnDismissChanges").addEventListener("click", function () {
        window.location.reload();
    });
    //#endregion
});

