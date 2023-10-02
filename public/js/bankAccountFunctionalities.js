$(document).ready(function () {
    // in case creation of category/expense fails, modal should stay open and display an error message
    if (shouldOpenModal == "add" || shouldOpenModal == "edit") {
        $("#bankAccountModal").modal('show');
        console.log("expense modal show");
    } else if (shouldOpenModal == "confirmDelete") {
        $('#confirmDeleteModal').modal('show');
    }

    //#region set autofocus for modal input fields
    const bankAccountModal = document.getElementById('bankAccountModal');
    const inpTitle = document.getElementById('inpTitle');

    bankAccountModal.addEventListener('shown.bs.modal', () => {
        inpTitle.focus()
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

    //#region reload page when click on cancel while adding/editing a expense
    document.getElementById("btnDismissChanges").addEventListener("click", function () {
        window.location.reload();
    });
    //#endregion
});

