var tags = {
    "1": ["<h1>", "</h1>"],
    "2": ["<h2>", "</h2>"],
    "3": ["<h3>", "</h3>"],
    "4": ["<h4>", "</h4>"],
    "5": ["<h5>", "</h5>"],
    "6": ["<h6>", "</h6>"],
    ">": ["<a>", "</a>"],
    "nav": ["<div id=\"nav\" class=\"nav\">", "</div>"],
    "section": ["<div id=\"section\" class=\"section\">", "</div>"],
    "footer": ["<div id=\"footer\" class=\"footer\">", "</div>"],
    "div": ["<div class=\"text\">", "</div>"],
    "img": ["<div class=\"image\">", "</div>"],
    "button": ["<button class=\"button\">", "</button>"],
    "ul": ["<ul>", "</ul>"],
    "ol": ["<ol>", "</ol>"],
    "li": ["<li>", "</li>"],
    "uncheck": ["<input type=\"checkbox\">", ""],
    "checked": ["<input type=\"checkbox\" checked>", ""],
    "radio": ["<input type=\"radio\">", ""],
    "selected radio": ["<input type=\"radio\" checked>", ""],
    "progress": ["<progress value=\"0\" max=\"100\">", "</progress>"],
    "hr": ["<hr/>", ""],
    "form": ["<form>", "</form>"],
    "span": ["<span>", "</span>"],
    "lbl": ["<label>", "</label>"],
    "input": ["<div>", "</div>"],
    "video": ["<video>", "</video>"],
};
var vueTags = {
    "checkbox": ["<v-checkbox>", "</v-checkbox>"],
    "radio": ["<v-radio>", "</v-radio>"],
    "progress": ["<v-progress-linear>", "</v-progress-linear>"],
    "button": ["<v-btn class=\"button\">", "</v-btn>"],
    "nav": ["<v-toolbar class=\"nav\">", "</v-toolbar>"],
    "footer": ["<v-footer class=\"footer\">", "</v-footer>"],
    "row": ["<v-row>", "</v-row>"],
    "img": ["<v-img>", "</v-img>"],
    "table": ["<v-data-table>", "</v-data-table>"],
    "template": ["<template>", "</template>"],
}

var mailIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
</svg>`;
var phoneIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
</svg>`;
var imageIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-placeholder" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
</svg>`;
var videoIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-placeholder" fill="none" viewBox="0 0 24 24" stroke="currentColor">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>`;

var loremShort = "Lorem ipsum dolor sit";

var loremLong = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.";

var html = "";

var closeTags = [];

var output = [];

var menus = [];

var genCode = "html";

// to generate html code
document.getElementById("submit").addEventListener("click", (e) => {
    var radios = document.getElementsByName('code');

    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            genCode = radios[i].getAttribute("value");

            // only one radio can be logically checked, don't check the rest
            break;
        }
    }

    show(genCode);
    e.preventDefault();
}, false);

// to generate vue code
// document.getElementById("vue-code").addEventListener("click", (e) => {
//     show("vue");
//     e.preventDefault();
// }, false);

// allow "tab" in textarea
document.getElementById('form-input').addEventListener('keydown', function (e) {
    if (e.key == 'Tab') {
        e.preventDefault();
        var start = this.selectionStart;
        var end = this.selectionEnd;

        // set textarea value to: text before caret + tab + text after caret
        this.value = this.value.substring(0, start) + "\t" + this.value.substring(end);

        // put caret at right position again
        this.selectionStart = this.selectionEnd = start + 1;
    }
});

if(document.getElementById("form-input").value==="") { 
    document.getElementById('submit').disabled = true; 
}
document.getElementById('form-input').addEventListener('keyup', (e) => {
    if(document.getElementById("form-input").value==="") { 
        document.getElementById('submit').disabled = true; 
    } else { 
        document.getElementById('submit').disabled = false;
    }
    e.preventDefault();
});

showHideModal("cdn-modal", "add-cdn");

let styleTag = "";

