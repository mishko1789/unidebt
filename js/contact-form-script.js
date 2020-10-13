$("#contactForm").validator().on("submit", function (event) {
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        formError();
        submitMSG(false, "Zkontrolujte si formulář, není správně vyplněn.");
    } else {
        // everything looks good!
        event.preventDefault();
        submitForm();
    }
});


function submitForm(){
    // Initiate Variables With Form Content
    var name = $("#name").val();
    
    var email = $("#email").val();
    var phone = $("#phone").val();
    var option = $('input[name="inlineRadioOptions"]:checked').val();
    var adress = $("#adress").val();
    var type = $("input[name=type]").val();

    var consent1 = $("#form_checkbox").is(":checked");
    var consent2 = $("#form_checkbox_SE").is(":checked");

    var USOURCE = $("input[name='USOURCE']").val();
    var UMEDIUM = $("input[name='UMEDIUM']").val();
    var UCAMPAIGN = $("input[name='UCAMPAIGN']").val();
    var UCONTENT = $("input[name='UCONTENT']").val();
    var UTERM = $("input[name='UTERM']").val();

    // var msg_subject = $("#msg_subject").val();
    // var message = $("#message").val();


    $.ajax({
        type: "POST",
        url: "/form-process.php",
        // data: "name=" + name + "&email=" + email + "&msg_subject=" + msg_subject + "&message=" + message,
        data: {
            type: type,
            name: name,
            email: email,
            phone: phone,
            option: option,
            adress: adress,
            consent1: consent1,
            consent2: consent2,
            USOURCE: USOURCE,
            UMEDIUM: UMEDIUM,
            UCAMPAIGN: UCAMPAIGN,
            UCONTENT: UCONTENT,
            UTERM: UTERM
        },
        success : function(text){
            if (text == "success"){
                formSuccess();
            } else {
                formError();
                submitMSG(false,text);
            }
        }
    });
}

function formSuccess(){
    $("#contactForm")[0].reset();
    submitMSG(true, "Úspěšně odesláno! Děkujeme, brzy se vám ozveme.")
}

function formError(){
    $("#contactForm").removeClass().addClass('shake animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        $(this).removeClass();
    });
}

function submitMSG(valid, msg){
    if(valid){
        var msgClasses = "h3 text-center tada animated text-success";
    } else {
        var msgClasses = "h3 text-center text-danger";
    }
    $("#msgSubmit").removeClass().addClass(msgClasses).text(msg);
}
