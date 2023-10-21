$(document).ready(function () {
    // define color for each category
    const colorForEachCategory = {};
    for (let i = 0; i < allCategories.length; i++) {
        colorForEachCategory[allCategories[i].title] = getNextHexColor(i);

        console.log(getNextHexColor(i));
    }

    let CanvasExpensesAmountPerCategoryCurrentMonth = document.getElementById("CanvasExpensesAmountPerCategoryCurrentMonth");
    let CanvasExpensesAmountPerCategoryCurrentMonth2 = document.getElementById("CanvasExpensesAmountPerCategoryCurrentMonth2");

    const categoryLabels = expensesAmountPerCategoryCurrentMonth.map(expense => expense.categoryTitle);
    const expenseAmounts = expensesAmountPerCategoryCurrentMonth.map(expense => expense.totalAmount);

    replaceNullValue(categoryLabels, "Sonstiges");

    // add exactly as many background colors to the array as there are categories
    const backgroundColors = [];
    for (let i = 0; i < categoryLabels.length; i++) {
        backgroundColors.push(colorForEachCategory[categoryLabels[i]]);
    }

    let suggestedMax, suggestedMin;

    if (Math.max(...expenseAmounts) > 0)
        suggestedMax = Math.max(...expenseAmounts) * 1.1;
    else
        suggestedMax = Math.min(...expenseAmounts) * -0.1;

    if (Math.min(...expenseAmounts) > 0)
        suggestedMin = Math.max(...expenseAmounts) * -0.1;
    else
        suggestedMin = Math.min(...expenseAmounts) * 1.1;

    new Chart(CanvasExpensesAmountPerCategoryCurrentMonth, {
        type: 'bar',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: 'Aktueller Monat über alle Konten hinweg',
                backgroundColor: backgroundColors,
                data: expenseAmounts,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    suggestedMax: suggestedMax,
                    suggestedMin: suggestedMin,
                    reverse: true
                }
            }
        }
    });


    //#region expensesAmountPerCategoryPerMonthLast12Months

    // replace null category values
    for (let i = 0; i < expensesAmountPerCategoryPerMonthLast12Months.length; i++) {
        if (expensesAmountPerCategoryPerMonthLast12Months[i].categoryTitle == null)
            expensesAmountPerCategoryPerMonthLast12Months[i].categoryTitle = "Sonstiges"
    }

    // save as a set so that entries are unique and not duplicate
    const months = [...new Set(expensesAmountPerCategoryPerMonthLast12Months.map(expense => expense.month))];
    const categories = [...new Set(expensesAmountPerCategoryPerMonthLast12Months.map(expense => expense.categoryTitle))];
    const datasetData = {};

    // result will be a "map" having a maximum (maybe you have no expenses from 12 months ago, then it will be less) of 12 values per category,
    // each of the 12 values represents the total amount of a month in that category
    // first fill it with default value 0
    categories.forEach(category => {
        datasetData[category] = new Array(months.length).fill(0); // Ein leeres Array für jeden Monat
    });

    // now fill the "map" with the correct total amounts for each month per category
    expensesAmountPerCategoryPerMonthLast12Months.forEach(expense => {
        const monthIndex = months.indexOf(expense.month);
        if (monthIndex !== -1) {
            datasetData[expense.categoryTitle][monthIndex] = expense.totalAmount;
        }
    });

    // save array of datasets in a form that is correct for chart.js
    const datasets = categories.map(category => ({
        label: category,
        data: datasetData[category],
        borderWidth: 2,
        borderColor: null,
        backgroundColor: null
    }));

    // change color for each dataset
    for (let i = 0; i < datasets.length; i++) {
        datasets[i].borderColor = colorForEachCategory[datasets[i].label];
        datasets[i].backgroundColor = colorForEachCategory[datasets[i].label];
    }

    new Chart(CanvasExpensesAmountPerCategoryCurrentMonth2, {
        type: 'line',
        data: {
            labels: months,
            datasets: datasets,
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 20,
                    suggestedMin: -5,
                    reverse: true
                },
            },
            elements: {
                line: {
                    tension: 0.2
                }
            }
        },
    });
    //#endregion


});


function getNextHexColor(index) {
    // random color codes
    const colors = [
        "#241023", "#6B0504", "#A3320B", "#D5E68D", "#47A025",
        "#312F2F", "#84DCCF", "#A6D9F7", "#BCCCE0", "#BF98A0",
        "#BF211E", "#F9DC5C", "#69A197", "#D8DBE2", "#58A4B0",
        "#1B1B1E", "#ABE188", "#F1BB87", "#F7EF99", "#5D675B",
        "#5F464B", "#8E4A49", "#7DAA92", "#C2FBEF", "#394053",
        "#7CAE7A", "#E26D5C", "#723D46", "#472D30", "#7B435B"

    ];

    return colors[index % (colors.length - 1)];
}

function replaceNullValue(data, replacement) {
    // replaces null value for example with "Sonstiges"
    for (let i = 0; i < data.length; i++) {
        if (data[i] == null) {
            data[i] = replacement;
        }
    }
}
