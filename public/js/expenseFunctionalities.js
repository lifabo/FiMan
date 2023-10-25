$(document).ready(function () {
    //#region set default timestamp value to current date
    let inpTimestamp = document.getElementById("inpTimestamp");
    if (inpTimestamp.value == "")
        inpTimestamp.value = new Date().toISOString().split('T')[0];
    //#endregion

    // in case creation of category/expense fails, modal should stay open and display an error message
    if (shouldOpenModal == "add" || shouldOpenModal == "edit") {
        $("#expenseModal").modal('show');
        console.log("expense modal show");
    } else if (shouldOpenModal == "confirmDelete") {
        $('#confirmDeleteModal').modal('show');
    }

    //#region prevent multiple form submitions
    let formSubmitted = false;
    document.getElementById("formAddEditExpense").addEventListener("submit", function (event) {
        if (formSubmitted) {
            event.preventDefault();
        } else {
            formSubmitted = true;
        }
    });

    document.getElementById("formDeleteExpense").addEventListener("submit", function (event) {
        if (formSubmitted) {
            event.preventDefault();
        } else {
            formSubmitted = true;
        }
    });
    //#endregion

    //#region set autofocus for modal input fields
    const expenseModal = document.getElementById('expenseModal');
    const inpAmount = document.getElementById('inpAmount');

    expenseModal.addEventListener('shown.bs.modal', () => {
        inpAmount.focus()
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

    //#region load expenses of bank account when selected bank account changes
    const formSelectBankAccount = document.getElementById("formSelectBankAccount");
    const selectBankAccount = document.getElementById("selectBankAccount");

    selectBankAccount.addEventListener('change', function () {
        formSelectBankAccount.submit();
    });
    //#endregion

    //#region disable controls when user has no bank account
    if (disableControls == true) {
        document.getElementById("btnOpenAddModal").classList.add("d-none");
        document.getElementById("tblExpenses").classList.add("d-none");
        selectBankAccount.classList.add("d-none");
        document.getElementById("lblBankAccountBalance").classList.add("d-none");
        document.getElementById("lblSelectBankAccount").classList.add("d-none");
        document.getElementById("txtBalance").classList.add("d-none");
    }
    //#endregion
});