function show(type) {

    var input = [];

    var newarr = [];

    html = "";

    output = [];

    closeTags = [];

    input = splitEnter(document.getElementById("form-input").value); // look for return or new line

    let newIndex = 0;

    menus = [];

    for (let a = 0; a < input.length; a++) {
        if (isUnorderedList(input[a])) {
            let beforeList = returnIndentOnly(input[a]) + "ul";
            let listItem = returnIndentOnly(input[a]) + "\tli-" + removeListDel(input[a]);
            if (!isUnorderedList(input[a - 1]) || a == 0 || countIndent(input[a - 1]) != countIndent(input[a])) {
                newarr[newIndex] = beforeList;
                newIndex++
                newarr.push(listItem);
                newIndex++
            }
            else {
                newarr.push(listItem);
                newIndex++
            }
        }

        else if (isOrderedList(input[a])) {
            let beforeList = returnIndentOnly(input[a]) + "ol";
            let listItem = returnIndentOnly(input[a]) + "\tli-" + removeListDel(input[a]);
            if (!isOrderedList(input[a - 1]) || a == 0 || countIndent(input[a - 1]) != countIndent(input[a])) {
                newarr[newIndex] = beforeList;
                newIndex++
                newarr.push(listItem);
                newIndex++
            }
            else {
                newarr.push(listItem);
                newIndex++
            }
        }

        else if (/->/.test(input[a])) { //if it is nested
            var parent = input[a].split("->");
            newarr[newIndex] = parent[0];
            newIndex++;

            var indentOnly = "";

            if (countIndent(parent[0]) > 0) { // if the entry has indent
                indentOnly = returnIndentOnly(parent[0]); // getting all indents before characters
                parent[0] = parent[0].split(/^[\t]*/g)[1]; // getting all the text after indents
            }

            if (parent[0] == "i-n" || parent[0] == "nav" || parent[0] == "navigation") { // function that will set all the child element a link if the parent element is nav. 

                if (/&/.test(parent[1])) { // if there's a comma it means it has more than one child
                    var child = parent[1].split("&");

                    for (var d = 0; d <= child.length - 1; d++) {
                        if (/>/.test(child[d])) {
                            newarr[newIndex] = indentOnly + "\t" + child[d];
                            newIndex++;
                        } else {
                            newarr[newIndex] = indentOnly + "\t" + child[d] + ">..";
                            newIndex++;
                        }
                    }
                }
                else if (parent[1]) {

                    if (/>/.test(parent[1])) {
                        newarr[newIndex] = indentOnly + "\t" + parent[1];
                        newIndex++;
                    } else {
                        newarr[newIndex] = indentOnly + "\t" + parent[1] + ">..";
                        newIndex++;
                    }
                }
            }

            else if (/&/.test(parent[1])) {  // if there's a comma it means it has more than one child
                var child = parent[1].split("&");

                for (var d = 0; d <= child.length - 1; d++) {
                    newarr[newIndex] = indentOnly + "\t" + child[d];
                    newIndex++;
                }
            }
            else if (parent[1]) {
                newarr[newIndex] = indentOnly + "\t" + parent[1];
                newIndex++;
            }
        }

        else {
            newarr[newIndex] = input[a];
            newIndex++
        }
    }

    // converting newarr to associative array
    for (let i = 0; i < newarr.length; i++) {
        let parentsIndent = countIndent(newarr[0]);

        if (isMainParent(newarr[i])) {
            let multi = countMulti(newarr[i]); // getting the parent's multiplier

            menus.push(newarr[i]);

            let indent0 = {
                element: mainParent(newarr[i]),
                indent: countIndent(newarr[i]),
                multi: multi,
                child: []
            }
            indent0.child = getChildren(newarr, i, 0)
            output.push(indent0)
        }

    }

    // converting the associative array to html
    for (let j = 0; j < output.length; j++) {
        for (let k = 0; k < output[j].multi; k++) { // display the element a multiple times if the parent has multi

            html += convertToHTML(output[j].element, type);
            displayChildren(output[j], type);
        }
    }

    // PREVIEW OUTPUT IN HTML
    let previewHTML = document.querySelector('#output');

    let previewPane = document.querySelector('iframe');

    previewPane.src = "data:text/html;charset=utf-8," + encodeURI(previewHTML.textContent);

    // ADD MULTIPLE CSS
    for (let index = 0; index < cssFiles.length; index++){
        styleTag += "<style data-id=\"" + cssFiles[index].filename + "\">" + cssFiles[index].fileContent + "</style>";
    }

    document.getElementById("output").innerHTML = html; // Display the result
}

// displaying children
function displayChildren(parent, type) {
    if (parent.child.length != 0) {
        for (var j = 0; j < parent.child.length; j++) {
            for (let k = 0; k < parent.child[j].multi; k++) {
                html += convertToHTML(parent.child[j].element, type);
                displayChildren(parent.child[j], type);
            }

            // add closing tag after displaying all the child
            if (j == parent.child.length - 1) {
                html += closeTags[closeTags.length - 1];
                closeTags.pop();
            }
        }
    } else {
        //add closing tag if there's no child
        html += closeTags[closeTags.length - 1];
        closeTags.pop();
    }
}

// getting the children in deeper
function getChildren(array, index, indent) {
    let children = [];
    for (let i = index + 1; i < array.length; i++) {
        let parentCount = countIndent(array[index]);
        let nextCount = countIndent(array[i]);
        let multi = countMulti(array[i]);

        if (parentCount < nextCount && (nextCount - parentCount) === 1) {
            children.push({
                element: cleanString(array[i]),
                indent: indent + 1,
                multi: multi,
                child: getChildren(array, i, indent + 1)
            });
        } else if ((nextCount - parentCount) > 1) {
            continue;
        } else {
            break;
        }
    }
    return children;
}

