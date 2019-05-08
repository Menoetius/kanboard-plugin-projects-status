/*
    Prevzaté z: https://codepen.io/malahovks/pen/gLxLWX
    License:

Copyright (c) 2019 by Kirill Malakhov (https://codepen.io/malahovks/pen/gLxLWX)

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

    Kód bol prevzatý a upravený
*/
function download_file(table, filename, isCSV) {
    let file;
    let downloadLink;

    if(isCSV) {
        file = new Blob([table], {type: "text/csv"});
    } else {
        file = new Blob([table], {type: "application/vnd.ms-excel"});
    }
    downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(file);
    downloadLink.style.display = "none";
    document.body.appendChild(downloadLink);
    downloadLink.click();
}

function export_table_to_csv(html, filename) {
    let csv = [];
    let rows = document.querySelectorAll("table tr");

    for (let i = 0; i < rows.length; i++) {
        let row = [], cols = rows[i].querySelectorAll("td, th");

        for (let j = 0; j < cols.length; j++)
            row.push(cols[j].innerText);

        csv.push(row.join(";;"));
    }
    download_file(csv.join("\n"), filename, true);
}

$("button.exportCSV").click(function () {
    let html = document.querySelector("table").outerHTML;
    let date = new Date().toISOString().split('T')[0];
    export_table_to_csv(html, "projectsReport" +  date + ".csv");
});

$("button.exportHTML").click(function () {
    let html = document.querySelector("table").outerHTML;
    let date = new Date().toISOString().split('T')[0];
    download_file(html, "projectsReport" +  date + ".xls", false);
});