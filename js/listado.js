$(function(){
    // Variant 1:
    $("span.dynatree-edit-icon").live("click", function(e){
        alert("Edit " + $.ui.dynatree.getNode(e.target));
    });
    $("#tree").dynatree({
        onActivate: function(node) {
        //        $("#info").text("You activated " + node);
    },
    onRender: function(node, nodeSpan) {
    $(nodeSpan).find('.dynatree-icon')
        .before('<span></span>');
    },
    // Variant 2:
    onClick: function(node, e){
        if($(e.target).hasClass("dynatree-edit-icon")){
            $("#info").text("You clicked " + node + ",  url=" + node.url);
        }
    },
    children: [
        {title: "Item 1"},
        {title: "Folder 2", isFolder: true,
        children: [
            {title: "Sub-item 2.1"},
            {title: "Sub-item 2.2"}
            ]
        },
        {title: "Item 3"}
        ]
    });
});