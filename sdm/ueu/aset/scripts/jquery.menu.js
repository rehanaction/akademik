var mt; // menu timeout

$(document).ready(function() {
    $("ul.subnav").prev("a").append("<span></span>");
    $("ul.leafnav").prev("a").append("<span></span>");
    
    $("ul.topnav > li > a").click(function() {
        clearTimeout(mt);
        
        // cek subnavnya
        subnav = $(this).parent().find("ul.subnav");
        if(subnav.is(":visible"))
            return false;
        
        hideSubNav();
        subnav.slideDown('fast');
        
        return true;
    });
    
    $("ul.subnav > li > a").click(function() {
        clearTimeout(mt);
        
        hideLeafNav();
        $(this).parent().find("ul.leafnav").slideDown('fast');
    });
    
    $("ul.subnav").hover(function() {
        clearTimeout(mt);
    }, function() {  
        mt = setTimeout('hideSubNav()',1000);
    });
});

function hideSubNav() {
    $("ul.subnav:visible,ul.leafnav:visible").slideUp('fast');
}

function hideLeafNav() {
    $("ul.leafnav:visible").slideUp('fast');
}