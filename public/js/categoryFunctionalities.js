$(document).ready(function () {
    // in case creation of category/expense fails, modal should stay open and display an error message
    if (shouldOpenModal == "add" || shouldOpenModal == "edit") {
        $('#categoryModal').modal('show');
        console.log("expense modal show");
    } else if (shouldOpenModal == "confirmDelete") {
        $('#confirmDeleteModal').modal('show');
    }

    //#region set autofocus for modal input fields
    const categoryModal = document.getElementById('categoryModal');
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    const txtTitle = document.getElementById('txtTitle');
    const txtTitleDelete = document.getElementById('txtTitleDelete');

    categoryModal.addEventListener('shown.bs.modal', () => {
        txtTitle.focus()
    });

    confirmDeleteModal.addEventListener('shown.bs.modal', () => {
        txtTitleDelete.focus()
    });

    //#endregion

    //#region check confirm delete input value
    txtTitleDelete.addEventListener("input", function () {

        const btnConfirmDelete = document.getElementById("btnConfirmDelete");
        if (txtTitleDelete.value == confirmDeleteTitle) {
            btnConfirmDelete.classList.remove("disabled");
        }
        else {
            btnConfirmDelete.classList.add("disabled");
        }
    });
    //#endregion

    //#region show alert box
    const alertDiv = document.getElementById("alertDiv");

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

