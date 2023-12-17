$(document).ready(function () {
    // define color for each category
    const colorForEachCategory = {};
    for (let i = 0; i < allCategories.length; i++) {
        colorForEachCategory[allCategories[i].title] = getNextHexColor(i);

        //console.log(getNextHexColor(i));
    }

    let CanvasExpensesAmountPerCategoryCurrentMonthNegative = document.getElementById("CanvasExpensesAmountPerCategoryCurrentMonthNegative");
    let CanvasExpensesAmountPerCategoryCurrentMonthPositive = document.getElementById("CanvasExpensesAmountPerCategoryCurrentMonthPositive");
    let CanvasExpensesAmountPerCategoryLast12Months = document.getElementById("CanvasExpensesAmountPerCategoryLast12Months");
    let CanvasExpensesMonthlyBalanceLast12Months = document.getElementById("CanvasExpensesMonthlyBalanceLast12Months");

    //#region expensesAmountPerCategoryCurrentMonth
    //const categoryLabels = expensesAmountPerCategoryCurrentMonth.map(expense => expense.categoryTitle);
    //const expenseAmounts = expensesAmountPerCategoryCurrentMonth.map(expense => expense.totalAmount);

    var currentMonthPositiveAmountsExpenses = new Array();
    var currentMonthPositiveAmountsLabels = new Array();
    var currentMonthNegativeAmountsExpenses = new Array();
    var currentMonthNegativeAmountsLabels = new Array();

    // split expense amounts and category titles into positive and negative array
    expensesAmountPerCategoryCurrentMonth.forEach(item => {
        if (item.totalAmount >= 0) {
            currentMonthPositiveAmountsExpenses.push(item.totalAmount);
            currentMonthPositiveAmountsLabels.push(item.categoryTitle)
        }
        else {
            currentMonthNegativeAmountsExpenses.push(item.totalAmount);
            currentMonthNegativeAmountsLabels.push(item.categoryTitle)
        }
    });

    replaceNullValue(currentMonthPositiveAmountsLabels, "Sonstiges");
    replaceNullValue(currentMonthNegativeAmountsLabels, "Sonstiges");

    // add exactly as many background colors to the array as there are categories
    const backgroundColorsPositive = [];
    const backgroundColorsNegative = [];
    for (let i = 0; i < expensesAmountPerCategoryCurrentMonth.length; i++) {
        if (expensesAmountPerCategoryCurrentMonth[i].totalAmount >= 0)
            backgroundColorsPositive.push(colorForEachCategory[expensesAmountPerCategoryCurrentMonth[i].categoryTitle]);

        backgroundColorsNegative.push(colorForEachCategory[expensesAmountPerCategoryCurrentMonth[i].categoryTitle]);
    }

    let suggestedMaxPositive, suggestedMaxNegative, suggestedMinPositive, suggestedMinNegative;
    suggestedMaxPositive = Math.max(...currentMonthPositiveAmountsExpenses) * 1.1;
    suggestedMinPositive = Math.max(...currentMonthPositiveAmountsExpenses) * -0.1;

    suggestedMaxNegative = Math.min(...currentMonthNegativeAmountsExpenses) * -0.1;
    suggestedMinNegative = Math.min(...currentMonthNegativeAmountsExpenses) * 1.1;

    new Chart(CanvasExpensesAmountPerCategoryCurrentMonthNegative, {
        type: 'bar',
        data: {
            labels: currentMonthNegativeAmountsLabels,
            datasets: [{
                label: 'Ausgaben aktueller Monat, alle Konten',
                backgroundColor: backgroundColorsNegative,
                data: currentMonthNegativeAmountsExpenses,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    suggestedMax: suggestedMaxNegative,
                    suggestedMin: suggestedMinNegative,
                    reverse: true
                }
            }
        }
    });

    new Chart(CanvasExpensesAmountPerCategoryCurrentMonthPositive, {
        type: 'bar',
        data: {
            labels: currentMonthPositiveAmountsLabels,
            datasets: [{
                label: 'Einkünfte aktueller Monat, alle Konten',
                backgroundColor: backgroundColorsPositive,
                data: currentMonthPositiveAmountsExpenses,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    suggestedMax: suggestedMaxPositive,
                    suggestedMin: suggestedMinPositive
                }
            }
        }
    });
    //#endregion

    //#region expensesAmountPerCategoryPerMonthLast12Months

    // replace null category values
    for (let i = 0; i < expensesAmountPerCategoryPerMonthLast12Months.length; i++) {
        if (expensesAmountPerCategoryPerMonthLast12Months[i].categoryTitle == null)
            expensesAmountPerCategoryPerMonthLast12Months[i].categoryTitle = "Sonstiges"
    }

    // save as a set so that entries are unique and not duplicate
    let months = [...new Set(expensesAmountPerCategoryPerMonthLast12Months.map(expense => expense.month))];
    let categories = [...new Set(expensesAmountPerCategoryPerMonthLast12Months.map(expense => expense.categoryTitle))];
    let datasetData = {};

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
    let datasets = categories.map(category => ({
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

    new Chart(CanvasExpensesAmountPerCategoryLast12Months, {
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
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        },
    });
    //#endregion

    //#region expensesMonthlyBalanceLast12Months
    var labels = [];
    var positiveAmounts = [];
    var negativeAmounts = [];
    var totalAmounts = [];

    // traverse through each datapoint sent from the database and add to the corresponding datasets
    expensesMonthlyBalanceLast12Months.forEach(function (item) {
        labels.push(item.month);
        positiveAmounts.push(item.allPositiveAmounts);
        negativeAmounts.push(item.allNegativeAmounts);
        totalAmounts.push(item.balance);
    });

    new Chart(CanvasExpensesMonthlyBalanceLast12Months, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Positive Amounts',
                    data: positiveAmounts,
                    borderColor: '#2A982F',
                    backgroundColor: '#2A982F',
                    fill: false
                },
                {
                    label: 'Negative Amounts',
                    data: negativeAmounts,
                    borderColor: '#F5141C',
                    backgroundColor: '#F5141C',
                    fill: false
                },
                {
                    label: 'Total Amounts',
                    data: totalAmounts,
                    borderColor: 'black',
                    backgroundColor: 'black',
                    fill: false
                }
            ]
        },
        options: {
            elements: {
                line: {
                    tension: 0.2
                }
            },
        }
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