function isMainParent(item) {
    if (/^\t/.test(item)) {
        return false;
    }
    else
        return true;
}

// Count the indents
function countIndent(item) {
    if (/^\t/.test(item)) {
        var indent = item.match(/^[\t]*/g); // getting all indents before characters
        return indent[0].match(/\t/g).length; // count indents
    }
    else
        return 0;
}

function returnIndentOnly(item) {
    return item.match(/^[\t]*/g);
}

function cleanString(item) {
    item = removeIndent(item);
    item = removeMulti(item);
    return item;
}

function mainParent(item) {
    let str = cleanString(item);

    if (str.match(/^\w+\./)) {
        str = str.replace(/^\w+\./, "");
        return "page." + str;
    }
    else return "page." + str;
}

function removeListDel(item) {
    if (isUnorderedList(item))
        return item.replace(/^\t+\-/, "");
    else if (isOrderedList(item))
        return item.replace(/^\t+\w+\./, "");
    else
        return item;
}

function removeMulti(item) {
    if (/\*\d+$/.test(item))
        return item.replace(/\*\d$/, "");
    else
        return item;
}

// Remove the indent from char
function removeIndent(item) {
    if (/^\t/.test(item))
        return item.replace(/^[\t]*/, ""); // getting all the text after indents
    else
        return item;
}

// Check if it is Ordered list
// if there's any number with "."
// Input: 1.Step1
// Output: <ol><li>Step1</li></ol>
function isOrderedList(item) { 
    if(/^\t+h[1-6]\./.test(item)){
        return false;
    }
    else return /^\t+\w+\./.test(item);
}

// Check if it is Unordered list
// if there's "-" 
// Input: -Step1
// Output: <ul><li>Step1</li></ul>
function isUnorderedList(item) { 
    return /^\t+\-/.test(item);
}

// Check if it is Unordered list
function splitEnter(val) {
    return val.replace(/\r/g, "").split(/\n/);
}

// return the number after "*", display element multiple times
function countMulti(item) {
    if (/\*\d+$/.test(item)) {
        return parseInt(item.match(/\d+$/));
    }
    else
        return 1;
}

//get class and ID after comma
function getClassID(item) {
    if (/\./.test(item)) {
        return item.match(/\,([^]+)\./)[1];
    }
    else return item.match(/\,([^]+)/)[1];
}

//get content between two double quote
function getContent(item) {
    if(item.match(/"([^]+)"/)[1])
        return item.match(/"([^]+)"/)[1];
    else return lorem
}

//get content between two double quote
function getHTMLContent(item) {
    if(item.match(/(.+)*--/)[1])
        return item.match(/(.+)*--/)[1];
    else return loremShort;
}

