
function tabular(e,obj)
{
    tecla=(document.all) ? e.keyCode : e.which;
    if(tecla!=13) return;
    frm=obj.form;
    for(i=0;i<frm.elements.length;i++)
        if(frm.elements[i]==obj)
        {
            if (i==frm.elements.length-1)
                i=-1;
            break
        }
    /*ACA ESTA EL CAMBIO disabled, Y PARA SALTEAR CAMPOS HIDDEN*/
    if ((frm.elements[i+1].disabled ==true) || (frm.elements[i+1].type=='hidden') )
        tabular(e,frm.elements[i+1]);
    /*ACA ESTA EL CAMBIO readOnly */
    else if (frm.elements[i+1].readOnly ==true )
        tabular(e,frm.elements[i+1]);
    else {
        if (frm.elements[i+1].type=='text') /*VALIDA SI EL CAMPO ES TEXTO*/
        {
            frm.elements[i+1].select();
        };   /* A�ADIR LOS CORCHETES Y ESTA INSTRUCCION */
        frm.elements[i+1].focus();
    }
    return false;
}

function forceFloat(number) {
    if(number === null || isNaN(number))
        number = 0;

    return parseFloat(number);
}

$('[data-toggle="popover"]').popover({
    trigger: 'hover',
    placement: 'top',
    container: 'body'
});

function showPopover(element, content, title, placement, timeout) {
    title = typeof title !== 'undefined' ?  title : '';
    placement = typeof placement !== 'undefined' ?  placement : 'top';
    timeout = typeof timeout !== 'undefined' ?  timeout : 4000;
    
    element.popover({content: content, title: title, placement: placement});
    element.popover("show");
    setTimeout(function () {
        element.popover("hide");
        element.popover("destroy");
    }, timeout);
}