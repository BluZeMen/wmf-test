/**
 * Created by bzm on 05.12.14.
 */


function sel(selector){
    switch(selector[0]){
        case '#':
            return document.getElementById(selector.slice(1));
        case '.':
            return document.getElementsByClassName(selector.slice(1));
        default:
            return document.getElementsByTagName(selector);
    }
}

function isValidEmail(emailString){
    var reg = /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/;
    return reg.test(emailString);
}