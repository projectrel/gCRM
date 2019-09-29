$(document).ready(function () {
$('#menu .drop-list:has("#drop-down")').children('#drop-down').hide(); 

$('#menu .drop-list:has("#drop-down")').click(function(){
    $(this).children('#drop-down').slideToggle();
    $(this).find('#arrow').toggleClass('fa-arrow-down fa-arrow-up')
});

});


