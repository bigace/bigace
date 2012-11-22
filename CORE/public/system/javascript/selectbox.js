function remove(selectbox){
    if ( selectbox.length > 0 && selectbox.selectedIndex != -1 )
    {
        bla = selectbox.selectedIndex;
        selectbox.options[bla] = null;
        if (selectbox.options.length > bla)
            selectbox.selectedIndex = bla;
        else if (selectbox.options.length > bla -1)
            selectbox.selectedIndex = bla - 1;
    }
    else
    {
        alert(selectOptionMsg);
    }
}

function moveUp(selectbox){
    if ( selectbox.length > 1 && selectbox.selectedIndex > 0 ){
        storedIndex = selectbox.selectedIndex;
        itemToBeMoved = new Option(selectbox.options[storedIndex].text, selectbox.options[storedIndex].value);
        itemToBeSwitched = new Option(selectbox.options[storedIndex-1].text, selectbox.options[storedIndex-1].value);
        selectbox.options[storedIndex-1] = itemToBeMoved;
        selectbox.options[storedIndex] = itemToBeSwitched;
        selectbox.selectedIndex = storedIndex-1;
    }
}

function moveDown(selectbox){
    if ( selectbox.length > 1 && selectbox.selectedIndex > -1 && selectbox.selectedIndex < selectbox.length-1 ){
        storedIndex = selectbox.selectedIndex;
        itemToBeMoved = new Option(selectbox.options[storedIndex].text, selectbox.options[storedIndex].value);
        itemToBeSwitched = new Option(selectbox.options[storedIndex+1].text, selectbox.options[storedIndex+1].value);
        selectbox.options[storedIndex+1] = itemToBeMoved;
        selectbox.options[storedIndex] = itemToBeSwitched;
        selectbox.selectedIndex = storedIndex+1;
    }
}

function moveToTop(selectbox) {
    if ( selectbox.length > 1 && selectbox.selectedIndex > 0 ){
        storedIndex = selectbox.selectedIndex;
        itemToTop = new Option(selectbox.options[storedIndex].text, selectbox.options[storedIndex].value);
        for(i=storedIndex-1; i > -1; i--) {
            selectbox.options[i+1] = new Option(selectbox.options[i].text, selectbox.options[i].value);
        }
        selectbox.options[0] = itemToTop;
        selectbox.selectedIndex = 0;
    }
}

function moveToBottom(selectbox) {
    if ( selectbox.length > 1 && selectbox.selectedIndex < selectbox.length-1 ){
        storedIndex = selectbox.selectedIndex;
        itemToBottom = new Option(selectbox.options[storedIndex].text, selectbox.options[storedIndex].value);
        for(i=storedIndex+1; i < selectbox.length; i++) {
            selectbox.options[i-1] = new Option(selectbox.options[i].text, selectbox.options[i].value);
        }
        selectbox.options[selectbox.length-1] = itemToBottom;
        selectbox.selectedIndex = selectbox.length-1;
    }
}

function selectAll(selectbox) {

    if ( selectbox.length > 0 ){
        if( !selectbox.multiple ) {
            selectbox.multiple = true;
        }

        for(i=0; i < selectbox.length; i++) {
            selectbox.options[i].selected = true;
        }

        // now walk above all and check again
        for(i=0; i < selectbox.length; i++) {
            if(!selectbox.options[i].selected)
                return false;
        }
    }
    return true;
}
