const ua = window.navigator.userAgent;
const isIE = /MSIE|Trident/.test(ua);

// Check if the browser is Internet Explorer
if (isIE) {
    alert("Internet Explorer is not a supported browser\n\nPlease use any of the following:\nGoogle Chrome\nMozilla Firefox\nMicrosoft Edge"); // Use standard JS alert (IE11 may not support Bootstrap alerts)
}
else { // Enable main toolbar/interface buttons
    document.getElementById('btn_filter').removeAttribute('disabled');
    document.getElementById('btn_import').removeAttribute('disabled');
    document.getElementById('pac-input').removeAttribute('disabled');
    document.getElementById('btn_analyse').removeAttribute('disabled');
}

function ShowLoading() {
    // Remove hidden class?
    LoadingSymbol = document.getElementById("loading_symbol");
    LoadingSymbol.style.left = "calc(50% - 50px)";
    LoadingSymbol.style.top = "calc(50% - 50px)";
    LoadingSymbol.style.display = "block";
}

function HideLoading() {
    // Add hidden class?
    LoadingSymbol = document.getElementById("loading_symbol");
    LoadingSymbol.style.left = "-500px";
    LoadingSymbol.style.top = "-500px";
    LoadingSymbol.style.display = "none";
}

ShowLoading();

function AddOptions(select, options) { /* Add parameter options to parameter select */
    options.forEach(option => {
        const el = document.createElement("option");
        el.textContent = option;
        el.value = option;
        select.appendChild(el);
    })
}

function AddLocationOptions(options) {
    const filter_loc = document.getElementById("Filter_Location");
    options.forEach(option => {
        const el = document.createElement("option");
        el.textContent = `Within ${option.text} miles`;
        el.value = option.value;
        filter_loc.appendChild(el);
    })
}

// Main category select elements
const add_select = document.getElementById("Add_Crime_Type");
const filter_select = document.getElementById("Filter_Crime_Type");
const edit_select = document.getElementById("Edit_Crime_Type");

const filter_loc = document.getElementById("Filter_Location");

// Add the needed options to the relevant select elements
AddOptions(filter_loc, all_option);
AddOptions(filter_select, all_option);

AddOptions(add_select, main_options);
AddOptions(filter_select, main_options);
AddOptions(edit_select, main_options);

AddLocationOptions(locMappings);

// Subcategory select elements
const add_sub_select = document.getElementById("Add_Crime_Type_sub");
const filter_sub_select = document.getElementById("Filter_Crime_Type_sub");
const edit_sub_select = document.getElementById("Edit_Crime_Type_sub");

// Updating the available options for the sub-dropdowns when the main dropdown is changed
document.getElementById('Add_Crime_Type').addEventListener("change", (event) => { // When main category selected
    document.querySelectorAll('#Add_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove()); // Remove all but the default hidden value

    const foundMappingAddChange = crimeTypeMappings.find(x => x.value == event.target.value); // Find which array of options need to now be presented
    if (foundMappingAddChange) {
        AddOptions(add_sub_select, foundMappingAddChange.options); // Add those options
    }
});

document.getElementById('Filter_Crime_Type').addEventListener("change", () => {
    document.querySelectorAll('#Filter_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());
    AddOptions(filter_sub_select, all_option);

    const foundMappingFilterChange = crimeTypeMappings.find(x => x.value == event.target.value);
    if (foundMappingFilterChange) {
        AddOptions(filter_sub_select, foundMappingFilterChange.options);
    }
});

document.getElementById('Edit_Crime_Type').addEventListener("change", () => {
    document.querySelectorAll('#Edit_Crime_Type_sub option:not(:first-child)').forEach(el => el.remove());

    const foundMappingEditChange = crimeTypeMappings.find(x => x.value == event.target.value);
    if (foundMappingEditChange) {
        AddOptions(edit_sub_select, foundMappingEditChange.options);
    }
});