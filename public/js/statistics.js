$(document).ready(function () {
    let CanvasExpensesAmountPerCategoryCurrentMonth = document.getElementById("CanvasExpensesAmountPerCategoryCurrentMonth");
    let CanvasExpensesAmountPerCategoryCurrentMonth2 = document.getElementById("CanvasExpensesAmountPerCategoryCurrentMonth2");

    const categoryLabels = expensesAmountPerCategoryCurrentMonth.map(expense => expense.categoryTitle);
    const expenseAmounts = expensesAmountPerCategoryCurrentMonth.map(expense => expense.totalAmount);

    replaceNullValue(categoryLabels, "Sonstiges");

    // add exactly as many background colors to the array as there are categories
    const backgroundColors = [];
    for (let i = 0; i < categoryLabels.length; i++) {
        backgroundColors.push(getRandomHexColor());
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
                    suggestedMin: suggestedMin
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
        let color = getRandomHexColor();
        datasets[i].borderColor = color;
        datasets[i].backgroundColor = color;
    }

    console.log(datasets);

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


function getRandomHexColor() {
    // 20 random color codes
    const colors = [
        "#317F43", "#C6A664", "#1D1E33", "#3D642D", "#2271B3",
        "#015D52", "#F75E25", "#E6D690", "#CBD0CC", "#A03472",
        "#57A639", "#EC7C26", "#7E7B52", "#FAD201", "#E1CC4F",
        "#6F4F28", "#FE0000", "#826C34", "#2C5545", "#F8F32B",
        "#9E9764", "#705335", "#9C9C9C", "#84C3BE", "#F8F32B",
        "#592321", "#B5B8B1", "#8F8B66", "#E1CC4F", "#FE0000",
        "#642424", "#6C6874", "#008F39", "#35682D", "#497E76",
        "#1D1E33", "#E7EBDA", "#6C6960", "#75151E", "#B5B8B1",
        "#3E3B32", "#4E5754", "#C51D34", "#B5B8B1", "#6C3B2A",
        "#382C1E", "#3B83BD", "#282828", "#26252D", "#924E7D"
    ];

    return colors[Math.floor(Math.random() * 19)];
}

function replaceNullValue(data, replacement) {
    // replaces null value for example with "Sonstiges"
    for (let i = 0; i < data.length; i++) {
        if (data[i] == null) {
            data[i] = replacement;
        }
    }
}
