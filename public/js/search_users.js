document.addEventListener('DOMContentLoaded', function() {
    var usernameInput = document.getElementById('add_existing_player_username');
    var autocompleteResults = document.createElement('ul');
    autocompleteResults.id = 'autocomplete-results';
    autocompleteResults.style.position = 'absolute';
    autocompleteResults.style.border = '1px solid #000';
    autocompleteResults.style.backgroundColor = '#fff';
    autocompleteResults.style.listStyleType = 'none';
    autocompleteResults.style.padding = '0';
    autocompleteResults.style.marginTop = '2px';
    autocompleteResults.style.zIndex = '100';
    autocompleteResults.style.width = usernameInput.offsetWidth + 'px';
    usernameInput.parentNode.appendChild(autocompleteResults);

    function clearAutocompleteResults() {
        autocompleteResults.innerHTML = '';
    }

    function addAutocompleteResult(username) {
        var listItem = document.createElement('li');
        listItem.style.padding = '0.5rem';
        listItem.style.cursor = 'pointer';
        listItem.textContent = username;
        listItem.onclick = function() {
            usernameInput.value = username;
            clearAutocompleteResults();
        };
        autocompleteResults.appendChild(listItem);
    }

    usernameInput.addEventListener('input', function() {
        var searchTerm = this.value;

        if(searchTerm.length >= 2) {
            fetch(`/search/users?term=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(usernames => {
                    clearAutocompleteResults();
                    usernames.forEach(addAutocompleteResult);
                });
        } else {
            clearAutocompleteResults();
        }
    });

    document.addEventListener('click', function(event) {
        if (!usernameInput.contains(event.target)) {
            clearAutocompleteResults();
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // Sélectionne uniquement les champs d'input dans le formulaire spécifique
    var form = document.getElementById('add-existing-player-form');
    var inputs = form.querySelectorAll('input[type="text"]');

    inputs.forEach(function(input) {
        input.value = ''; 
    });

});