let cusComponent = 0;
function convertToHTML(item, type) {

    // CHECK IF IT IS CUSTOM COMPONENT
    if(/^i-\w+/.test(item)){ // START CUSTOM COMPONENT
        cusComponent = 1;
        closeTags.push("");
        return "";
    }else if(/^i-\/$/.test(item) && cusComponent == 1){ // END CUSTOM COMPONENT
        cusComponent = 0;
        closeTags.push("");
        return "";
    }else if(cusComponent == 1) { // CUSTOM COMPONENT CONTENT
        cusComponent = 1;
        closeTags.push("");
        return item;
    }
    
    let content = "";
    let classID = "";
    let cdn = document.getElementById("cdn-input").value;

    if (/\.\"/.test(item)) { //check if there's a content. (e.g. h1,page-title."Page Title")
        content = getContent(item);
        item = item.replace(/\."([^]+)"/, "");
    }
    else if(/\"\./.test(item)){ //check if there's a content. (e.g. "Page Title".h1,page-title)
        content = getContent(item);
        item = item.replace(/"([^]+)"\./, "");
    }
    else if(/"([^]+)"/.test(item)){ //check if there's a content. (e.g. "Page Title"h1,page-title)
        content = getContent(item);
        item = item.replace(/"([^]+)"/, "");
    }
    else if(/(.+)*--/.test(item)){ //check if there's a content. (e.g. "Page Title"h1,page-title)
        content = getHTMLContent(item);
        item = item.replace(/(.+)*--/, "");
    }
    if (/\,/.test(item)) { // check if there's a class or an ID
        classID = getClassID(item).toLowerCase();
        if (/\./.test(item)) {
            item = item.replace(/\,([^]+)\./, "");
        }
        else item = item.replace(/\,([^]+)/, "");
    }
  
    // if the first character is number with "."
    // Input: 1.Home
    // Output: <!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Home</title></head><body></body></html>
    if (item.match(/^page\./)) {
        var charAfterNumber = item.replace(/^page\./, ""); // getting all characters after number with "."
        var menuString = '';

        // Generate Navigation on every page If enabled
        if (document.getElementById('auto-gen').checked){
            if (menus.length > 0){
                menuString = '<div class="nav">';
                for (var m = 0; m < menus.length; m++){
                    menus[m] = menus[m].replace(/^\d\./, "");
                    if(menus[m] == "Home" || menus[m] == "home" || menus[m] == "Homepage" || menus[m] == "homepage"){
                        menuString += '<a href="/">'+menus[m]+'</a>';
                    }else{
                        menuString += '<a href="/'+menus[m].toLowerCase()+'">'+menus[m]+'</a>';
                    }
                }
                menuString += '</div>';
            }
        }
        var blurLorem = '';
        if(document.getElementById('blur-lorem').checked){
            blurLorem = '.lorem{color: transparent;text-shadow: 0 0 5px rgb(0 0 0);}';
        }
        if (type == "html") {
            closeTags.push("</body></html>");
            return `<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>` + charAfterNumber + `</title>` + cdn + styleTag + `</head><style>`+blurLorem+` svg.icon{color:rgb(0,0,0);width:32px;height:32px;}</style><body>`+menuString;
        }
        else if (type == "vue") {
            closeTags.push("</div><script src=\"https://unpkg.com/vue@next\"></script><script>const app = Vue.createApp({}).mount('.app')</script></body></html>");
            return `<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui"><title>` + charAfterNumber + `</title>` + cdn + styleTag + `</head><style>`+blurLorem+` svg.icon{color:rgb(0,0,0);width:32px;height:32px;}</style><body><div class="app">`+menuString;
        }
        else if (type == "vue-template") {
            let closing = "</div>" + vueTags["template"][1];
            closeTags.push(closing);
            return vueTags["template"][0]+"<div id=\""+charAfterNumber.toLowerCase()+"\" class=\""+charAfterNumber.toLowerCase()+"\">";
        }
    }

    // function that will convert input to uncheck checkbox
    // Input: checkbox
    // Output: <input type="checkbox">
    else if (/^c$/.test(item) || /^cbx$/.test(item) || /^checkbox$/.test(item)) {
        closeTags.push(tags["uncheck"][1]);
        return tags["uncheck"][0] + content;
    }

    // function that will convert input to unselected radio button
    // Input: radio
    // Output: <input type="radio">
    else if (/^r$/.test(item) || /^rdo$/.test(item) || /^radio$/.test(item)) {
        closeTags.push(tags["radio"][1]);
        return tags["radio"][0] + content;
    }

    // function that will convert input to progress bar by percentage (e.g. [8])
    // Input: [8]
    // Output: <progress id="" class="" value="8" max="100"></progress>
    else if (/\[\s*\d+%?\s*\]/.test(item)) {
        let char = item.replace(/\[\s*\d+%?\s*\]/, "");
        let value = item.substring(item.lastIndexOf("\[") + 1, item.lastIndexOf("\]"));
        let values = value.match(/\d+/g);
        closeTags.push(tags["progress"][1]);
        return "<progress id=\"" + classID + "\" class=\"" + classID + "\" value=\"" + values + "\" max=\"100\">" + content;
    }

    // function that will convert input to progress bar by value and maximum number (e.g. [8&10])
    // Input: [8&10]
    // Output: <progress value="8" max="10"></progress>
    else if (/^\[\s*\d+\s*\&\s*\d+\s*\]/.test(item)) {
        let char = item.replace(/\[\s*\d+\s*\&\s*\d+\s*\]/, "");
        let value = item.substring(item.lastIndexOf("\[") + 1, item.lastIndexOf("\]"));
        let values = value.match(/\d+/g);
        let percentage = values[0] * 100 / values[1];
        closeTags.push(tags["progress"][1]);
        return "<progress value=\"" + values[0] + "\" max=\"" + values[1] + "\">" + content;
    }

    // Abbreviations
    // for creating div for button (e.g. btn,custom-btn."Click Here!")
    // Input: btn
    // Output: <button id="" class="button "></button>
    // Input: btn,custom-btn."Click Here!"
    // Output: <button id="custom-btn" class="button custom-btn">Click Here!</button>
    else if (/^b-i$/.test(item) || /^btn$/.test(item) || /^button$/.test(item)) {
        closeTags.push(tags["button"][1]);
        return "<button id=\"" + classID + "\" class=\"button " + classID + "\">" + content;
    }

    // for creating div for navigation  (e.g. nav,custom-nav)
    // Input: nav
    // Output: <div id="" class="nav "></div>
    // Input: nav,custom-nav
    // Output: <div id="custom-nav" class="nav custom-nav"></div>
    else if (/^n$/.test(item) || /^n-i$/.test(item) || /^nav$/.test(item) || /^navigation$/.test(item)) {
        closeTags.push(tags["nav"][1]);
        return "<div id=\"" + classID + "\" class=\"nav " + classID + "\">" + content;
    }

    // for creating div for section (e.g. sec,custom-section)
    // Input: sec
    // Output: <div id="" class="section "></div>
    // Input: sec,custom-section
    // Output: <div id="custom-section" class="section custom-section"></div>
    else if (/^s$/.test(item) || /^s-i$/.test(item) || /^sec$/.test(item) || /^section$/.test(item)) {
        closeTags.push(tags["section"][1]);
        return "<div id=\"" + classID + "\" class=\"section " + classID + "\">" + content;
    }

    // for creating div for footer (e.g. footer,main-footer)
    // Input: fut
    // Output: <div id="" class="footer "></div>
    // Input: footer,main-footer
    // Output: <div id="main-footer" class="footer main-footer"></div>
    else if (/^f$/.test(item) || /^f-i$/.test(item) || /^fut$/.test(item) || /^footer$/.test(item)) { 
        closeTags.push(tags["footer"][1]);
        return "<div id=\"" + classID + "\" class=\"footer " + classID + "\">" + content;
    }

    // for creating div for text (e.g. div,custom-wrapper)
    // Input: div
    // Output: <div id="" class=" "></div>
    // Input: div,custom-wrapper
    // Output: <div id="custom-wrapper" class="custom-wrapper"></div>
    else if (/^dv$/.test(item) || /^d-i$/.test(item) || /^div$/.test(item)) { 
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"" + classID + "\">" + content;
    }

    // for creating wrapper div for text (e.g. wdv,custom-nav
    // Input: wdv
    // Output: <div id="" class="wrapper "></div>
    // Input: wdv,custom-wrapper
    // Output: <div id="custom-wrapper" class="wrapper custom-wrapper"></div>
    else if (/^w$/.test(item) || /^w-i$/.test(item) || /^wdv$/.test(item) || /^_$/.test(item) || /^wrapper$/.test(item)) {
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"wrapper " + classID + "\">"+ content;
    }

    // for creating row div for text (e.g. row,custom-row)
    // Input: row
    // Output: <div id="" class="row "></div>
    // Input: row,custom-row
    // Output: <div id="custom-row" class="row custom-row"></div>
    else if (/^r$/.test(item) || /^r-i$/.test(item) || /^rw$/.test(item) || /^row$/.test(item)) {
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"row " + classID + "\">"+ content;
    }

    // for creating video div for text (e.g. video,custom-video)
    // Input: video
    // Output: <video id="" class="video " width="320" height="240" controls></video>
    // Input: video,custom-video
    // Output: <video id="custom-video" class="video custom-video" width="320" height="240" controls></video>
    else if (/^v$/.test(item) || /^v-i$/.test(item) || /^vid$/.test(item) || /^video$/.test(item)) {
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"video " + classID + "\">"+ videoIcon;
    }

    // for creating tel div for text (e.g. tel,custom-tel."2025550182")
    // Input: tel."2025550182"
    // Output: <a id="" class="tel " href="tel:2025550182"><svg>...</a>
    // Input: tel,custom-tel."2025550182"
    // Output: <a id="custom-tel" class="tel custom-tel" href="tel:2025550182"><svg>...</a>
    else if (/^tl$/.test(item) || /^tel$/.test(item)) {
        closeTags.push(tags[">"][1]);
        return "<a id=\"" + classID + "\" class=\"tel " + classID + "\" href=\"tel:"+ content +"\">"+ phoneIcon;
    }

    // for creating tel div for text (e.g. email,custom-email."example@mail.com")
    // Input: email."example@mail.com"
    // Output: <a id="" class="email " href="mailto:example@mail.com"><svg>...</a>
    // Input: email,custom-email."example@mail.com"
    // Output: <a id="custom-tel" class="email custom-email" href="mailto:example@mail.com"><svg>...</a>
    else if (/^email$/.test(item)) {
        closeTags.push(tags[">"][1]);
        return '<a id="' + classID + '" class="email ' + classID +'" href="mailto:'+ content +'">'+ mailIcon;
    }

    // for creating div for image (e.g. pic,custom-class)
    // Input: pic
    // Output: <div class=\"image\"><img></div>
    else if (item == "i-i" || item == "img" || item == "image" || item == "p-i" || item == "pic" || item == "picture") {
        closeTags.push(tags["img"][1]);
        return tags["img"][0] + imageIcon;
    }

    // for creating table (e.g. table2 5,custom-table)
    // Input: table2 2
    // Output: <div class="table"><div class="tr"><div class="td"></div><div class="td"></div></div><div class="tr"><div class="td"></div><div class="td"></div></div></div>
    // Input: table2 2,custom-table
    // Output: <div id+"custom-table" class="table custom-table"><div class="tr"><div class="td"></div><div class="td"></div></div><div class="tr"><div class="td"></div><div class="td"></div></div></div>
    else if (/^table\d\s+\d$/.test(item) || /^table$/.test(item) || /^tbl\d\s+\d$/.test(item) || /^tbl$/.test(item)) {
        let convertedStr = "<div class=\"table\">";
        if (/^table\d\s+\d$/.test(item)) {
            let cr = /^table(\d\s+\d)/.exec(item);
            let params = cr[1].split(" ");
            for (let x = 0; x < params[0]; x++) {
                convertedStr += "<div class=\"tr\">";
                for (let y = 0; y < params[1]; y++) {
                    convertedStr += "<div class=\"td\"></div>";
                    if (y == params[1] - 1) {
                        convertedStr += "</div>";
                    }
                }
            }
        }
        closeTags.push("</div>");
        return convertedStr;
    }

    // for creating gallery (e.g. gallery2 5,custom-class)
    // Input: gallery2 2
    // Output: <div class="gallery"><div class="image col-2"><img></div><div class="image col-2"><img></div><div class="image col-2"><img></div><div class="image col-2"><img></div></div>
    else if (/^gallery\d\s+\d$/.test(item) || /^gallery$/.test(item)) {
        let convertedStr = "<div class=\"gallery\">";
        if (/^gallery\d\s+\d$/.test(item)) {
            let cr = /^gallery(\d\s+\d)/.exec(item);
            let params = cr[1].split(" ");
            let numberOfImages = params[0] * params[1];
            for (let x = 0; x < numberOfImages; x++) {
                convertedStr += "<div class=\"image col-" + params[0] + "\"><img></div>";
            }
        }
        closeTags.push("</div>");
        return convertedStr;
    }

    // for creating element for Unordered List
    // Input: ul
    // Output: <ul></ul>
    else if (item == "ul") { 
        closeTags.push(tags["ul"][1]);
        return tags["ul"][0];
    }

    // for creating element for Ordered List
    // Input: ol
    // Output: <ol></ol>
    else if (item == "ol") {
        closeTags.push(tags["ol"][1]);
        return tags["ol"][0];
    }

    // for creating element for List
    // Input: li
    // Output: <li></li>
    else if (/^li\-/.test(item)) { 
        item = item.replace(/^li\-/, "");
        closeTags.push(tags["li"][1]);
        return tags["li"][0] + item + content;
    }

    // for creating nav item; menu text>menu link (e.g. About Us>about-us)
    // Input: About Us>about-us
    // Output: <a href="/about-us">About Us</a>
    else if (/>/.test(item)) { //if it is link

        // Input: contact>..
        // Output: <a href="/contact">contact</a>
        var link = item.split(">");
        if (link[1] == "..") { //if the char after greater than sign is double dots
            link[1] = link[0];
        }

        if (link[0] == "") link[0] = loremShort;

        if (link[1] == "") link[1] = "";

        // Link with logo
        // Input: logo>home
        // Output: <a href="/home"><div class="logo"></div></a>
        if (link[0] == "logo") {
            closeTags.push("</div></a>");
            return "<a href=\"/" + link[1] + "\"><div class=\"logo\">";
        }
        else if (link[1] == "#") {
            closeTags.push("</a>");
            return "<a href=\"" + link[1] + "\">" + link[0];
        } else {
            closeTags.push("</a>");
            return "<a href=\"" + link[1] + ".html\">" + link[0];
        }
    }

    else if (/[1-6]$/.test(item) || /^h[1-6]$/.test(item)) { //if the last char is digit
        var lastDigit = item.match(/\d$/); // get the last number in a string

        if (content == "") content = loremShort;

        if (/^column-\d$/g.test(item) || /^col\d$/g.test(item) || /^c\d$/g.test(item) || /^col-\d$/g.test(item) || /^c-\d$/g.test(item)) {
            closeTags.push("</div>");
            return "<div class=\"col-" + lastDigit + " " + classID + "\">" + content;
        }

        else if (/^h[1-6]$/.test(item)) {
            closeTags.push(tags[lastDigit][1]);
            return "<h" + lastDigit + " id=\"" + classID + "\" class=\"" + classID + "\">" + content;
        }
        else {
            closeTags.push(tags["div"][1]);
            return "<div id=\"" + classID + "\" class=\"" + classID + "\">" + item;
        }
    }

    else if (item == "hr") { //for creating separator
        closeTags.push(tags["hr"][1]);
        return tags["hr"][0];
    }

    else if (item == "inp" || item == "input") { // for creating input div
        closeTags.push(tags["input"][1]);
        return "<div id=\"" + classID + "\" class=\"input " + classID + "\">" + content;
    }

    else if (item == "lbl" || item == "label") { // for creating label
        if (content == "") content = loremShort;
        closeTags.push(tags["lbl"][1]);
        return "<label id=\"" + classID + "\" class=\"" + classID + "\">" + content;
    }

    else if (item == "fm" || item == "frm" || item == "form") { // for creating form
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"" + classID + "\">"+ content;
    }

    else if (item == "sp" || item == "spn" || item == "span") { // for creating span
        closeTags.push(tags["span"][1]);
        return "<span id=\"" + classID + "\" class=\"" + classID + "\">" + content;
    }

    else if (item == "select" || item == "dd") { // for creating select dropdown
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"select " + classID + "\">" + content;
    }

    else if (item == "option" || item == "dd#" || item == "d#") { // for creating option dropdown
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"option " + classID + "\">" + content;
    }

    else if (item == "contact form" || item == "ctf") { // for creating contact us form
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"contact-form " + classID + "\">"+ content +"<div class=\"name\"></div><div class=\"email\"></div><div class=\"subject\"></div><div class=\"message\"></div>";
    }

    else if (item == "register") { // for creating Register Form
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"register " + classID + "\">"+ content +"<div class=\"name\"></div><div class=\"email\"></div><div class=\"password\"></div><div class=\"confrim-password\"></div>";
    }

    else if (item == "login") { // for creating Login Form
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"login " + classID + "\">"+ content +"<div class=\"email\"></div><div class=\"password\"></div>";
    }

    else if (item == "chatbot" || item == "chat" || item == "cb") { // for creating Chat Bot
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"chatbot " + classID + "\"><div class=\"chatbox\"></div><button class=\"chat-button\">"+ content +"</button>";
    }

    else if (item == "card") { // for creating Chat Bot
        closeTags.push(tags["div"][1]);
        return "<div id=\"" + classID + "\" class=\"card " + classID + "\"><div class=\"card-img\"></div><div class=\"card-content\">"+ content +"</div>";
    }

    else if(item == "txt" || item == "t"){ 
        closeTags.push("</div>");
        // 
        if (document.getElementById('auto-lorem').checked){
            return '<div id="' + classID + '" class="lorem ' + classID +'" "'+ item +'">'+ loremLong;
        }else{
            return '<div id="' + classID + '" class="' + classID +'" "'+ item +'">'+ content;
        }
    }

    else { //normal text
        closeTags.push("</div>");
        return "<div id=\"" + classID + "\" class=\"" + classID +" "+ item +"\">"+ content;
    }
}

