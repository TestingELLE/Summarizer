$(document).on("keypress", "td", function(e) {
    return e.which != 13;
});

//hide or display question, worst case, catalyst,strategy
$(document).on("click", "#question", function() {
    if ($("#question1").is(":hidden")) {
        $("#first").attr("class", "glyphicon glyphicon-menu-down")

        $("#question1").show();
        $("#question1").slideDown("slow");
    } else {
        $("#question1").hide();
        $("#first").attr("class", "glyphicon glyphicon-menu-right")


    }
});
$(document).on("click", "#case", function() {
    if ($("#case1").is(":hidden")) {
        $("#second").attr("class", "glyphicon glyphicon-menu-down");
        $("#case1").show();
        $("#case1").slideDown("slow");
    } else {
        $("#second").attr("class", "glyphicon glyphicon-menu-right")
        $("#case1").hide();
    }
});
$(document).on("click", "#catalyst", function() {
    if ($("#catalyst1").is(":hidden")) {
        $("#third").attr("class", "glyphicon glyphicon-menu-down");
        $("#catalyst1").show();
        $("#catalyst1").slideDown("slow");
    } else {
        $("#third").attr("class", "glyphicon glyphicon-menu-right")
        $("#catalyst1").hide();
    }
});
$(document).on("click", "#ticket", function() {
    if ($("#ticket1").is(":hidden")) {
        $("#forth").attr("class", "glyphicon glyphicon-menu-down")
        $("#ticket1").show();
        $("#ticket1").slideDown("slow");
    } else {
        $("#forth").attr("class", "glyphicon glyphicon-menu-right")
        $("#ticket1").hide();
    }
});
$(document).on("click", "#stra", function() {
    if ($("#stra1").is(":hidden")) {
        $("#fifth").attr("class", "glyphicon glyphicon-menu-down")
        $("#stra1").show();
        $("#stra1").slideDown("slow");
    } else {
        $("#fifth").attr("class", "glyphicon glyphicon-menu-right")
        $("#stra1").hide();

    }
});
$(document).on("click", "#btn1", function() {
    if ($("#question").is(":hidden")) {
        $("#first").attr("class", "glyphicon glyphicon-menu-down")
        $("#question").show();
        $("#first1").attr("class", "glyphicon glyphicon-menu-down")
        $("#question1").show();
    } else {
        $("#question1").hide();
        $("#first").attr("class", "glyphicon glyphicon-menu-right")
        $("#first1").attr("class", "glyphicon glyphicon-menu-right")
        $("#question").hide()

    }
})
$(document).on("click", "#btn2", function() {
    if ($("#case").is(":hidden")) {
        $("#second").attr("class", "glyphicon glyphicon-menu-down")
        $("#second1").attr("class", "glyphicon glyphicon-menu-down")
        $("#case").show();
        $("#case1").show();
    } else {
        $("#case1").hide();
        $("#second").attr("class", "glyphicon glyphicon-menu-right")
        $("#second1").attr("class", "glyphicon glyphicon-menu-right")
        $("#case").hide()

    }
})
$(document).on("click", "#btn3", function() {
    if ($("#catalyst").is(":hidden")) {
        $("#third").attr("class", "glyphicon glyphicon-menu-down")
        $("#third1").attr("class", "glyphicon glyphicon-menu-down")
        $("#catalyst").show();
        $("#catalyst1").show();
    } else {
        $("#catalyst1").hide();
        $("#third").attr("class", "glyphicon glyphicon-menu-right")
        $("#third1").attr("class", "glyphicon glyphicon-menu-right")
        $("#catalyst").hide()

    }
})
$(document).on("click", "#btn4", function() {
    if ($("#ticket").is(":hidden")) {
        $("#forth").attr("class", "glyphicon glyphicon-menu-down")
        $("#forth1").attr("class", "glyphicon glyphicon-menu-down")
        $("#ticket").show();
        $("#ticket1").show();
    } else {
        $("#ticket1").hide();
        $("#forth").attr("class", "glyphicon glyphicon-menu-right")
        $("#forth1").attr("class", "glyphicon glyphicon-menu-right")
        $("#ticket").hide()

    }
})
$(document).on("click", "#btn5", function() {
        if ($("#stra").is(":hidden")) {
            $("#fifth").attr("class", "glyphicon glyphicon-menu-down")
            $("#fifth1").attr("class", "glyphicon glyphicon-menu-down")
            $("#stra").show();
            $("#stra1").show();
        } else {
            $("#stra1").hide();
            $("#fifth").attr("class", "glyphicon glyphicon-menu-right")
            $("#fifth1").attr("class", "glyphicon glyphicon-menu-right")
            $("#stra").hide()

        }
    })
    // $(document).on("click","#btn1",function(){

//   $("#question1").show();
//   $("#question").show();
//   $("#first").attr("class","glyphicon glyphicon-menu-down")

// });
function Search() {
    window.location.href = "Summarizer.php";
};