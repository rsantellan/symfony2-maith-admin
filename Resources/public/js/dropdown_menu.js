$(document).ready(function() {
    $('#navigation > li').bind('mouseover', openSubMenu);
    $('#navigation > li').bind('mouseout', closeSubMenu);

    function openSubMenu() {
        $(this).find('ul').css('visibility', 'visible');	
    };

    function closeSubMenu() {
        $(this).find('ul').css('visibility', 'hidden');	
    };

});

