$(document).ready(function () {
<<<<<<< HEAD
 $('#menu li:has("ul")').children('#drop-down').hide(); 

$('#menu li:has("ul")').click(function(){
    $(this).children('#drop-down').slideToggle();
});
});
function changeArrow(x) {
	console.log(1);
  x.classList.toggle("fa-arrow-left");
}
=======
 $('#menu li:has("ul")').children('ul').hide(); 

$('#menu li:has("ul")').click(function(){
    $(this).children('ul').slideToggle();
});
});
>>>>>>> 69fc9823e7e69d60726c68d05c155a4f5acdd910
