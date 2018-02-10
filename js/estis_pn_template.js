window.onload = function () {
    estisTemplateMainJs()
};


function estisTemplateMainJs() {
    var deleteAll = document.querySelector('#deleteAllTemplates');
    var deleteButton = document.querySelectorAll('.deleteThisTemplate');
    estisDelete(deleteButton);
    deleteAllConfirm(deleteAll);
    hideDeleteButton(deleteButton, deleteAll);
}

function estisDelete(deleteButton) {
    deleteButton.forEach(function (item) {
        item.onclick = function (e) {
            var conf = confirm("Do you want to delete this record?");
            if (!conf) {
                e.preventDefault();
            }
        }
    });
}


function hideDeleteButton(deleteButton, deleteAll) {
    if(deleteButton.length === 0){
        deleteAll.style.display = 'none';
    }
}

function deleteAllConfirm(deleteAll) {
    console.log(deleteAll);
    deleteAll.onclick = function (e) {
        var conf = confirm("Do you want to delete all templates?");
        if (!conf) {
            e.preventDefault();
        }
    }
}







