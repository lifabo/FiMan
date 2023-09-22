$(document).ready(function () {
    // in case creation of category/expense fails, modal should stay open and display an error message
    if (shouldOpenModal == "add" || shouldOpenModal == "edit") {
        $("#expenseModal").modal('show');
        console.log("expense modal show");
    } else if (shouldOpenModal == "confirmDelete") {
        $('#confirmDeleteModal').modal('show');
    }

    //#region set autofocus for modal input fields
    const expenseModal = document.getElementById('expenseModal');
    const inpAmount = document.getElementById('inpAmount');

    expenseModal.addEventListener('shown.bs.modal', () => {
        inpAmount.focus()
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

    //#region reload page when click on cancel while adding/editing a category/expense
    document.getElementById("btnDismissChanges").addEventListener("click", function () {
        window.location.reload();
    });
    //#endregion
});