// Copy the text inside the input textarea
document.getElementById("copy-input").addEventListener("click", () => {
    
    var copyText = document.getElementById("form-input");
  
    // Select the text field
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
  
    // Copy the text inside the input textarea
    document.execCommand("copy");
});

// Copy the text inside the output textarea
document.getElementById("copy-output").addEventListener("click", () => {
    
    var copyText = document.getElementById("output");
  
    // Select the text field
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
  
    // Copy the text inside the output textarea
    document.execCommand("copy");
});

// Save output to text file
document.getElementById("export").addEventListener("click", saveFile);

function saveFile() {
    const msg = document.getElementById('output');
    
    // This variable stores all the data.
    let data = msg.value;
    
    // Convert the text to BLOB.
    const textToBLOB = new Blob([data], { type: 'text/plain' });
    const sFileName = 'output.txt';	   // The file to save the data.

    let newLink = document.createElement("a");
    newLink.download = sFileName;

    if (window.webkitURL != null) {
        newLink.href = window.webkitURL.createObjectURL(textToBLOB);
    }
    else {
        newLink.href = window.URL.createObjectURL(textToBLOB);
        newLink.style.display = "none";
        document.body.appendChild(newLink);
    }

    newLink.click(); 
}



// variables for uploading text file for input

let fileUploaded = document.querySelector('#file-input');

