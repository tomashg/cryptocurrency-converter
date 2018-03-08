$( document ).ready(function() {
    
    $('select').change(function() {
    var myOpt = [];
    $("select").each(function () {
        myOpt.push($(this).val());
    });
    $("select").each(function () {
        $(this).find("option").prop('hidden', false);
        var sel = $(this);
        $.each(myOpt, function(key, value) {
            if((value != "") && (value != sel.val())) {
                sel.find("option").filter('[value="' + value +'"]').prop('hidden', true);
            }
        });
    });
    });
    
    $('form').on('change keyup paste', function (){
		var data = $("form :input[value!='']").serializeArray();

		if(data.length == 3){
            console.log('aaa');
		    $.ajax({
				type: 'POST',
				url: "converter.php",
				dataType: 'json',
				data: $.param(data),

                success: function (value) {

                    if (value.error) {
                        $("#score").html( value.error.msg ).attr( "class", 'alert alert-info');
                    } else {
                        $("#score").html(data[0].value + " " + data[1].value + " = " + value + " " + data[2].value).attr( "class", 'alert alert-success');
                    }

                }


            });
		}
    });


});

