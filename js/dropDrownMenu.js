$(document).ready(function () {

$('#menu li:has("ul")').children('#drop-down').hide(); 

$('#menu li:has("ul")').click(function(){
    $(this).children('#drop-down').slideToggle();
});
});