fileUploaded.addEventListener('change', () => {
    let files = fileUploaded.files;
    const file = files[0];

    let reader = new FileReader();

    let fileName = file.name;

    if(document.getElementById("file-input").value==="") { 
        document.getElementById('submit').disabled = true; 
    } else { 
        document.getElementById('submit').disabled = false;
    }

    document.getElementById('file-input-name').innerText = fileName;

    reader.onload = (e) => {
        document.getElementById('form-input').value = e.target.result;
    }

    reader.onerror = (e) => alert(e.target.error.name);

    reader.readAsText(file);
});

// variables for uploading CSS file
let cssFileUploaded = document.querySelector('#css-input');

let cssFiles = [];

let cssFilesList = "";

cssFileUploaded.addEventListener('change', () => {

    // let files = cssFileUploaded.files;

    // if (files.length == 0) return;

    // for (let index = 0; index < files.length; index++){
    //     const file = files[index];

    //     let reader = new FileReader();

    //     let fileInput = "";

    //     let fileName = file.name;

    //     fileName = fileName.replace(".css", "");

    //     reader.onload = (e) => {
    //         fileInput = e.target.result;
    //     }

    //     cssFiles.push({id: index, fileName: fileName, fileContent: fileInput});

    //     reader.onerror = (e) => alert(e.target.error.name);

    //     reader.readAsText(file);

    //     cssFilesList += "<li class=\"css-file-name\">"+ file.name +"<span class=\"clear remove-css\" onclick=\"removeFile("+ index +", this)\">x</span></li>";

    //     document.querySelector('#css-files').innerHTML = cssFilesList;
    // }
    /* Dragable function to make it enable use drag-sort-enable */
});

