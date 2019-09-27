$(document).ready(function () {
 $('#menu li:has("ul")').children('ul').hide(); 

$('#menu li:has("ul")').click(function(){
    $(this).children('ul').slideToggle();
});
});