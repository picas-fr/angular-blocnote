
function objectToArray(obj)
{
    var objArray = [];
    for (var property in obj) {
        objArray.push(obj[property]);
    }
    return objArray;
}

function findNoteById(notes, id)
{
    if (typeof notes == 'object') {
        notes = objectToArray(notes);
    }
    var note = {};
    for (var index in notes) {
        if (notes[index]['id']==id) {
            note = notes[index];
        }
    }
    return note;
}

function findNoteIndexById(notes, id)
{
    if (typeof notes == 'object') {
        notes = objectToArray(notes);
    }
    var _index = null;
    for (i=0; i<notes.length; i++) {
        if (notes[i]['id']==id) {
            _index = i;
        }
    }
    return _index;
}

