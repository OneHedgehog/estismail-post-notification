'use strict';

$(document).ready(function () {
    estisSendMailPageJs();
    //DatePicker;
    $('#datetimepicker').datetimepicker({
        format: 'd.m.Y H:i'
    });

    //MultiSelect
    var Selects = $('.estisMultiSelect');

    var Select = Selects[0];
    $(Select).multiSelect();

    $('#select-all').click(function () {
        $(Select).multiSelect('select_all');
        return false;
    });

    $('#deselect-all').click(function () {
        $(Select).multiSelect('deselect_all');
        return false;
    });


    var Select1 = Selects[1];
    $(Select1).multiSelect();

    $('#select-all-ex').click(function () {
        $(Select1).multiSelect('select_all');
        return false;
    });

    $('#deselect-all-ex').click(function () {
        $(Select1).multiSelect('deselect_all');
        return false;
    });


});

function estisSendMailPageJs() {

    checkReqValues();

    function checkReqValues() {
        var Form = $('#estisPnSendMailsForm');
        var valid = false;
        console.log(Form.serializeArray());

        Form.submit(function () {
            var Arr = Form.serializeArray();
            //don't check date, cause it have auto value.
            Arr.splice(Arr.indexOf(Arr.length - 1), 1);
            for (var nameObj in Arr) {
                if ((Arr[nameObj].value) === "") {
                    alert('please, fill this filed ' + Arr[nameObj].name);
                    return false;
                }

                if ((Arr[nameObj].name === 'estis_included_list[]' )) {
                    valid = true;

                }

            }
            if (!valid) {
                alert('Empty lists');
                return false;
            }

        })
    }
}