function removeFile(index, input){
    input.parentNode.remove()
    cssFiles.splice(index, 1);
}

// CLEAR FILE INPUT
document.getElementById("clear-file-input").addEventListener("click", () => {
    document.getElementById("file-input").value = null;
    document.getElementById("file-input-name").innerText = "No uploaded file";
    document.getElementById("form-input").value = "";
});

// LIST OF UPLOADED CSS FILE
updateList = function() {
    var input = document.getElementById('file');
    var output = document.getElementById('fileList');
    var children = "";
    for (var i = 0; i < input.files.length; ++i) {
        children += '<li>' + input.files.item(i).name + '</li>';
    }
    output.innerHTML = '<ul>'+children+'</ul>';
}

showHideModal("css-modal", "add-css");

function showHideModal(myModal, myBtn){
    // Get the modal
    let modal = document.getElementById(myModal);

    // Get the button that opens the modal
    let btn = document.getElementById(myBtn);

    // Get the <span> element that closes the modal
    let closeModal = modal.getElementsByClassName("close")[0];
    let closeModalBtn = modal.getElementsByClassName("close-modal")[0];

    // When the user clicks on the button, open the modal
    btn.onclick = function () {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    closeModal.onclick = function () {
        modal.style.display = "none";
    }
    closeModalBtn.onclick = function () {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

// DRAG & DROP FUNCTION
let dropArea = document.getElementById('drop-area');

;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
});
  
function preventDefaults (e) {
    e.preventDefault();
    e.stopPropagation();
}
;['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

;['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropArea.classList.add('highlight');
}

function unhighlight(e) {
    dropArea.classList.remove('highlight');
}

dropArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
  let dt = e.dataTransfer;
  let files = dt.files;

  handleFiles(files);
}
function handleFiles(files) {
    files = [...files];
    // initializeProgress(files.length);
    files.forEach(uploadFile);
    files.forEach(previewFile);
    
    function enableDragSort(listClass) {
        const sortableLists = document.getElementsByClassName(listClass);
        Array.prototype.map.call(sortableLists, (list) => {enableDragList(list)});
    }

    function enableDragList(list) {
        Array.prototype.map.call(list.children, (item) => {enableDragItem(item)});
    }

    function enableDragItem(item) {
        item.setAttribute('draggable', true)
        item.ondrag = handleDrag;
        item.ondragend = handleDrop;
    }

    function handleDrag(item) {
        const selectedItem = item.target,
        list = selectedItem.parentNode,
        x = event.clientX,
        y = event.clientY;

        selectedItem.classList.add('drag-sort-active');
        let swapItem = document.elementFromPoint(x, y) === null ? selectedItem : document.elementFromPoint(x, y);

        if (list === swapItem.parentNode) {
            swapItem = swapItem !== selectedItem.nextSibling ? swapItem : swapItem.nextSibling;
            list.insertBefore(selectedItem, swapItem);
        }
    }

    function handleDrop(item) {
        item.target.classList.remove('drag-sort-active');
    }

    (()=> {enableDragSort('drag-sort-enable')})();
}
  
function uploadFile(file, i) { // <- Add `i` parameter
    var url = 'YOUR URL HERE';
    var xhr = new XMLHttpRequest();
    var formData = new FormData();
    xhr.open('POST', url, true);
  
    // xhr.upload.addEventListener("progress", function(e) {
    //   updateProgress(i, (e.loaded * 100.0 / e.total) || 100);
    // })
  
    xhr.addEventListener('readystatechange', function(e) {
      if (xhr.readyState == 4 && xhr.status == 200) {
        // Done. Inform the user
      }
      else if (xhr.readyState == 4 && xhr.status != 200) {
        // Error. Inform the user
      }
    })
  
    formData.append('file', file);
    xhr.send(formData);
}
  
  
let index = 0;

function previewFile(file) {
    let reader = new FileReader();
    document.getElementById('no-file').innerText = "";
    reader.readAsDataURL(file);
    reader.onloadend = function() {
      let css = "<li class=\"css-file-name\" draggable=\"true\">"+ file.name +"<span class=\"clear remove-css\" onclick=\"removeFile("+ index++ +", this)\">x</span></li>";
      // css.src = reader.result;
      document.getElementById('css-files').innerHTML += css;
    };
}

// let filesDone = 0;
// let filesToDo = 0;
// let uploadProgress = [];

// let progressBar = document.getElementById('progress-bar');

// function initializeProgress(numFiles) {
//     progressBar.value = 0;
//     uploadProgress = [];
  
//     for(let i = numFiles; i > 0; i--) {
//       uploadProgress.push(0);
//     }
// }

// function updateProgress(fileNumber, percent) {
//     uploadProgress[fileNumber] = percent;
//     let total = uploadProgress.reduce((tot, curr) => tot + curr, 0) / uploadProgress.length;
//     progressBar.value = total;
// }