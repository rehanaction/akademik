function expandItem(elem) {
    var arrid = elem.id.split("_");
    
    // cek tr anak yang expand
    var exp, expid;
    $("[id$='parent_"+arrid[1]+"']").each(function() {
        exp = $(this).find("[id$='_collapse']").not(".Hidden");
        if(exp.length > 0) {
            expid = exp.attr("id").split("_");
            $("[id$='parent_"+expid[1]+"']").removeClass("Hidden");
        }
        
        $(this).removeClass("Hidden");
    });
    
    switchToggle(elem,arrid[0]+"_"+arrid[1]);
}

function collapseItem(elem) {
    var arrid = elem.id.split("_");
    
    // cek tr anak yang expand
    var exp, expid;
    $("[id$='parent_"+arrid[1]+"']").each(function() {
        exp = $(this).find("[id$='_collapse']").not(".Hidden");;
        if(exp.length > 0) {
            expid = exp.attr("id").split("_");
            $("[id$='parent_"+expid[1]+"']").addClass("Hidden");
        }
        
        $(this).addClass("Hidden");
    });
    
    switchToggle(elem,arrid[0]+"_"+arrid[1]);
}

function switchToggle(elem,groupid) {
    var arrid = elem.id.split("_");
     
    $("[id^='"+groupid+"_']").removeClass("Hidden");
    $(elem).addClass("Hidden");
}