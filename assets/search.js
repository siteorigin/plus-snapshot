jQuery(function($){
    // Add the search if we need it
    $('#menu-main-menu-container > .menu > ul').each(function(){
        $(this).append($('<li id="main-menu-search"><a href="#">'+snapshotSearch.menuText+'</a></li>'));
    });
    
    // Display the search
    $('#main-menu-search a').click(function(){
        $('#hidden-search').slideToggle(250);
        if($('#hidden-search').is(':visible')){
            $('#hidden-search #s').focus();
        }
        else{
            $('#hidden-search #s').blur();
        }
        return false;
    });
    if($('body').hasClass('search')) {
        $('#hidden-search').show();
    }
});