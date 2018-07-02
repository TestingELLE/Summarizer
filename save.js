$(document).ready(function() {
    //save user changes on symbol page
    $("#save").on("click", function() {
        //escape string
        var re = /(?![\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})./g;
        $("#catalyst1").val($("#catalyst1").val().replace(/'/g, "\\'").replace(re, "").replace(/"/g,'\"'));
        $("#question1").val($("#question1").val().replace(/'/g, "\\'").replace(re, "").replace(/"/g,'\"'));
        $("#stra1").val($("#stra1").val().replace(/'/g, "\\'").replace(re, "").replace(/"/g,'\"'));
        $("#case1").val($("#case1").val().replace(/'/g, "\\'").replace(re, "").replace(/"/g,'\"'));
        $("#ticket1").val($("#ticket1").val().replace(/'/g, "\\'").replace(re, "").replace(/"/g,'\"'));
        $("#note1").html($("#note1").html().replace(/'/g, "\\'").replace(re, "").replace(/"/g,'\"'));
        // $("#comment1").html($("#comment1").html().replace(re, "").replace(/'/g, "\\'"))
        var divv = document.getElementById("note1");
        divv.innerHTML = divv.innerHTML.replace(/<div>/ig, "<br>").replace(/<\/div>/ig, "").replace(/"/g,'\"');
        // var divv2 = document.getElementById("comment1");
        // divv2.innerHTML = divv2.innerHTML.replace(/<div>/ig, "<br>").replace(/<\/div>/ig, "");
        var newData = {
            "symbol": $("#symbol").text().trim(),
            "price": $("#price").text().trim(),
            "intern": $("#intern").text().trim(),
            "LDate": $("#LDate").text().trim(),
            "mktCap": $("#mktCap").text().trim(),
            "next_earnings": $("#next_earnings").text().trim(),
            "PTarget": $("#PTarget").text().trim(),
            "LTarget": $("#LTarget").text().trim(),
            "industry": $("#industry").text().trim(),
            "upside": $("#upside").text().trim(),
            "secondPTarget":$("#2ndPTarget").text().trim(),
            "secondupside":$("#2ndupside").text().trim(),
            "down": $("#down").text().trim(),
            "PStock": $("#PStock").text().trim(),
            "biotech": $("#biotech").text().trim(),
            "status": $("#status").text().trim(),
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
            "last_price":$("#last_price").html().trim()
            // "comment": $("#comment1").html().trim()
        }

        $.ajax({
            url: "saveData.php",
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