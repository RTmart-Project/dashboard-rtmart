function cloneElement(original, appendTo) {
    //you can use :
    var $orginal = $(original);
    var $cloned = $orginal.clone().find("input, select").val("").end();

    //then use this to solve duplication problem
    $cloned.find(".bootstrap-select").replaceWith(function () {
        return $("select", this);
    });
    $cloned.find(".selectpicker").selectpicker("render");

    //Then Append
    $cloned.appendTo(appendTo);
}
