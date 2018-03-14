$(document).ready(function() {

    $("#save").on("click", function() {

        var divv = document.getElementById("note1");
        divv.innerHTML = divv.innerHTML.replace(/<div>/ig, "<br>").replace(/<\/div>/ig, "");
        var divv2 = document.getElementById("comment1");
        divv2.innerHTML = divv2.innerHTML.replace(/<div>/ig, "<br>").replace(/<\/div>/ig, "");


        var newData = {
            "symbol": $("#mysymbol").text().trim(),
            "price": $("#price").text().trim(),
            "intern": $("#intern").text().trim(),
            "LDate": $("#LDate").text().trim(),
            "mktCap": $("#mktCap").text().trim(),
            "NDate": $("#NDate").text().trim(),
            "PTarget": $("#PTarget").text().trim(),
            "LTarget": $("#LTarget").text().trim(),
            "industry": $("#industry").text().trim(),
            "upside": $("#upside").text().trim(),
            "down": $("#down").text().trim(),
            "PStock": $("#PStock").text().trim(),
            "biotech": $("#biotech").text().trim(),
            "active": $("#active").text().trim(),
            "LUpdate": $("#LUpdate").text().trim(),
            "rank": $("#rank").text().trim(),
            "AnalysisDate": $("#AnalysisDate").text().trim(),
            "confidence": $("#confidence").text().trim(),
            "Tweight": $("#Tweight").text().trim(),
            "Tposition": $("#Tposition").text().trim(),
            "analysisPrice": $("#analysisPrice").text().trim(),
            "cash": $("#cash").text().trim(),
            "actualWeight": $("#actualWeight").text().trim(),
            "actualPosition": $("#actualPosition").text().trim(),
            "burn": $("#burn").text().trim(),
            "diff": $("#diff").text().trim(),
            "boah": $("#boah").text().trim(),
            "question": $("#question1").val().trim(),
            "catalyst": $("#catalyst1").val().trim(),
            "strategy": $("#stra1").val().trim(),
            "case": $("#case1").val().trim(),
            "ticket": $("#ticket1").val().trim(),
            "note": $("#note1").html().trim(),
            "comment": $("#comment1").html().trim()
        }

        $.ajax({
            url: "savaData.php",
            type: "POST",
            data: newData,
        }).done(function(res) {
            alert(JSON.stringify(res));

            if (JSON.stringify(res) === '"Successfully updated"') {

                window.location.reload()
            }
        })
    })

})